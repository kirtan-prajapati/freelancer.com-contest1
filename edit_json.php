<?php
// Define the path to the data.json file
define('JSON_FILE', 'data.json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the form input
    $jsonData = $_POST['jsonData'];

    // Validate the JSON data
    $decodedData = json_decode($jsonData, true);

    // Check if the JSON is valid
    if (json_last_error() === JSON_ERROR_NONE) {
        // JSON is valid, save it to the file
        if (file_put_contents(JSON_FILE, $jsonData)) {
            $successMessage = 'JSON data successfully updated.';
        } else {
            $errorMessage = 'Failed to update the JSON file.';
        }
    } else {
        // JSON is invalid, show an error message
        $errorMessage = 'Invalid JSON data. Please correct it and try again.';
    }
} else {
    // Load current data.json content for editing
    $jsonData = file_exists(JSON_FILE) ? file_get_contents(JSON_FILE) : '{}';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit JSON File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        textarea {
            width: 100%;
            height: 300px;
            margin-top: 10px;
            padding: 10px;
            font-family: monospace;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Edit JSON Data</h1>

    <!-- Display success or error message -->
    <?php if (isset($successMessage)): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php elseif (isset($errorMessage)): ?>
        <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <!-- Form to edit the JSON data -->
    <form action="edit_json.php" method="POST">
        <label for="jsonData">Edit the JSON data below:</label>
        <textarea name="jsonData" id="jsonData" required><?= htmlspecialchars($jsonData) ?></textarea>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>