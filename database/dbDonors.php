<?php

// dbDonors.php
// Overall Grading:
// 1. Program specifications/correctness: Good - Program mostly inserts data into dbDonors properly
// 2. Readability: Adequate - Need further documentation. Variables are named accordingly. Need to indent everything within <php> tags
// 3. Code efficiency: Excellent - Code is very efficient, but isn't working properly as mentioned above
// 4. Documentation: Adequate - Need further documentation for the functions
// 5. Assigned Task: Good - Program mostly inserts data into dbDonors properly but with 1 small error


require_once('database/dbinfo.php');
require_once(dirname(__FILE__) . '/../domain/Donor.php');

function checkDonorExists($email, $con) {
    $query = $con->prepare("SELECT Email FROM dbdonors WHERE Email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    return $result->num_rows > 0;
}

function addDonor($donorData, $con) {
    // Ensure email validation has already been done
    $email = trim($donorData[7]); // Already validated in upload.php
    
    // Ensure the order of `$donorData` elements matches the CSV columns order exactly (please work im begging)
    $query = $con->prepare("INSERT INTO dbdonors (Email, Company, FirstName, LastName, PhoneNumber, Address, City, State, Zip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssissss", $email, $donorData[4], $donorData[5], $donorData[6], $donorData[8], $donorData[9], $donorData[10], $donorData[11], $donorData[12]);
    
    if (!$query->execute()) {
        error_log("Failed to add donor: " . $query->error);
    }
}


function combineDonor($donorData, $con) {
    // Prepare the SQL query to update an existing donor
    $query = $con->prepare("UPDATE dbdonors SET Company = ?, FirstName = ?, LastName = ?, PhoneNumber = ?, Address = ?, City = ?, State = ?, Zip = ? WHERE Email = ?");
    $query->bind_param("sssisssss", $donorData['Company'], $donorData['First Name'], $donorData['Last Name'], $donorData['Phone Number'], $donorData['Address'], $donorData['City'], $donorData['State'], $donorData['Zip'], $donorData['Email']);
    $query->execute();
}

?>
