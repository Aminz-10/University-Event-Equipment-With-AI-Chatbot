<?php
/**
 * Simple Test for Chatbot API
 */
session_start();

// Fake a logged-in user for testing
$_SESSION['user_number'] = 1;
$_SESSION['role'] = 'student';

// Fake the POST request
$_POST['message'] = 'How many projectors are available?';

// Now run the API
include 'ai_openrouter_api.php';
?>
