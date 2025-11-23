<?php
/**
 * UniEquip AI Chatbot API
 * Intelligent equipment information assistant with natural language processing
 * Integrates with OpenAI API for enhanced conversational AI
 * Provides real-time database queries for equipment, bookings, and availability
 */

session_start();
include "db.php";
include "ai_config.php";

header('Content-Type: application/json');

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

// AI Response Handler
class EquipmentAI {
    private $connect;
    private $user_role;
    private $user_num;
    private $use_openai;

    public function __construct($conn, $role, $num) {
        $this->connect = $conn;
        $this->user_role = $role;
        $this->user_num = $num;
        // Check if OpenAI API is properly configured
        $this->use_openai = is_openai_configured();
    }

    /**
     * Process user input and generate appropriate response
     */
    public function processQuery($input) {
        $input_lower = strtolower($input);
        
        // Detect query intent - prioritize database queries
        if ($this->isAvailabilityQuestion($input_lower)) {
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
            // For random/general questions, use OpenAI if available
            if ($this->use_openai) {
                log_ai_operation("Processing general question with OpenAI: $input");
                return $this->queryOpenAI($input);
            } else {
                log_ai_operation("Processing general question with local AI: $input");
                return $this->getSmartFallback($input);
            }
        }
    }

    /**
     * Query OpenAI for intelligent response
     * Provides context-aware answers with system knowledge
     */
    private function queryOpenAI($user_question) {
        // Build context about UniEquip system
        $system_context = $this->buildSystemContext();
        
        // Prepare OpenAI request
        $messages = [
            [
                'role' => 'system',
                'content' => $system_context
            ],
            [
                'role' => 'user',
                'content' => $user_question
            ]
        ];

        $request_data = [
            'model' => OPENAI_MODEL,
            'messages' => $messages,
            'temperature' => AI_TEMPERATURE,
            'max_tokens' => AI_MAX_TOKENS,
            'top_p' => 1.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 0.0
        ];

        try {
            $response = $this->callOpenAIAPI($request_data);
            
            if ($response && isset($response['choices'][0]['message']['content'])) {
                $ai_response = $response['choices'][0]['message']['content'];
                log_ai_operation("OpenAI response generated successfully");
                return $this->formatResponse($ai_response, 'success');
            } else {
                // Fallback if OpenAI fails
                log_ai_operation("OpenAI returned no valid response, using fallback", 'warning');
                if (AI_FALLBACK_ENABLED) {
                    return $this->getSmartFallback($user_question);
                } else {
                    return $this->formatResponse("Unable to process your request at this time. Please try again.", 'error');
                }
            }
        } catch (Exception $e) {
            // Log error and fallback to local response
            log_ai_operation("OpenAI API Error: " . $e->getMessage(), 'error');
            
            if (AI_FALLBACK_ENABLED) {
                return $this->getSmartFallback($user_question);
            } else {
                return $this->formatResponse("Service temporarily unavailable. Please try again.", 'error');
            }
        }
    }

    /**
     * Call OpenAI API with error handling
     */
    private function callOpenAIAPI($data) {
        $ch = curl_init(OPENAI_ENDPOINT);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => AI_TIMEOUT,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . OPENAI_API_KEY
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if (!empty($curl_error)) {
            throw new Exception("cURL Error: $curl_error");
        }

        if ($http_code !== 200) {
            $error_msg = "OpenAI API Error (HTTP $http_code)";
            if (AI_LOG_FAILURES) {
                log_ai_operation("$error_msg: $response", 'error');
            }
            throw new Exception($error_msg);
        }

        return json_decode($response, true);
    }

