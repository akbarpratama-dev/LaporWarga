<?php
/**
 * Vision AI Description Generator
 * Endpoint untuk menghasilkan deskripsi laporan dari foto menggunakan OpenAI Vision API
 */

// Set header JSON
header('Content-Type: application/json');

// Load configuration
$config_file = __DIR__ . '/../config/vision.php';
if (!file_exists($config_file)) {
    echo json_encode([
        'success' => false,
        'error' => 'Konfigurasi Vision AI belum diatur. Silakan hubungi administrator.'
    ]);
    exit;
}

$config = require $config_file;

// Validate API key
if (empty($config['openai_api_key']) || $config['openai_api_key'] === 'YOUR_OPENAI_API_KEY_HERE') {
    echo json_encode([
        'success' => false,
        'error' => 'API key OpenAI belum dikonfigurasi.'
    ]);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Method tidak valid.'
    ]);
    exit;
}

// Validate file upload
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'error' => 'Tidak ada file yang diunggah atau terjadi kesalahan upload.'
    ]);
    exit;
}

$file = $_FILES['foto'];

// Validate file type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $config['allowed_types'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Tipe file tidak didukung. Gunakan JPG atau PNG.'
    ]);
    exit;
}

// Validate file size
if ($file['size'] > $config['max_file_size']) {
    $max_mb = $config['max_file_size'] / 1048576;
    echo json_encode([
        'success' => false,
        'error' => "Ukuran file terlalu besar. Maksimal {$max_mb}MB."
    ]);
    exit;
}

// Read and encode image to base64
$image_data = file_get_contents($file['tmp_name']);
if ($image_data === false) {
    echo json_encode([
        'success' => false,
        'error' => 'Gagal membaca file gambar.'
    ]);
    exit;
}

$base64_image = base64_encode($image_data);

// Prepare OpenAI Vision API request
$api_url = 'https://api.openai.com/v1/chat/completions';

$payload = [
    'model' => $config['openai_model'],
    'messages' => [
        [
            'role' => 'system',
            'content' => $config['system_prompt']
        ],
        [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'Deskripsikan kondisi yang terlihat pada gambar ini untuk laporan warga:'
                ],
                [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:{$mime_type};base64,{$base64_image}"
                    ]
                ]
            ]
        ]
    ],
    'temperature' => $config['temperature'],
    'max_tokens' => $config['max_tokens']
];

// Initialize cURL
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $config['openai_api_key']
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle cURL errors
if ($response === false) {
    error_log("Vision AI cURL Error: " . $curl_error);
    echo json_encode([
        'success' => false,
        'error' => 'Gagal menghubungi layanan AI. Silakan coba lagi.'
    ]);
    exit;
}

// Parse API response
$result = json_decode($response, true);

// Handle API errors
if ($http_code !== 200 || !isset($result['choices'][0]['message']['content'])) {
    $error_msg = $result['error']['message'] ?? 'Terjadi kesalahan pada layanan AI.';
    error_log("Vision AI API Error (HTTP {$http_code}): " . json_encode($result));
    
    echo json_encode([
        'success' => false,
        'error' => 'Layanan AI tidak dapat memproses gambar. Silakan tulis deskripsi manual.'
    ]);
    exit;
}

// Extract description
$description = trim($result['choices'][0]['message']['content']);

// Validate description length
if (strlen($description) > $config['max_description_length']) {
    $description = substr($description, 0, $config['max_description_length']);
}

// Sanitize output
$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

// Return success response
echo json_encode([
    'success' => true,
    'description' => $description,
    'message' => 'Deskripsi berhasil dibuat. Anda dapat mengedit sebelum mengirim laporan.'
]);
?>
