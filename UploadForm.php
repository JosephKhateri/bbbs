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

    require_once('include/api.php');

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;

    // Check if user is logged in
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level']; // Assuming this is set when the user logs in
        $userID = $_SESSION['_id'];
    }

    // Require user privileges
    if ($accessLevel < 1) {
        redirect('login.php');
        die();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the temporary file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        $fileType = mime_content_type($tmpFilePath);
        //Filepath printing for debugging
        //echo $tmpFilePath;
        echo $fileType;
        if (($fileType == 'text/csv') || ($fileType == 'text/plain')) {
            require 'upload.php';
            parseCSV($tmpFilePath);
        } else {
            redirect('index.php?fileTypeFail');
        }
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
        <p> CSV File should have the following headers (In order) </p>
        <p>
                 Date of Contribution, 
                 Contributed Support, 
                 Contribution Category, 
                 Amount Given, 
                 Company,                  
                 First Name, 
                 Last Name, 
                 Email, 
                 Phone Number, 
                 Address,
                 City,                  
                 State,                  
                 Zip, 
                 Payment Method, 
                 Memo 
        
    </main>

<script>
        // Something in the following script is fucked up
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('form[name="uploadFile"]');
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Stop the form from submitting the traditional way

            var formData = new FormData(this);
            
            // AJAX request to the server with the form data
            fetch('upload.php', { // Ensure this matches the path to your processing script
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'duplicate') {
                    // Ask the user what they want to do about the duplicate
                    if (confirm(data.message)) {
                        // If user chooses to proceed, resend data with a flag to force insert
                        formData.append('forceInsert', 'true');
                        fetch('upload.php', { // Resend to the same endpoint
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => alert(data.message)); // Notify the user of the outcome
                    } else {
                        alert('Duplicate donation not added.'); // User chose not to proceed
                    }
                } else {
                    alert('Donation processed successfully.'); // No duplicates detected
                    
                }
            })
            window.location.href = 'index.php?fileSuccess';
            .catch(error => console.error('Error:', error));
        });
    });
</script>

</body>
</html>
