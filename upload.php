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
    require_once('database/dbDonors.php');
    require_once('database/dbDonations.php');
    require_once('domain/Donor.php');
    require_once('domain/Donation.php');

    $con = connect(); 

    // Open the CSV file
    $file = fopen($csvFilePath, 'r');
    if (!$file) {
        // If the file couldn't be opened, redirect with an error message
        header('Location: index.php?fileFail');
        exit;
    }

    fgetcsv($file); // Skip header row

    echo ($line = fgetcsv($file));
    while (($line = fgetcsv($file)) !== false) {
        // Check for a valid email in the expected column (index 7 based on your CSV structure)
        if (!filter_var(trim($line[7]), FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid or missing email for row: " . implode(",", $line));
            continue; // Skip rows with invalid or missing emails
        }

        // Process donor data
        processDonorData($line, $con);

        // Process donation data
        processDonationData($line, $con);
    }

    // Close the CSV file
    fclose($file);
    
    // Redirect with success message
    //header('Location: index.php?fileSuccess');
    //exit;
}
?>
