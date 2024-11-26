<?php
// jdoodle_execute.php
header('Content-Type: application/json');

// API Configuration
define('JDOODLE_API_URL', 'https://api.jdoodle.com/v1/execute');
define('CLIENT_ID', 'f776b2d450e4824ec07e790990b1d66b');
define('CLIENT_SECRET', '4623f39a5aaedb3300baf5df2901f6eb672105fd220c617ba5f6521c37801bd5');

define('CODE_EXECUTION_TIMEOUT', 30);

function prepare_string($conn, $string) {
    return mysqli_real_escape_string($conn, trim($string));
}

function log_error($message, $context = array()) {
    $timestamp = date('Y-m-d H:i:s');
    $contextString = !empty($context) ? json_encode($context) : '';
    $logMessage = "[$timestamp] $message $contextString\n";
    error_log($logMessage, 3, '../logs/error.log');
}

function executeCode($script, $language) {
    // Validate input
    if (empty($script)) {
        return json_encode(['error' => 'Code cannot be empty']);
    }

    // Prepare API request
    $postData = [
        'clientId' => CLIENT_ID,
        'clientSecret' => CLIENT_SECRET,
        'script' => $script,
        'language' => $language,
        'versionIndex' => "0"
    ];

    // Initialize cURL session
    $ch = curl_init(JDOODLE_API_URL);
    
    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ]
    ]);

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Check for errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return json_encode(['error' => 'API Request failed: ' . $error]);
    }
    
    curl_close($ch);

    // Handle response
    if ($httpCode !== 200) {
        return json_encode(['error' => 'API returned error code: ' . $httpCode]);
    }

    return $response;
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['script']) || !isset($input['language'])) {
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

    // Execute code and return result
    echo executeCode($input['script'], $input['language']);
}
?>