    /**
     * Build system context for OpenAI
     * Provides AI with knowledge about UniEquip system
     */
    private function buildSystemContext() {
        $role_desc = $this->user_role === 'student' ? 
            'a student user' : 'an admin/staff member';
        
        // Get equipment stats for context
        $equipment_count = $this->getEquipmentCount();
        $booking_count = $this->getBookingCount();
        $categories = $this->getCategories();
        
        $context = "You are UniEquip AI Assistant, a helpful and friendly chatbot for a university equipment rental system. " .
                   "You are speaking to $role_desc. Be conversational, use relevant emojis, and provide helpful information.\n\n" .
                   
                   "SYSTEM INFORMATION:\n" .
                   "- Equipment Categories: " . implode(', ', $categories) . "\n" .
                   "- Total Equipment Items: $equipment_count\n" .
                   "- Active Bookings: $booking_count\n" .
                   "- System Status: Operating 24/7\n" .
                   "- Current Date/Time: " . date('Y-m-d H:i:s') . "\n\n" .
                   
                   "YOUR PRIMARY RESPONSIBILITIES:\n" .
                   "1. Answer questions about equipment availability and details\n" .
                   "2. Explain the booking process and system features\n" .
                   "3. Provide friendly, helpful support for any questions\n" .
                   "4. Redirect to system features when appropriate\n" .
                   "5. Be accurate and helpful at all times\n\n" .
                   
                   "INTERACTION GUIDELINES:\n" .
                   "- Be conversational and friendly (not robotic)\n" .
                   "- Use 1-2 relevant emojis per response\n" .
                   "- Keep responses concise but informative (100-300 words)\n" .
                   "- Format responses with clear sections using ** for headers\n" .
                   "- Use bullet points for lists\n" .
                   "- For off-topic questions, still be helpful but gently redirect\n" .
                   "- Always promote the dashboard features\n" .
                   "- If equipment specifics are asked, suggest using the search feature\n\n" .
                   
                   "SYSTEM FEATURES TO PROMOTE:\n" .
                   "- Equipment browsing and searching\n" .
                   "- Real-time availability tracking\n" .
                   "- Booking submission and tracking\n" .
                   "- Admin approval workflow\n" .
                   "- Booking history and status updates\n\n" .
                   
                   "UniEquip is committed to making equipment booking seamless, efficient, and user-friendly.";
        
        return $context;
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
     * Get all categories for context
     */
    private function getCategories() {
        $query = "SELECT DISTINCT category FROM equipment ORDER BY category";
        $result = $this->connect->query($query);
        $categories = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['category'];
            }
        }
        
