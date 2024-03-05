<?php

    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $targetDir = '/path/to/upload/directory/'; // Replace with correct directory eventually
        $targetFile = $targetDir . basename($_FILES['file']['name']);
        $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

        // Check if file is a CSV
        if ($fileType !== 'csv') {
            echo 'Only CSV files are allowed.';
        } else {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                echo 'File uploaded successfully.';
            } else {
                echo 'Error uploading file.';
            }
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