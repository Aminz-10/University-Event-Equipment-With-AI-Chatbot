<?php
/**
 * UniEquip OpenRouter AI Chatbot API
 * TypeScript-style implementation with extended reasoning via OpenRouter
 * Uses Grok-4.1 model with reasoning_details preservation
 */

session_start();
include "db.php";
include "ai_openrouter_config.php";

header('Content-Type: application/json');

// Debug logging
if (AI_DEBUG_MODE) {
    error_log('=== CHATBOT API DEBUG ===');
    error_log('Session user_number: ' . (isset($_SESSION['user_number']) ? $_SESSION['user_number'] : 'NOT SET'));
    error_log('POST message: ' . (isset($_POST['message']) ? $_POST['message'] : 'NOT SET'));
    error_log('Is OpenRouter configured: ' . (is_openrouter_configured() ? 'YES' : 'NO'));
    error_log('DB Connect set: ' . (isset($connect) ? 'YES' : 'NO'));
}

// Check if user is logged in
if (!isset($_SESSION['user_number'])) {
    echo json_encode(['error' => 'Unauthorized access', 'message' => 'Please login first']);
    http_response_code(401);
    exit();
}

// Get the user's question
$input = isset($_POST['message']) ? trim($_POST['message']) : '';
$user_num = $_SESSION['user_number'];
$user_role = $_SESSION['role'] ?? 'student';

if (empty($input)) {
    echo json_encode(['error' => 'Empty message', 'message' => 'Please provide a question']);
    exit();
}

// AI Response Handler with TypeScript-style reasoning
class EquipmentAIWithReasoning {
    private $connect;
    private $user_role;
    private $user_num;
    private $use_openrouter;
    private $conversation_history = [];

    public function __construct($conn, $role, $num) {
        $this->connect = $conn;
        $this->user_role = $role;
        $this->user_num = $num;
        $this->use_openrouter = is_openrouter_configured();
        
        // Load conversation history from session
        if (isset($_SESSION['ai_history'])) {
            $this->conversation_history = $_SESSION['ai_history'];
        }
    }