        return $categories ?: ['Audio Equipment', 'Visual Equipment', 'Furniture & Seating'];
    }


    /**
     * Detect if question is about equipment availability
     */
    private function isAvailabilityQuestion($input) {
        $keywords = ['available', 'is there', 'do you have', 'can i borrow', 'stock', 'in stock', 'left'];
        return $this->containsKeywords($input, $keywords);
    }

    /**
     * Detect if question is about equipment categories
     */
    private function isCategoryQuestion($input) {
        $keywords = ['category', 'type of equipment', 'audio equipment', 'visual equipment', 'furniture', 'seating'];
        return $this->containsKeywords($input, $keywords);
    }

    /**
     * Detect if question is about booking status
     */
    private function isBookingStatusQuestion($input) {
        $keywords = ['booking', 'status', 'booking #', 'reservation', 'my booking', 'order'];
        return $this->containsKeywords($input, $keywords);
    }

    /**
     * Detect if question is about quantity
     */
    private function isQuantityQuestion($input) {
        $keywords = ['how many', 'quantity', 'much', 'total', 'count', 'remaining', 'left'];
        return $this->containsKeywords($input, $keywords);
    }

    /**
     * Detect if question is about specific model
     */
    private function isModelQuestion($input) {
        $keywords = ['model', 'epson', 'shure', 'mipro', 'daikin'];
        return $this->containsKeywords($input, $keywords);
    }

    /**
     * Detect if asking about user's own bookings
     */
    private function isMyBookingsQuestion($input) {
        $keywords = ['my booking', 'my bookings', 'my reservations', 'my orders'];
        return $this->containsKeywords($input, $keywords);
    }

    /**
     * Detect if asking for all equipment
     */
    private function isAllEquipmentQuestion($input) {
        $keywords = ['all equipment', 'list all', 'show all', 'what equipment'];
        return $this->containsKeywords($input, $keywords);
    }

    /**
     * Check if input contains any of the keywords
     */
    private function containsKeywords($input, $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($input, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extract equipment name from input
     */
    private function extractEquipmentName($input) {
        // Remove common words
        $cleaned = str_ireplace(['how many', 'are', 'is', 'there', 'available', 'do you have', 'can i borrow', 'left', '?'], '', $input);
        return trim($cleaned);
    }

    /**
     * Extract category from input
     */
    private function extractCategory($input) {
        $categories = ['Audio Equipment', 'Visual Equipment', 'Furniture & Seating', 'Catering Equipment', 'Climate Control', 'Signage & Display', 'Transportation & Storage', 'Stage Equipment'];
        
        foreach ($categories as $cat) {
            if (stripos($input, $cat) !== false || stripos($input, strtolower($cat)) !== false) {
                return $cat;
            }
        }
        return null;
    }

    /**
     * Handle availability query
     * Example: "How many projectors are available?"
     */
    private function handleAvailabilityQuery($input) {
        $equipment_name = $this->extractEquipmentName($input);
        
        $query = "SELECT name, qty, status, model, category FROM equipment WHERE LOWER(name) LIKE ? AND status = 'Available' LIMIT 5";
        $stmt = $this->connect->prepare($query);
        $search_term = "%$equipment_name%";
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return $this->formatResponse(
                "No available equipment found matching '$equipment_name'. Would you like me to check all equipment or show you available categories?",
                'info'
            );
        }

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        if (count($items) === 1) {
            $item = $items[0];
            $message = "✅ **{$item['name']}** is available!\n\n" .
                      "📊 **Available Quantity:** {$item['qty']}\n" .
                      "🏷️ **Category:** {$item['category']}\n";
            if ($item['model']) {
                $message .= "🔧 **Model:** {$item['model']}\n";
            }
            $message .= "\n💡 You can book this equipment from your dashboard!";
        } else {
            $message = "Found " . count($items) . " matching equipment:\n\n";
            foreach ($items as $item) {
                $message .= "• **{$item['name']}** ({$item['qty']} available) - {$item['category']}\n";
            }
        }

        return $this->formatResponse($message, 'success');
    }

    /**
     * Handle category query
     * Example: "What equipment is in the Audio category?"
     */
    private function handleCategoryQuery($input) {
        $category = $this->extractCategory($input);
        
        if (!$category) {
            return $this->formatResponse(
                "Available categories:\n\n" .
                "• Audio Equipment\n" .
                "• Visual Equipment\n" .
                "• Furniture & Seating\n" .
                "• Catering Equipment\n" .
                "• Climate Control\n" .
                "• Signage & Display\n" .
                "• Transportation & Storage\n" .
                "• Stage Equipment\n\n" .
                "Ask me about any of these categories!",
                'info'
            );
        }

        $query = "SELECT id_equipment, name, qty, status, model FROM equipment WHERE category = ? ORDER BY name";
        $stmt = $this->connect->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return $this->formatResponse("No equipment found in the $category category.", 'info');
        }

        $message = "📋 **Equipment in $category Category:**\n\n";
        while ($row = $result->fetch_assoc()) {
            $status_icon = $row['status'] === 'Available' ? '✅' : '🔧';
            $message .= "$status_icon **{$row['name']}** - {$row['qty']} available\n";
        }

        return $this->formatResponse($message, 'success');
    }

    /**
     * Handle booking status query
     * Example: "What is the status of booking #25?"
     */
    private function handleBookingStatusQuery($input) {
        // Extract booking ID if provided
        preg_match('/\d+/', $input, $matches);
        $booking_id = $matches[0] ?? null;

        if (!$booking_id && $this->user_role === 'student') {
            // Get latest booking for student
            $query = "SELECT id_booking, event_name, status, start_date, end_date FROM booking WHERE stud_num = ? ORDER BY id_booking DESC LIMIT 1";
            $stmt = $this->connect->prepare($query);
            $stmt->bind_param("s", $this->user_num);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return $this->formatResponse("You don't have any bookings yet. Use the dashboard to create a new booking!", 'info');
            }

            $booking = $result->fetch_assoc();
        } else {
            // Get specific booking
            if (!$booking_id) {
                return $this->formatResponse("Please provide a booking number (e.g., 'status of booking #25')", 'error');
            }

            $query = "SELECT id_booking, event_name, status, start_date, end_date FROM booking WHERE id_booking = ?";
            $stmt = $this->connect->prepare($query);
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return $this->formatResponse("Booking #$booking_id not found.", 'error');
            }

            $booking = $result->fetch_assoc();
        }

        $status_icon = $this->getStatusIcon($booking['status']);
        $message = "$status_icon **Booking #{$booking['id_booking']}**\n\n" .
                  "📅 **Event:** {$booking['event_name']}\n" .
                  "📌 **Status:** " . ucfirst($booking['status']) . "\n" .
                  "🗓️ **Start Date:** " . date('M d, Y', strtotime($booking['start_date'])) . "\n" .
                  "🗓️ **End Date:** " . date('M d, Y', strtotime($booking['end_date'])) . "\n";

        return $this->formatResponse($message, 'success');
    }

    /**
     * Handle quantity query
     * Example: "How many tripods are left?"
     */
    private function handleQuantityQuery($input) {
        $equipment_name = $this->extractEquipmentName($input);

        $query = "SELECT name, qty, status, category FROM equipment WHERE LOWER(name) LIKE ? ORDER BY qty DESC";
        $stmt = $this->connect->prepare($query);
        $search_term = "%$equipment_name%";
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return $this->formatResponse("Equipment '$equipment_name' not found in our inventory.", 'info');
        }

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        if (count($items) === 1) {
            $item = $items[0];
            $status_icon = $item['status'] === 'Available' ? '✅' : '🔧';
            $message = "$status_icon **{$item['name']}**\n\n" .
                      "📊 **Quantity:** {$item['qty']} units\n" .
                      "📌 **Status:** {$item['status']}\n" .
                      "🏷️ **Category:** {$item['category']}\n";
        } else {
            $message = "Found " . count($items) . " items:\n\n";
            foreach ($items as $item) {
                $message .= "• **{$item['name']}** - {$item['qty']} units ({$item['status']})\n";
            }
        }

        return $this->formatResponse($message, 'success');
    }

    /**
     * Handle model-specific query
     * Example: "Is projector model EPSON EB-X41 available?"
     */
    private function handleModelQuery($input) {
        preg_match('/([A-Z0-9\-]+(?:\s+[A-Z0-9\-]+)*)/i', $input, $matches);
        $model = $matches[1] ?? null;

        if (!$model) {
            return $this->formatResponse("Please specify a model number (e.g., 'EPSON EB-X41')", 'error');
        }

        $query = "SELECT name, model, qty, status, category FROM equipment WHERE LOWER(model) LIKE ? OR LOWER(name) LIKE ?";
        $stmt = $this->connect->prepare($query);
        $search = "%$model%";
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return $this->formatResponse("No equipment found with model '$model'.", 'info');
        }

        $message = "🔍 **Search Results for Model: $model**\n\n";
        while ($row = $result->fetch_assoc()) {
            $status_icon = $row['status'] === 'Available' ? '✅' : '🔧';
            $message .= "$status_icon **{$row['name']}**\n" .
                       "🔧 Model: {$row['model']}\n" .
                       "📊 Available: {$row['qty']} units\n" .
                       "📌 Status: {$row['status']}\n\n";
        }

        return $this->formatResponse($message, 'success');
    }

    /**
     * Handle "my bookings" query
     */
    private function handleMyBookingsQuery() {
        if ($this->user_role !== 'student') {
            return $this->formatResponse("This query is only available for students.", 'error');
        }

        $query = "SELECT id_booking, event_name, status, start_date, end_date FROM booking WHERE stud_num = ? ORDER BY start_date DESC LIMIT 10";
        $stmt = $this->connect->prepare($query);
        $stmt->bind_param("s", $this->user_num);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return $this->formatResponse("You have no bookings yet. Create one from your dashboard!", 'info');
        }

        $message = "📋 **Your Recent Bookings:**\n\n";
        while ($row = $result->fetch_assoc()) {
            $status_icon = $this->getStatusIcon($row['status']);
            $message .= "$status_icon **Booking #{$row['id_booking']}** - {$row['event_name']}\n" .
                       "📅 {$row['start_date']} to {$row['end_date']}\n" .
                       "Status: " . ucfirst($row['status']) . "\n\n";
        }

        return $this->formatResponse($message, 'success');
    }

    /**
     * Handle "all equipment" query
     */
    private function handleAllEquipmentQuery() {
        $query = "SELECT id_equipment, name, qty, status, category FROM equipment ORDER BY category, name";
        $result = $this->connect->query($query);

        if ($result->num_rows === 0) {
            return $this->formatResponse("No equipment found in the inventory.", 'info');
        }

        $message = "📦 **Complete Equipment Inventory:**\n\n";
        $current_category = null;
        
        while ($row = $result->fetch_assoc()) {
            if ($current_category !== $row['category']) {
                $current_category = $row['category'];
                $message .= "\n**📁 {$row['category']}**\n";
            }
            $status_icon = $row['status'] === 'Available' ? '✅' : '🔧';
            $message .= "  $status_icon {$row['name']} ({$row['qty']} available)\n";
        }

        return $this->formatResponse($message, 'success');
    }

    /**
     * Get appropriate status icon
     */
    private function getStatusIcon($status) {
        $icons = [
            'pending' => '⏳',
            'approved' => '✅',
            'borrowed' => '📦',
            'returned' => '🔄',
            'rejected' => '❌',
            'available' => '✅',
            'maintenance' => '🔧'
        ];
        return $icons[strtolower($status)] ?? '•';
    }

    /**
     * Fallback response for unrecognized queries
     * Uses AI agent to provide helpful context-aware responses
     */
    private function getSmartFallback($input) {
        // Try to intelligently respond based on context
        return $this->handleGeneralQuestion($input);
    }

    /**
     * Handle general/random questions with AI agent
     * Provides intelligent context-aware responses
     */
    private function handleGeneralQuestion($input) {
        $input_lower = strtolower($input);
        
        // Detect intent patterns
        
        // Help/Support questions
        if ($this->matchesPattern($input_lower, ['help', 'support', 'assist', 'can you', 'how do i', 'what can you', 'how does this'])) {
            return $this->handleHelpQuestion($input);
        }
        
        // Greeting/Welcome questions
        if ($this->matchesPattern($input_lower, ['hi', 'hello', 'hey', 'greetings', 'how are you', 'what\'s up'])) {
            return $this->handleGreeting();
        }
        
        // Feedback/Complaint questions
        if ($this->matchesPattern($input_lower, ['feedback', 'complaint', 'issue', 'problem', 'bug', 'suggestion', 'improve'])) {
            return $this->handleFeedback($input);
        }
        
        // System/Feature questions
        if ($this->matchesPattern($input_lower, ['feature', 'function', 'system', 'work', 'does it', 'capability', 'tool'])) {
            return $this->handleSystemQuestion($input);
        }
        
        // When questions
        if ($this->matchesPattern($input_lower, ['when', 'time', 'hours', 'available', 'open', 'closed'])) {
            return $this->handleScheduleQuestion($input);
        }
        
        // Who questions
        if ($this->matchesPattern($input_lower, ['who', 'contact', 'admin', 'staff', 'support team'])) {
            return $this->handleContactQuestion();
        }
        
        // Why questions
        if ($this->matchesPattern($input_lower, ['why', 'reason', 'cause', 'purpose', 'explain'])) {
            return $this->handleExplanationQuestion($input);
        }
        
        // Default: Provide context-based suggestion
        return $this->handleContextualFallback($input);
    }

    /**
     * Pattern matching helper
     */
    private function matchesPattern($input, $patterns) {
        foreach ($patterns as $pattern) {
            if (strpos($input, strtolower($pattern)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Handle help-related questions
     */
    private function handleHelpQuestion($input) {
        $response = "🤝 **Happy to help!**\n\n" .
                   "I'm the **UniEquip AI Assistant**, designed to help you with:\n\n" .
                   "📦 **Equipment Queries**\n" .
                   "• Check equipment availability\n" .
                   "• Search by equipment name or model\n" .
                   "• Browse equipment by category\n" .
                   "• See quantity available\n\n" .
                   "📅 **Booking Information**\n" .
                   "• View your booking history\n" .
                   "• Check booking status\n" .
                   "• Get booking details\n\n" .
                   "💡 **Quick Tips**\n" .
                   "• Use specific equipment names for better results\n" .
                   "• I understand natural language questions\n" .
                   "• Try: \"How many projectors?\" or \"Show Audio equipment\"\n\n" .
                   "Still need help? Visit the dashboard for more features!";
        
        return $this->formatResponse($response, 'info');
    }

    /**
     * Handle greeting/welcome questions
     */
    private function handleGreeting() {
        $greetings = [
            "👋 **Hello!** I'm the UniEquip AI Assistant. Ready to help you find equipment information?",
            "🤖 **Hey there!** Welcome! I can help you with equipment availability, bookings, and more.",
            "✨ **Greetings!** I'm here to make your equipment search quick and easy.",
            "😊 **Hi!** How can I assist you with UniEquip today?",
            "🎉 **Welcome!** Ask me anything about our equipment inventory!"
        ];
        
        $response = $greetings[array_rand($greetings)];
        return $this->formatResponse($response, 'info');
    }

    /**
     * Handle feedback/suggestions
     */
    private function handleFeedback($input) {
        $response = "📝 **Thank you for your feedback!**\n\n" .
                   "Your input is valuable to us. Here's what you can do:\n\n" .
                   "1. 📧 Contact the admin team through the system\n" .
                   "2. 💬 Share feedback in your profile settings\n" .
                   "3. 🎯 Report issues directly to support staff\n\n" .
                   "**Your feedback helps us improve UniEquip!**\n" .
                   "We're committed to making equipment booking easier.\n\n" .
                   "Is there anything else I can help you with right now?";
        
        return $this->formatResponse($response, 'success');
    }

    /**
     * Handle system/feature questions
     */
    private function handleSystemQuestion($input) {
        $response = "⚙️ **UniEquip System Overview**\n\n" .
                   "**Core Features:**\n" .
                   "✓ Real-time equipment availability tracking\n" .
                   "✓ Instant booking reservations\n" .
                   "✓ AI-powered equipment search (that's me!)\n" .
                   "✓ Booking status tracking\n" .
                   "✓ Equipment categorization\n" .
                   "✓ Inventory management\n\n" .
                   "**How It Works:**\n" .
                   "1. Browse or search for equipment\n" .
                   "2. Check availability and details\n" .
                   "3. Submit a booking request\n" .
                   "4. Get admin approval\n" .
                   "5. Collect and return equipment\n\n" .
                   "Want to know more about a specific feature?";
        
        return $this->formatResponse($response, 'info');
    }

    /**
     * Handle schedule/time-related questions
     */
    private function handleScheduleQuestion($input) {
        $response = "🕐 **Availability Information**\n\n" .
                   "**System Access:**\n" .
                   "• Available 24/7 online\n" .
                   "• Submit bookings anytime\n" .
                   "• Check availability instantly\n\n" .
                   "**Equipment Pickup/Return:**\n" .
                   "• Coordinate with admin staff\n" .
                   "• Dates specified in your booking\n" .
                   "• Contact staff for specifics\n\n" .
                   "**Admin Support Hours:**\n" .
                   "• Check with your institution\n" .
                   "• View staff contact info in system\n\n" .
                   "Is there a specific equipment or booking you need help with?";
        
        return $this->formatResponse($response, 'info');
    }

    /**
     * Handle contact/staff questions
     */
    private function handleContactQuestion() {
        $response = "👥 **Contact Information**\n\n" .
                   "**For Equipment Inquiries:**\n" .
                   "→ Use the UniEquip system to browse equipment\n" .
                   "→ Check admin dashboard for staff details\n" .
                   "→ Contact your assigned admin for approvals\n\n" .
                   "**Admin Staff:**\n" .
                   "• View in dashboard\n" .
                   "• Check your booking details\n" .
                   "• Email/phone in system\n\n" .
                   "**Support:**\n" .
                   "• I'm here 24/7 for equipment help\n" .
                   "• Quick answers about availability\n" .
                   "• Instant booking information\n\n" .
                   "Need help finding something specific?";
        
        return $this->formatResponse($response, 'info');
    }

    /**
     * Handle explanation/why questions
     */
    private function handleExplanationQuestion($input) {
        if ($this->matchesPattern($input, ['why', 'booking', 'approval', 'status'])) {
            $response = "📌 **Why Do We Need Bookings?**\n\n" .
                       "✓ **Prevent Conflicts** - Ensure equipment isn't double-booked\n" .
                       "✓ **Track Usage** - Know where each item is\n" .
                       "✓ **Fair Access** - Everyone gets a chance to use equipment\n" .
                       "✓ **Maintenance** - Schedule necessary repairs\n" .
                       "✓ **Accountability** - Track borrowing history\n\n" .
                       "**Why Approval?**\n" .
                       "Admins verify:\n" .
                       "• Equipment availability\n" .
                       "• Event legitimacy\n" .
                       "• Quantity needed\n" .
                       "• Date/time conflicts\n\n" .
                       "This ensures smooth operations!";
        } else {
            $response = "🤔 **Good Question!**\n\n" .
                       "I understand you're asking about the reasoning behind something.\n\n" .
                       "Could you be more specific? For example:\n" .
                       "• 'Why do I need approval?'\n" .
                       "• 'Why is this status pending?'\n" .
                       "• 'Why can't I book this?'\n\n" .
                       "I'm here to explain the system!";
        }
        
        return $this->formatResponse($response, 'info');
    }

    /**
     * Contextual fallback for truly random questions
     */
    private function handleContextualFallback($input) {
        // Try to extract key nouns/entities
        $keywords = array_filter(explode(' ', strtolower($input)));
        $keywords = array_slice($keywords, 0, 5); // Limit to 5 words
        
        $responses = [
            "🤔 **Interesting question!**\n\n" .
            "I'm specifically designed for equipment and booking information.\n\n" .
            "📚 **Here's what I can help with:**\n" .
            "• Check equipment availability\n" .
            "• Find equipment by category\n" .
            "• View booking status\n" .
            "• Search by model number\n" .
            "• Browse all inventory\n\n" .
            "Try rephrasing your question about equipment or bookings!",
            
            "💡 **Thanks for the question!**\n\n" .
            "I'm optimized for UniEquip equipment queries, but I'll do my best!\n\n" .
            "**Quick reminder of what I excel at:**\n" .
            "✓ Equipment info & availability\n" .
            "✓ Booking details & status\n" .
            "✓ Category browsing\n" .
            "✓ Quantity checks\n\n" .
            "Feel free to ask about equipment!",
            
            "🎯 **Good to know you're curious!**\n\n" .
            "While I specialize in equipment management, I'm happy to help.\n\n" .
            "**My strengths:**\n" .
            "📦 Equipment queries\n" .
            "📅 Booking management\n" .
            "🔍 Advanced search\n" .
            "📊 Inventory info\n\n" .
            "Ask me about any of these!"
        ];
        
        $response = $responses[array_rand($responses)];
        return $this->formatResponse($response, 'info');
    }

    /**
     * Format response in JSON structure
     */
    private function formatResponse($message, $type = 'info') {
        return [
            'success' => true,
            'message' => $message,
            'type' => $type,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

// Initialize AI and process query
$ai = new EquipmentAI($connect, $user_role, $user_num);
$response = $ai->processQuery($input);

echo json_encode($response);
?>
