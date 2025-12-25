<?php
/**
 * AI Chatbot API Endpoint
 * Handles chat requests and communicates with OpenAI API
 */

session_start();
header('Content-Type: application/json');

// Load configuration
$config = require_once __DIR__ . '/../config/chatbot.php';

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get and validate input
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? trim($input['message']) : '';

if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode(['error' => 'Pesan tidak boleh kosong']);
    exit;
}

// Validate message length
if (strlen($userMessage) > $config['max_message_length']) {
    http_response_code(400);
    echo json_encode(['error' => 'Pesan terlalu panjang (maksimal ' . $config['max_message_length'] . ' karakter)']);
    exit;
}

// Rate limiting
if (!isset($_SESSION['chatbot_messages'])) {
    $_SESSION['chatbot_messages'] = [];
    $_SESSION['chatbot_window_start'] = time();
}

// Clean old messages outside the window
$currentTime = time();
if ($currentTime - $_SESSION['chatbot_window_start'] > $config['rate_limit_window']) {
    $_SESSION['chatbot_messages'] = [];
    $_SESSION['chatbot_window_start'] = $currentTime;
}

// Check rate limit
if (count($_SESSION['chatbot_messages']) >= $config['rate_limit_messages']) {
    http_response_code(429);
    echo json_encode(['error' => 'Terlalu banyak pesan. Silakan tunggu beberapa menit.']);
    exit;
}

// Sanitize input
$userMessage = htmlspecialchars($userMessage, ENT_QUOTES, 'UTF-8');

// Initialize conversation history in session
if (!isset($_SESSION['chatbot_history'])) {
    $_SESSION['chatbot_history'] = [];
}

// Add system prompt if first message
if (empty($_SESSION['chatbot_history'])) {
    $_SESSION['chatbot_history'][] = [
        'role' => 'system',
        'content' => $config['system_prompt']
    ];
}

// Add user message to history
$_SESSION['chatbot_history'][] = [
    'role' => 'user',
    'content' => $userMessage
];

// Keep only last 10 messages (to avoid token limit)
if (count($_SESSION['chatbot_history']) > 11) { // 1 system + 10 messages
    $_SESSION['chatbot_history'] = array_merge(
        [array_shift($_SESSION['chatbot_history'])], // Keep system prompt
        array_slice($_SESSION['chatbot_history'], -9) // Keep last 9 messages
    );
}

// Prepare OpenAI API request
$apiUrl = 'https://api.openai.com/v1/chat/completions';
$apiKey = $config['openai_api_key'];

$requestData = [
    'model' => $config['openai_model'],
    'messages' => $_SESSION['chatbot_history'],
    'temperature' => $config['temperature'],
    'max_tokens' => $config['max_tokens']
];

// Initialize cURL
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ],
    CURLOPT_POSTFIELDS => json_encode($requestData),
    CURLOPT_TIMEOUT => 30
]);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Handle cURL errors
if ($curlError) {
    error_log('Chatbot cURL error: ' . $curlError);
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan koneksi. Silakan coba lagi.']);
    exit;
}

// Parse OpenAI response
$responseData = json_decode($response, true);

// Handle API errors
if ($httpCode !== 200) {
    error_log('OpenAI API error: ' . $response);
    
    if ($httpCode === 401) {
        http_response_code(500);
        echo json_encode(['error' => 'Konfigurasi API tidak valid']);
    } elseif ($httpCode === 429) {
        http_response_code(500);
        echo json_encode(['error' => 'Layanan sedang sibuk. Silakan coba lagi.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Terjadi kesalahan pada server']);
    }
    exit;
}

// Extract assistant's reply
if (!isset($responseData['choices'][0]['message']['content'])) {
    error_log('Invalid OpenAI response: ' . $response);
    http_response_code(500);
    echo json_encode(['error' => 'Respons tidak valid dari AI']);
    exit;
}

$assistantReply = trim($responseData['choices'][0]['message']['content']);

// Add assistant's reply to history
$_SESSION['chatbot_history'][] = [
    'role' => 'assistant',
    'content' => $assistantReply
];

// Track message count for rate limiting
$_SESSION['chatbot_messages'][] = $currentTime;

// Return success response
echo json_encode([
    'success' => true,
    'message' => $assistantReply,
    'timestamp' => date('H:i')
]);
?>
