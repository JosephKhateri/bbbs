<?php

    // UploadForm.php
    // Overall Grading:
    // 1. Program specifications/correctness: Excellent - Only accepts csv files and does so without any errors
    // 2. Readability: Excellent - Variables are named accordingly and make code pretty easy to read, but need more documentation
    // 3. Code efficiency: Excellent - Code is very efficient and achieves the task perfectly
    // 4. Documentation: Adequate - Add some documentation about what each section of code does
    // 5. Assigned Task: Excellent - Only accepts csv files and does so without any errors

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
        //Filepath printing for debugging
        //echo $tmpFilePath;
        //echo $fileType;

        //Commented out for testing.
        /*if (($fileType == 'text/csv') || ($fileType == 'text/plain')) {
            require 'upload.php';
            parseCSV($tmpFilePath);
        } else {
            echo $fileType;
            header('Location: index.php?fileTypeFail');
        }**/

        //Check if file type is CSV or plain text
        if (($fileType == 'text/csv') || ($fileType == 'text/plain')) {
            //Check CSV file for phone number format and date format
            $formatCheckResult = checkCSVFormat($tmpFilePath);
            if ($formatCheckResult === true) {
                require 'upload.php';
                parseCSV($tmpFilePath);
            } else {
                $errorMsg = '';
                if ($formatCheckResult === 'phone') {
                    $errorMsg = 'Wrong phone number format. Cannot upload CSV. Try again later.';
                } elseif ($formatCheckResult === 'date') {
                    $errorMsg = 'Wrong date format. Cannot upload CSV. Try again later.';
                }
                echo "<script>alert('$errorMsg');</script>";
            }
        } else {
            header('Location: index.php?fileTypeFail');
            exit;
        }
    }

    // Function to check CSV format for phone number and date format.
    //If phone numbers contain any dashes or the date is not formatted as such: Y-M-D, then it prompts an error message.
    function checkCSVFormat($csvFilePath) {
        $file = fopen($csvFilePath, 'r');
        if (!$file) {
            return false; //Unable to open file
        }

        fgetcsv($file); //Skip header row

        while (($line = fgetcsv($file)) !== false) {
            //Check phone number format (no dashes allowed)
            if (preg_match('/\d{3}-\d{3}-\d{4}/', $line[3])) {
                fclose($file);
                return 'phone'; //Wrong phone number format
            }

            //Check date format (year-month-day order)
            $date = $line[6];
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                fclose($file);
                return 'date'; //Wrong date format
            }
        }
        fclose($file);
        return true; //Format checks passed
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php require('universal.inc'); ?>
    <title>BBBS | Upload CSV File</title>
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