    /**
     * Process user input and generate appropriate response
     * Maintains reasoning_details across conversation turns
     */
    public function processQuery($input) {
        try {
            $input_lower = strtolower($input);
            
            // Detect query intent - prioritize database queries for speed
            if ($this->isBookingGuideQuestion($input_lower)) {
                return $this->handleBookingGuide();
            } elseif ($this->isAvailabilityQuestion($input_lower)) {
                return $this->handleAvailabilityQuery($input);
            } elseif ($this->isCategoryQuestion($input_lower)) {
                return $this->handleCategoryQuery($input);
            } elseif ($this->isBookingStatusQuestion($input_lower)) {
                return $this->handleBookingStatusQuery($input);
            } elseif ($this->isQuantityQuestion($input_lower)) {
                return $this->handleQuantityQuery($input);
            } elseif ($this->isModelQuestion($input_lower)) {
                return $this->handleModelQuery($input);
            } elseif ($this->isMyBookingsQuestion($input_lower)) {
                return $this->handleMyBookingsQuery();
            } elseif ($this->isAllEquipmentQuestion($input_lower)) {
                return $this->handleAllEquipmentQuery();
            } else {
                // For random/general questions, use OpenRouter with reasoning if available
                if ($this->use_openrouter) {
                    log_ai_operation("Processing question with OpenRouter (Grok-4.1 with reasoning): $input");
                    return $this->queryOpenRouterWithReasoning($input);
                } else {
                    log_ai_operation("Processing general question with local AI: $input");
                    return $this->getSmartFallback($input);
                }
            }
        } catch (Exception $e) {
            if (AI_DEBUG_MODE) {
                error_log('processQuery exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine());
            }
            return $this->formatResponse('An error occurred processing your request. Please try again.', 'error');
        }
    }

    /**
     * Query OpenRouter API with reasoning/extended thinking
     * TypeScript equivalent:
     * 
     * let response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
     *   method: "POST",
     *   headers: {
     *     "Authorization": `Bearer ${<OPENROUTER_API_KEY>}`,
     *     "Content-Type": "application/json"
     *   },
     *   body: JSON.stringify({
     *     "model": "x-ai/grok-4.1-fast:free",
     *     "messages": [...],
     *     "reasoning": {"enabled": true}
     *   })
     * });
     */
    private function queryOpenRouterWithReasoning($user_question) {
        try {
            // Build system context
            $system_context = $this->buildSystemContext();
            
            // Initialize messages array with system context
            $messages = [
                [
                    'role' => 'system',
                    'content' => $system_context
                ]
            ];
            
            // Add conversation history if preserving reasoning
            if (AI_PRESERVE_REASONING && count($this->conversation_history) > 0) {
                // Maintain last N messages with reasoning details
                $history_limit = min(AI_HISTORY_LENGTH, count($this->conversation_history));
                $start_idx = count($this->conversation_history) - $history_limit;
                
                for ($i = $start_idx; $i < count($this->conversation_history); $i++) {
                    $msg = $this->conversation_history[$i];
                    
                    // Preserve reasoning_details from previous responses
                    if ($msg['role'] === 'assistant' && isset($msg['reasoning_details'])) {
                        $messages[] = [
                            'role' => 'assistant',
                            'content' => $msg['content'],
                            'reasoning_details' => $msg['reasoning_details']
                        ];
                    } else {
                        $messages[] = $msg;
                    }
                }
            }
            
            // Add current user message
            $messages[] = [
                'role' => 'user',
                'content' => $user_question
            ];
            
            // Build request data with reasoning configuration
            $request_data = [
                'model' => OPENROUTER_MODEL,
                'messages' => $messages,
                'temperature' => AI_TEMPERATURE,
                'max_tokens' => AI_MAX_TOKENS,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0
            ];
            
            // Add reasoning configuration if enabled
            if (ENABLE_REASONING) {
                $request_data['reasoning'] = [
                    'enabled' => true,
                    'type' => REASONING_CONFIG
                ];
                
                // Add max_reasoning_tokens for models that support it
                if (defined('AI_MAX_REASONING_TOKENS') && AI_MAX_REASONING_TOKENS > 0) {
                    $request_data['max_reasoning_tokens'] = AI_MAX_REASONING_TOKENS;
                }
            }
            
            // Call OpenRouter API
            $response = $this->callOpenRouterAPI($request_data);
            
            if ($response && isset($response['choices'][0]['message']['content'])) {
                $message = $response['choices'][0]['message'];
                $ai_response = $message['content'];
                
                // Extract reasoning details if present
                $reasoning_details = $message['reasoning_details'] ?? null;
                
                // Log token usage
                if (isset($response['usage'])) {
                    log_ai_operation("Token usage: " . format_token_info($response['usage']));
                }
                
                // Log reasoning details if present
                if ($reasoning_details && AI_LOG_REASONING) {
                    log_reasoning_details($reasoning_details, $user_question);
                }
                
                // Update conversation history
                $this->addToHistory('user', $user_question);
                $this->addToHistory('assistant', $ai_response, $reasoning_details);
                
                log_ai_operation("OpenRouter response generated successfully");
                return $this->formatResponse($ai_response, 'success', $reasoning_details);
            } else {
                // Fallback if OpenRouter fails
                log_ai_operation("OpenRouter returned no valid response, using fallback", 'warning');
                if (AI_FALLBACK_ENABLED) {
                    return $this->getSmartFallback($user_question);
                } else {
                    return $this->formatResponse("Unable to process your request at this time. Please try again.", 'error');
                }
            }
        } catch (Exception $e) {
            // Log error and fallback to local response
            log_ai_operation("OpenRouter API Error: " . $e->getMessage(), 'error');
            
            if (AI_LOG_FAILURES && AI_DEBUG_MODE) {
                log_ai_operation("Stack trace: " . $e->getTraceAsString(), 'debug');
            }
            
            if (AI_FALLBACK_ENABLED) {
                return $this->getSmartFallback($user_question);
            } else {
                return $this->formatResponse("Service temporarily unavailable. Please try again.", 'error');
            }
        }
    }

    /**
     * Call OpenRouter API with cURL
     * Implements the TypeScript fetch logic in PHP
     */
    private function callOpenRouterAPI($data) {
        $ch = curl_init(OPENROUTER_ENDPOINT);
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENROUTER_API_KEY,
            'HTTP-Referer: ' . $_SERVER['HTTP_HOST'],
            'X-Title: UniEquip'
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => AI_TIMEOUT,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if (!empty($curl_error)) {
            throw new Exception("cURL Error: $curl_error");
        }

        if ($http_code !== 200) {
            $error_data = json_decode($response, true);
            $error_msg = $error_data['error']['message'] ?? "OpenRouter API Error (HTTP $http_code)";
            if (AI_LOG_FAILURES) {
                log_ai_operation("$error_msg: $response", 'error');
            }
            throw new Exception($error_msg);
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON Decode Error: " . json_last_error_msg());
        }
        
        return $decoded;
    }

    /**
     * Build system context for OpenRouter/Grok
     */
    private function buildSystemContext() {
        $role_desc = $this->user_role === 'student' ? 
            'a student user' : 'an admin/staff member';
        
        $equipment_count = $this->getEquipmentCount();
        $booking_count = $this->getBookingCount();
        $categories = $this->getCategories();
        
        $context = "You are UniEquip AI Assistant, a helpful and intelligent chatbot for a university equipment rental system. " .
                   "You are speaking to $role_desc. Use extended reasoning to provide thoughtful, accurate answers.\n\n" .
                   
                   "SYSTEM INFORMATION:\n" .
                   "- Equipment Categories: " . implode(', ', $categories) . "\n" .
                   "- Total Equipment Items: $equipment_count\n" .
                   "- Active Bookings: $booking_count\n" .
                   "- System Status: Operating 24/7\n" .
                   "- Current Date/Time: " . date('Y-m-d H:i:s') . "\n\n" .
                   
                   "YOUR PRIMARY RESPONSIBILITIES:\n" .
                   "1. Answer questions about equipment availability and details\n" .
                   "2. Explain the booking process and system features\n" .
                   "3. Provide intelligent, thoughtful support\n" .
                   "4. Use reasoning to verify accuracy before responding\n" .
                   "5. Be accurate, helpful, and conversational\n\n" .
                   
                   "REASONING GUIDELINES:\n" .
                   "- Think through the question carefully before responding\n" .
                   "- For complex queries, break them into logical steps\n" .
                   "- Verify facts against system knowledge\n" .
                   "- Explain your reasoning when helpful\n" .
                   "- For off-topic questions, explain why you're redirecting\n\n" .
                   
                   "RESPONSE FORMAT:\n" .
                   "- Be conversational but professional\n" .
                   "- Use 1-2 relevant emojis per response\n" .
                   "- Keep responses concise (100-300 words)\n" .
                   "- Format with **bold** headers and bullet points\n" .
                   "- Use numbered lists for procedures\n" .
                   "- Always be helpful, even for off-topic questions\n\n" .
                   
                   "UniEquip is committed to seamless, efficient equipment booking.";
        
        return $context;
    }

    /**
     * Add message to conversation history
     */
    private function addToHistory($role, $content, $reasoning_details = null) {
        $message = [
            'role' => $role,
            'content' => $content
        ];
        
        if ($reasoning_details !== null && AI_PRESERVE_REASONING) {
            $message['reasoning_details'] = $reasoning_details;
        }
        
        $this->conversation_history[] = $message;
        
        // Limit history size
        if (count($this->conversation_history) > AI_HISTORY_LENGTH * 2) {
            $this->conversation_history = array_slice($this->conversation_history, -AI_HISTORY_LENGTH);
        }
        
        // Save to session
        $_SESSION['ai_history'] = $this->conversation_history;
    }

    /**
     * Get equipment count for context
     */
    private function getEquipmentCount() {
        $query = "SELECT COUNT(*) as total FROM equipment";
        $result = $this->connect->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    /**
     * Get active booking count for context
     */
    private function getBookingCount() {
        $query = "SELECT COUNT(*) as total FROM booking WHERE status IN ('pending', 'approved', 'borrowed')";
        $result = $this->connect->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    /**
     * Get equipment categories for context
     */
    private function getCategories() {
        $query = "SELECT DISTINCT category FROM equipment WHERE category IS NOT NULL AND category != '' LIMIT 10";
        $result = $this->connect->query($query);
        $categories = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = ucfirst($row['category']);
            }
        }
        
        return !empty($categories) ? $categories : ['Camera', 'Projector', 'Microphone', 'Laptop'];
    }

    // ========================
    // INTENT DETECTION METHODS
    // ========================

    private function isAvailabilityQuestion($input) {
        return $this->matchesPattern($input, ['available', 'availability', 'borrow', 'in stock', 'have any', 'do you have']);
    }

    private function isCategoryQuestion($input) {
        return $this->matchesPattern($input, ['category', 'categories', 'types of equipment', 'what equipment', 'kind of']);
    }

    private function isBookingStatusQuestion($input) {
        return $this->matchesPattern($input, ['booking status', 'my booking', 'book status', 'confirm', 'approve']);
    }

    private function isQuantityQuestion($input) {
        return $this->matchesPattern($input, ['how many', 'quantity', 'stock', 'count', 'much do you have']);
    }

    private function isModelQuestion($input) {
        return $this->matchesPattern($input, ['model', 'brand', 'specs', 'specifications', 'what model']);
    }

    private function isMyBookingsQuestion($input) {
        return $this->matchesPattern($input, ['my booking', 'my reservations', 'my equipment', 'what i book', 'i booked']);
    }

    private function isAllEquipmentQuestion($input) {
        return $this->matchesPattern($input, ['all equipment', 'list equipment', 'show me equipment', 'equipment list']);
    }

    private function isBookingGuideQuestion($input) {
        return $this->matchesPattern($input, ['how to book', 'how do i book', 'booking guide', 'how to reserve', 'how to rent', 'how can i book', 'booking process', 'help with booking']);
    }

    private function matchesPattern($input, $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($input, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    // ========================
    // DATABASE QUERY HANDLERS
    // ========================

    private function handleAvailabilityQuery($input) {
        try {
            $equipment_name = $this->extractEquipmentName($input);
            
            if ($equipment_name) {
                $stmt = $this->connect->prepare(
                    "SELECT e.id_equipment, e.name, e.qty, 
                            (SELECT COUNT(*) FROM booking_equipment be 
                             JOIN booking b ON be.id_booking = b.id_booking 
                             WHERE be.id_equipment = e.id_equipment 
                             AND b.status IN ('pending', 'approved', 'borrowed')) as borrowed
                     FROM equipment e 
                     WHERE LOWER(e.name) LIKE ?"
                );
                
                if (!$stmt) {
                    throw new Exception('Prepare failed: ' . $this->connect->error);
                }
                
                $search = "%$equipment_name%";
                if (!$stmt->bind_param("s", $search)) {
                    throw new Exception('Bind param failed: ' . $stmt->error);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception('Execute failed: ' . $stmt->error);
                }
                
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $available = $row['qty'] - $row['borrowed'];
                    $percentage = round(($available / $row['qty']) * 100);

                    $status_emoji = $available > 0 ? '✅' : '⚠️';
                    $stock_bar = str_repeat('●', max(1, (int)($percentage / 10))) 
                            . str_repeat('○', max(0, 10 - (int)($percentage / 10)));

                    $message = "🪑 " . $row['name'] . "\n";
                    $message .= "─────────────\n";
                    $message .= "Status: $status_emoji " . ($available > 0 ? "Available" : "Unavailable") . "\n";
                    $message .= "Units: $available / " . $row['qty'] . "\n";
                    $message .= "Condition: " . $percentage . "%\n\n";
                    $message .= $stock_bar . " " . $percentage . "%";

                    return $this->formatResponse($message, 'success');
                }

            }
            
            // Generic availability response
            $query = "SELECT COUNT(DISTINCT id_equipment) as total, SUM(qty) as items FROM equipment";
            $result = $this->connect->query($query);
            
            if (!$result) {
                throw new Exception('Query failed: ' . $this->connect->error);
            }
            
            $row = $result->fetch_assoc();
            
            return $this->formatResponse(
                "Total Equipment: " . $row['total'] . " types\n" .
                "Total Items: " . $row['items'] . "\n\n" .
                "Try asking:\n" .
                "• How many [item]?\n" .
                "• Show categories\n" .
                "• My bookings",
                'success'
            );
        } catch (Exception $e) {
            if (AI_DEBUG_MODE) {
                error_log('handleAvailabilityQuery error: ' . $e->getMessage());
            }
            return $this->formatResponse('Unable to fetch availability data. Please try again later.', 'error');
        }
    }

    private function handleCategoryQuery($input) {
        $query = "SELECT category, COUNT(*) as count, SUM(qty) as total FROM equipment WHERE category IS NOT NULL AND category != '' GROUP BY category ORDER BY total DESC";
        $result = $this->connect->query($query);
        
        $message = "Categories:\n";
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $icon = match($row['category']) {
                    'Visual Equipment' => '🎬',
                    'Audio Equipment' => '🎙️',
                    'Furniture & Seating' => '🪑',
                    'Stage Equipment' => '🎭',
                    'Climate Control' => '❄️',
                    'Tents & Canopies' => '⛺',
                    'Signage & Display' => '📋',
                    'Catering Equipment' => '🍽️',
                    'Transportation & Storage' => '🚚',
                    default => '📦'
                };
                $message .= "$icon " . $row['category'] . " (" . $row['count'] . " • " . $row['total'] . ")\n";
            }
        } else {
            return $this->getSmartFallback('categories');
        }
        
        return $this->formatResponse($message, 'success');
    }

