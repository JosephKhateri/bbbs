<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function parseCSV($csvFilePath){
    require_once('database/dbDonor.php');
    require_once('database/dbDonation.php');
    require_once('donor.php');
    require_once('donation.php');

    $con = connect(); 

    // Open the CSV file
    $file = fopen($csvFilePath, 'r');
    if (!$file) {
        // If the file couldn't be opened, redirect with an error message
        header('Location: index.php?fileFail');
        exit;
    }

    fgetcsv($file); // Skip header row

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
    header('Location: index.php?fileSuccess');
    exit;
}
?>
