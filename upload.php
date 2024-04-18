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

    $support = '';
    $category = '';
    while (($line = fgetcsv($file)) !== false) {
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
            redirect('index.php?phoneFormatFail');
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

        // If validations all pass, then create a new Donor and Donation object with the data from the current line
        /*$donor = new Donor ($email, $company, $first_name, $last_name, $phone, $address, $city, $state, $zip);

        $newID = count(get_all_donations()) + 1;
        $donation = new Donation ($newID, $email, $date, $contributed_support, $contribution_category, $amount, $payment_method, $memo);

        // With the following code below, should there be error handling regarding telling the user which lines successfully uploaded and which ones failed?

        // Add or update donor info based on if the donor already exists
        if (retrieve_donor($email) == null) {
            $donor_result = add_donor($donor);
        } else {
            $donor_result = update_donor($donor);
        }

        // Check if the donor was successfully added/updated
        if (!$donor_result) {
            // If the donor wasn't successfully added/updated, redirect with an error message
            header('Location: uploadForm.php?uploadFail');
            exit;
        }

        // Retrieve the max donation ID to determine if the donation should be added or updated
        // *******This needs to be changed to use the retrieve function that uses ami, date, and amount to check if the donation exists
        // what is currently here is incorrect and was a mistake when some of the code was being restructured
        if (getMaxDonationID() < $newID) {
            $donation_result = add_donation($donation);
        } else {
            $donation_result = update_donation($donation);
        }

        // Check if the donation was successfully added/updated
        if ($donation_result) {
            // If successful, continue to the next line
            continue;
        } else {
            // If the donation wasn't successfully added/updated, redirect with an error message
            header('Location: uploadForm.php?uploadFail');
            exit;
        }*/
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