    private function handleBookingStatusQuery($input) {
        $stmt = $this->connect->prepare(
            "SELECT b.id_booking, b.status, b.start_date, COUNT(be.id_equipment) as items
             FROM booking b
             LEFT JOIN booking_equipment be ON b.id_booking = be.id_booking
             WHERE b.stud_num = ?
             GROUP BY b.id_booking
             ORDER BY b.start_date DESC
             LIMIT 5"
        );
        
        $stmt->bind_param("i", $this->user_num);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Your Bookings:\n\n";
            while ($row = $result->fetch_assoc()) {
                $status_icon = match($row['status']) {
                    'pending' => '⏳',
                    'approved' => '✅',
                    'borrowed' => '📦',
                    'returned' => '✔️',
                    'rejected' => '❌',
                    default => '❓'
                };
                $date = date('M d', strtotime($row['start_date']));
                $message .= "$status_icon #" . $row['id_booking'] . " • " . $row['items'] . " items • {$date}\n";
            }
        } else {
            $message = "No Bookings Yet\n\nStart booking to reserve equipment!";
        }
        
        return $this->formatResponse($message, 'info');
    }

    private function handleQuantityQuery($input) {
        $equipment_name = $this->extractEquipmentName($input);
        
        if ($equipment_name) {
            $stmt = $this->connect->prepare("SELECT name, qty FROM equipment WHERE LOWER(name) LIKE ?");
            $search = "%$equipment_name%";
            $stmt->bind_param("s", $search);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $status = $row['qty'] > 10 ? "✨ Plenty" : ($row['qty'] > 0 ? "⚠️ Limited" : "❌ Out");
                return $this->formatResponse(
                    $row['name'] . ": " . $row['qty'] . " units • $status",
                    'success'
                );
            }
        }
        
        return $this->getSmartFallback($input);
    }

    private function handleModelQuery($input) {
        $equipment_name = $this->extractEquipmentName($input);
        
        if ($equipment_name) {
            $stmt = $this->connect->prepare("SELECT name, model, category FROM equipment WHERE LOWER(name) LIKE ? LIMIT 1");
            $search = "%$equipment_name%";
            $stmt->bind_param("s", $search);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $message = $row['name'];
                if ($row['model']) {
                    $message .= " (Model: " . $row['model'] . ")";
                }
                $message .= " • " . $row['category'];
                return $this->formatResponse($message, 'success');
            }
        }
        
        return $this->getSmartFallback($input);
    }

    private function handleMyBookingsQuery() {
        $stmt = $this->connect->prepare(
            "SELECT b.id_booking, b.status, b.start_date,
                    GROUP_CONCAT(e.name SEPARATOR ', ') as items
             FROM booking b
             LEFT JOIN booking_equipment be ON b.id_booking = be.id_booking
             LEFT JOIN equipment e ON be.id_equipment = e.id_equipment
             WHERE b.stud_num = ?
             GROUP BY b.id_booking
             ORDER BY b.start_date DESC
             LIMIT 10"
        );
        
        $stmt->bind_param("i", $this->user_num);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Your Bookings:\n\n";
            while ($row = $result->fetch_assoc()) {
                $status_icon = match($row['status']) {
                    'pending' => '⏳',
                    'approved' => '✅',
                    'borrowed' => '📦',
                    'returned' => '✔️',
                    'rejected' => '❌',
                    default => '❓'
                };
                $date = date('M d', strtotime($row['start_date']));
                $message .= "$status_icon #" . $row['id_booking'];
                if (!empty($row['items'])) {
                    $message .= " • " . $row['items'];
                }
                $message .= " • {$date}\n";
            }
        } else {
            $message = "No Bookings Yet - Start exploring equipment!";
        }
        
        return $this->formatResponse($message, 'info');
    }

    private function handleAllEquipmentQuery() {
        $query = "SELECT id_equipment, name, qty, category FROM equipment ORDER BY category, name";
        $result = $this->connect->query($query);
        
        if (!$result || $result->num_rows === 0) {
            return $this->getSmartFallback('equipment');
        }
        
        $message = "All Equipment:\n\n";
        $current_category = '';
        $total_items = 0;
        
        while ($row = $result->fetch_assoc()) {
            if ($current_category !== $row['category']) {
                if ($current_category !== '') {
                    $message .= "\n";
                }
                $current_category = $row['category'];
                $category_icon = match($row['category']) {
                    'Visual Equipment' => '🎬',
                    'Audio Equipment' => '🎙️',
                    'Furniture & Seating' => '🪑',
                    'Stage Equipment' => '🎭',
                    'Climate Control' => '❄️',
                    'Tents & Canopies' => '⛺',
                    'Signage & Display' => '📋',
                    'Catering Equipment' => '🍽️',
                    'Transportation & Storage' => '🚚',
                    default => '📦'
                };
                $message .= "$category_icon " . $row['category'] . "\n";
            }
            
            $status = $row['qty'] > 5 ? '✅' : ($row['qty'] > 0 ? '⚠️' : '❌');
            $message .= "$status " . $row['name'] . " (" . $row['qty'] . ")\n";
            $total_items += $row['qty'];
        }
        
        $message .= "\n" . $result->num_rows . " types • " . $total_items . " items";
        
        return $this->formatResponse($message, 'success');
    }

    /**
     * Handle booking guide request
     */
    private function handleBookingGuide() {
        $message = "📋 How to Book Equipment\n";
        $message .= "─────────────────────────\n\n";
        $message .= "1️⃣ Browse Equipment\n";
        $message .= "View all available equipment and check details\n\n";
        $message .= "2️⃣ Check Availability\n";
        $message .= "Confirm dates & quantity available\n\n";
        $message .= "3️⃣ Fill Event Details\n";
        $message .= "Enter event name & description\n\n";
        $message .= "4️⃣ Select Dates\n";
        $message .= "Choose start & end dates\n\n";
        $message .= "5️⃣ Choose Equipment\n";
        $message .= "Select items & quantities needed\n\n";
        $message .= "6️⃣ Review & Submit\n";
        $message .= "Check details & submit booking\n\n";
        $message .= "7️⃣ Wait for Approval\n";
        $message .= "Admin will approve within 24 hours\n\n";
        $message .= "❓ Ask about specific equipment!";
        
        return $this->formatResponse($message, 'success');
    }

    /**
     * Extract equipment name from user input
     */
    private function extractEquipmentName($input) {
        // Get all equipment names from database for matching
        $query = "SELECT DISTINCT LOWER(name) as name FROM equipment";
        $result = $this->connect->query($query);
        
        if ($result && $result->num_rows > 0) {
            $input_lower = strtolower($input);
            while ($row = $result->fetch_assoc()) {
                if (strpos($input_lower, $row['name']) !== false) {
                    return $row['name'];
                }
            }
        }
        
        // Fallback to partial matching if no exact match
        $keywords = ['camera', 'projector', 'laptop', 'microphone', 'speaker', 'tripod', 'monitor', 'keyboard', 'mouse', 'sofa', 'chair', 'table', 'canopy', 'fan', 'banner', 'stand', 'rostrum', 'stage', 'trolley'];
        foreach ($keywords as $keyword) {
            if (strpos(strtolower($input), $keyword) !== false) {
                return $keyword;
            }
        }
        return null;
    }

    /**
     * Smart fallback with contextual responses
     */
    private function getSmartFallback($input) {
        $suggestions = [
            "I'm not quite sure about that. 🤔\n\n" .
            "📚 Try asking me about:\n" .
            "• Equipment availability (e.g., 'How many cameras?')\n" .
            "• Equipment categories\n" .
            "• Your current bookings\n" .
            "• Browse all equipment",
            
            "That's an interesting question! ✨\n\n" .
            "I specialize in equipment queries. Here's what I can help with:\n" .
            "🎯 Check equipment availability\n" .
            "📂 Browse by category\n" .
            "📋 View your bookings\n" .
            "📊 See inventory status",
            
            "I don't have specific info about that. 📖\n\n" .
            "But I can help you with:\n" .
            "✅ Equipment availability & stock\n" .
            "✅ Booking history\n" .
            "✅ Category browsing\n" .
            "✅ Complete equipment list",
            
            "💡 Smart tip: I work best with equipment-related questions!\n\n" .
            "Try asking:\n" .
            "• 'How many [equipment] available?'\n" .
            "• 'Show me all equipment'\n" .
            "• 'What are the categories?'\n" .
            "• 'Check my bookings'"
        ];
        
        return $this->formatResponse($suggestions[array_rand($suggestions)], 'info');
    }

    /**
     * Format response for JSON output
     */
    private function formatResponse($message, $type = 'info', $reasoning_details = null) {
        $response = [
            'message' => $message,
            'type' => $type
        ];
        
        if ($reasoning_details && AI_LOG_REASONING) {
            $response['reasoning'] = $reasoning_details;
        }
        
        return $response;
    }
}

// Process the query
try {
    if (!isset($connect)) {
        throw new Exception('Database connection failed');
    }
    $ai = new EquipmentAIWithReasoning($connect, $user_role, $user_num);
    $response = $ai->processQuery($input);
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    $error_msg = AI_DEBUG_MODE ? $e->getMessage() : 'An error occurred processing your request';
    error_log('Chatbot API Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
    echo json_encode([
        'error' => 'Server error',
        'message' => $error_msg,
        'type' => 'error'
    ]);
}
?>
