<?php

// Define constants for file names
define('JSON_FILE', 'data.json');
define('LOG_FILE', 'access.log');

// Set response headers
header('Content-Type: application/json');
header('Cache-Control: no-cache');

// Validate JSON file existence
if (!file_exists(JSON_FILE)) {
    http_response_code(404);
    echo json_encode(['error' => 'JSON file not found.']);
    exit;
}

// Get the last modification time of the JSON file
$lastModifiedTime = filemtime(JSON_FILE);

// Retrieve and sanitize `lastChecked` parameter
$lastChecked = isset($_GET['lastChecked']) ? (int)$_GET['lastChecked'] : 0;

// Check if this is a manual refresh
$isManual = isset($_GET['manual']) && filter_var($_GET['manual'], FILTER_VALIDATE_BOOLEAN);

if ($isManual) {
    // Log manual refresh to the access log
    $logEntry = sprintf("[%s] Manual refresh\n", date('Y-m-d H:i:s'));
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
}

// Long polling: Wait until the file is updated or timeout (30 seconds)
$timeout = 30;
$startTime = time();

while (time() - $startTime < $timeout) {
    clearstatcache();
    $currentModifiedTime = filemtime(JSON_FILE);

    if ($currentModifiedTime > $lastChecked) {
        $data = json_decode(file_get_contents(JSON_FILE), true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to decode JSON file.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $data,
            'lastModified' => $currentModifiedTime,
            'isManual' => $isManual,
        ]);
        exit;
    }

    usleep(500000); // Sleep for 500ms to reduce CPU usage
}

// No updates within the timeout period
http_response_code(200);
echo json_encode([
    'success' => false,
    'message' => 'No updates available.',
    'lastModified' => $lastChecked,
    'isManual' => $isManual,
]);
