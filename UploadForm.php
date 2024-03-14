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

    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
      }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the temporary file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        $fileType = mime_content_type($tmpFilePath);
        //Filepath printing for debugging
        //echo $tmpFilePath;
        if ($fileType !== 'text/csv') {
            echo 'Only CSV files are allowed.';
        } else {
            require 'upload.php';
            parseCSV($tmpFilePath);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php require('universal.inc'); ?>
    <title>CSV File Upload</title>
    <style>
            .fileSelect{
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 4rem 0;
            }
            @media only screen and (min-width: 1024px) {
                .fileSelect {
                    /* width: 40%; */
                    width: 35rem;
            }
            main.upload {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
	    .column {
		padding: 0 4rem 0 0;
		width: 50%;
	    }
	    .row{
          	display: flex;
            }
	    }
    </style>
</head>
<body>
    <?php require('header.php'); ?>
    <h1>Upload File</h1>
    <main class="upload"> 
    <h2>Please select a CSV file to upload </h2>
        
        <form method="POST" name="uploadFile" class="fileSelect" enctype="multipart/form-data">
            <label for="uploadFile">Select File</label>
            <input type="file" name="file" accept=".csv">
            <input type="submit" value="Upload">
        </form>
    </main>
</body>
</html>