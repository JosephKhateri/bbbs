<?php

// dbDonations.php
// Overall Grading:
// 1. Program specifications/correctness: Adequate - Program doesn't insert data into dbDonations properly
// 2. Readability: Adequate - Need further documentation for the functions. Variables are named accordingly.
    // Need to indent everything within <php> tags
// 3. Code efficiency: Good - Code is very efficient, but there are some issues with the code actually working properly
// 4. Documentation: Adequate - Need further documentation for the functions
// 5. Assigned Task: Adequate - Program doesn't insert data into dbDonations properly
function checkDonationExists($email, $con) {
    $query = $con->prepare("SELECT Email FROM dbdonations WHERE Email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    return $result->num_rows > 0;
}

function addDonation($donationData, $con) {
    $email = trim($donationData[7]);
    $dateOfContribution = $donationData[0];
    $amountGiven = $donationData[3]; // Ensure this is captured correctly from our CSV
    if (empty($email) || empty($dateOfContribution) || empty($amountGiven)) {
        error_log("Missing essential donation information: " . implode(", ", $donationData));
        return;
    }
    // Prepare the SQL query to insert a new donation
    $query = $con->prepare("INSERT INTO dbdonations (Email, DateOfContribution, ContributedSupportType, ContributionCategory, AmountGiven, PaymentMethod, Memo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssdss", $donationData['Email'], $donationData['Date of Contribution'], $donationData['Contributed Support'], $donationData['Contribution Category'], $donationData['Amount Given'], $donationData['Payment Method'], $donationData['Memo']);
    if (!$query->execute()) {
        error_log("Failed to insert donation: " . $query->error);
    } else {
        // Optionally, call updateLifetime here if it's not automatically triggered elsewhere
        updateLifetime($email, $con);
    }
}

function updateDonationInfo($donationData, $con) {
    // Prepare the SQL query to update donation info
    $query = $con->prepare("UPDATE dbdonations SET DateOfContribution = ?, ContributedSupportType = ?, ContributionCategory = ?, AmountGiven = ?, PaymentMethod = ?, Memo = ? WHERE Email = ?");
    $query->bind_param("sssdsss", $donationData['Date of Contribution'], $donationData['Contributed Support'], $donationData['Contribution Category'], $donationData['Amount Given'], $donationData['Payment Method'], $donationData['Memo'], $donationData['Email']);
    $query->execute();
}

function updateLifetime($email, $con) {
    $query = $con->prepare("UPDATE dbdonors SET LifetimeDonation = COALESCE((SELECT SUM(AmountGiven) FROM dbdonations WHERE Email = ?), 0) WHERE Email = ?");
    $query->bind_param("ss", $email, $email);
    if (!$query->execute()) {
        error_log("Failed to update lifetime donation: " . $query->error);
    }
}



?>
