<?php

require_once('database/dbinfo.php');
require_once(dirname(__FILE__).'/../donor.php');

function checkDonorExists($email, $con) {
    $query = $con->prepare("SELECT Email FROM dbdonors WHERE Email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    return $result->num_rows > 0;
}

function addDonor($donorData, $con) {
    $email = isset($donorData[7]) ? trim($donorData[7]) : '';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Log error or handle the case where the email is invalid or missing
        error_log("Attempted to add donor with invalid or missing email.");
        return; // Exit the function to avoid database errors
    }
    // Insert query with all the columns present in the dbdonors table
    $query = $con->prepare("INSERT INTO dbdonors (Email, Company, FirstName, LastName, PhoneNumber, Address, City, State, Zip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssissss", $donorData['Email'], $donorData['Company'], $donorData['First Name'], $donorData['Last Name'], $donorData['Phone Number'], $donorData['Address'], $donorData['City'], $donorData['State'], $donorData['Zip']);
    $query->execute();
}

function combineDonor($donorData, $con) {
    // Prepare the SQL query to update an existing donor
    $query = $con->prepare("UPDATE dbdonors SET Company = ?, FirstName = ?, LastName = ?, PhoneNumber = ?, Address = ?, City = ?, State = ?, Zip = ? WHERE Email = ?");
    $query->bind_param("sssisssss", $donorData['Company'], $donorData['First Name'], $donorData['Last Name'], $donorData['Phone Number'], $donorData['Address'], $donorData['City'], $donorData['State'], $donorData['Zip'], $donorData['Email']);
    $query->execute();
}

?>
