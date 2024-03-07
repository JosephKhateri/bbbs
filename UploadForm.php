<?php
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;

    // Check if user is logged in
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level']; // Assuming this is set when the user logs in
        $userID = $_SESSION['_id'];
    }

    // File upload handling
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Get the temporary file path
        $tmpFilePath = $_FILES['file']['tmp_name'];


        /*
        // Target directory
        $targetDir = '/csvData';
        if (!file_exists($targetDir) || !is_writable($targetDir)) {
            die('Upload directory is not writable, or does not exist.');
        }

        // Full path for the uploaded file
        $targetFile = $targetDir . basename($_FILES['file']['name']);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        */

        // Validate file type
        if ($fileType !== 'csv') {
            echo 'Only CSV files are allowed.';
        } else {

            require 'upload.php'; // Make sure the path is correct
            parseCSV($tmpFilePath); // Assuming upload.php defines a function called parseCSV() (this is probably wrong tbh)

            /*
            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                echo 'File uploaded successfully.';
                // Include upload.php to start CSV parsing
                require 'upload.php'; // Make sure the path is correct
                parseCSV($tmpFilePath); // Assuming upload.php defines a function called parseCSV() (this is probably wrong tbh)
            } else {
                $error = error_get_last();
                echo 'Error uploading file. ' . htmlspecialchars($error['message']);
            }
            */
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php require('universal.inc'); ?>
    <title>CSV File Upload</title>
</head>
<body>
    <?php require('header.php'); ?>
    <h1>CSV File Upload</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv">
        <input type="submit" value="Upload">
    </form>
</body>
</html>
