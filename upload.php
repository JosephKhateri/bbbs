<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function parseCSV($csvFilePath){
    require_once("database/dbinfo.php");
    require_once('database/dbDonors.php');
    require_once('database/dbDonations.php');
    require_once('include/input-validation.php');
    require_once('include/api.php');
    $con = connect(); 

    // Open the CSV file
    $file = fopen($csvFilePath, 'r');
    if (!$file) {
        // If the file couldn't be opened, redirect with an error message
        redirect('index.php?fileFail');
        exit;
    }

    fgetcsv($file); // Skip header row
    $lineNumber = 1;

    $support = '';
    $category = '';
    while (($line = fgetcsv($file)) !== false) {
        $lineNumber++;
        // Check for a valid email in the expected column (index 7 based on your CSV structure)
        /*if (!filter_var(trim($line[7]), FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid or missing email for row: " . implode(",", $line));
            continue; // Skip rows with invalid or missing emails
        }*/

        // Handle potential blank values in "Contributed Support" and "Contribution Category"
        if (!empty($line[1])) {
            $support = $line[1];
        }
        $currLineSupport = $support;

        if (!empty($line[2])) {
            $category = $line[2];
        }
        $currLineCategory = $category;


        // Process each line of the CSV file
        $date = trim($line[0]);
        $email = trim($line[7]);
        $phone = trim($line[8]);
        $zip = trim($line[12]);

        //validate phone number format (assuming phone number is in column index 8)
        if (!validatePhoneNumberFormat($phone)) {
            //invalid; redirect with error message
            redirect('index.php?phoneFormatFail=' . $lineNumber);
            exit;
        }

        //validate date format (assuming date is in column index 0)
        if (!validateDate($date)) {
            //invalid; redirect with error message
            redirect('index.php?dateFormatFail=' . $lineNumber);
            exit;
        }

        // Check for a valid email in the expected column (index 7)
        if (!validateEmail($email)) {
            redirect('index.php?emailFormatFail=' . $lineNumber);
            exit;
        }

        // Check for a valid zip code in the expected column (index 12)
        if (!validateZipcode($zip)) {
            redirect('index.php?zipFormatFail=' . $lineNumber);
            exit;
        }

        // Process donor data
        processDonorData($line, $con);
        processDonationData($line, $con, $currLineSupport, $currLineCategory);
    }

    // Close the CSV file
    fclose($file);

    // Redirect with success message
    redirect('index.php?fileSuccess');
    exit;
}
 
function processDonorData($donorData, $con) {
    // Assuming donorData has the email as the unique identifier in the 6th position -- KEY WORD IS ASSUMING!!!
    $donorEmail = $donorData[7];
    if (empty($donorEmail) || !checkDonorExists($donorEmail, $con)) {
        addDonor($donorData, $con);
    }

    // Check if donor exists
    $donorExists = checkDonorExists($donorEmail, $con);

    if (!$donorExists) {
        // Add new donor
        addDonor($donorData, $con);
    } else {
        // Combine donor data
        combineDonor($donorData, $con);
    }
}

// Call the parseCSV function with the CSV file path
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parseCSV($_FILES['file']['tmp_name']);
}
?>