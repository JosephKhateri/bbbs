<?php

// upload.php
// Overall Grading:
// 1. Program specifications/correctness: Good - Only accepts csv files and does so without any errors,
    // but has errors handling and uploading data properly
// 2. Readability: Excellent - Has lots of comments throughout the function making it easy to read. Everything
    // within <php> tags needs to be indented
// 3. Code efficiency: Good - Code is very efficient and achieves the task almost perfectly, with the caveat of the errors mentioned above
// 4. Documentation: Adequate - Add some documentation about what each section of code does
// 5. Assigned Task: Good - Mostly performs the task correctly, but still has some errors with doing so

//code review - Joel
//Readability - 
//the code is well organised and structured making it easy to understand.
//uses proper variable naming, proper identation
//Documentation 
//The code could benefit from more comments 
//the code doesnt have the header comment
//code efficiency
//the code is not bulky 

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
    $lineNumber = 1; //Start counting lines from 1

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
        $contributed_support = trim($line[1]);
        $contribution_category = trim($line[2]);
        $amount = trim($line[3]);
        $company = trim($line[4]);
        $first_name = trim($line[5]);
        $last_name = trim($line[6]);
        $email = trim($line[7]);
        $phone = trim($line[8]);
        $address = trim($line[9]);
        $city = trim($line[10]);
        $state = trim($line[11]);
        $zip = trim($line[12]);
        $payment_method = trim($line[13]);
        $memo = trim($line[14]);

        //validate phone number format (assuming phone number is in column index 8)
        if (!validatePhoneNumberFormat($line[8])) {
            //invalid; redirect with error message
            redirect('index.php?phoneFormatFail=' . $lineNumber);
            exit;
        }

        //validate date format (assuming date is in column index 0)
        if (!validateDate($line[0])) {
            //invalid; redirect with error message
            redirect('index.php?dateFormatFail');
            exit;
        }

        // Check for a valid email in the expected column (index 7)
        if (!validateEmail($line[7])) {
            redirect('index.php?emailFormatFail');
            exit;
        }

        // Check for a valid zip code in the expected column (index 12)
        if (!validateZipcode($line[12])) {
            redirect('index.php?zipFormatFail');
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