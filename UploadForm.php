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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the temporary file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        $fileType = mime_content_type($tmpFilePath);
        if ($fileType !== 'csv') {
            echo 'Only CSV files are allowed.';
        } else {

            require 'upload.php'; // Make sure the path is correct
            parseCSV($tmpFilePath);
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