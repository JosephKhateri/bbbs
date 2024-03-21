<?php

function checkDonationExists($email, $con) {
    $query = $con->prepare("SELECT Email FROM dbdonations WHERE Email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    return $result->num_rows > 0;
}

function addDonation($donationData, $con) {
    // Prepare the SQL query to insert a new donation
    $query = $con->prepare("INSERT INTO dbdonations (Email, DateOfContribution, ContributedSupportType, ContributionCategory, AmountGiven, PaymentMethod, Memo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssdss", $donationData['Email'], $donationData['Date of Contribution'], $donationData['Contributed Support'], $donationData['Contribution Category'], $donationData['Amount Given'], $donationData['Payment Method'], $donationData['Memo']);
    $query->execute();
}

function updateDonationInfo($donationData, $con) {
    // Prepare the SQL query to update donation info
    $query = $con->prepare("UPDATE dbdonations SET DateOfContribution = ?, ContributedSupportType = ?, ContributionCategory = ?, AmountGiven = ?, PaymentMethod = ?, Memo = ? WHERE Email = ?");
    $query->bind_param("sssdsss", $donationData['Date of Contribution'], $donationData['Contributed Support'], $donationData['Contribution Category'], $donationData['Amount Given'], $donationData['Payment Method'], $donationData['Memo'], $donationData['Email']);
    $query->execute();
}

function updateLifetime($email, $con) {
    // Assuming 'LifetimeDonation' as a column in the 'dbdonors' table -- ONCE AGAIN, BIG ASSUMPTION!!
    $query = $con->prepare("UPDATE dbdonors SET LifetimeDonation = (SELECT SUM(AmountGiven) FROM dbdonations WHERE Email = ?) WHERE Email = ?");
    $query->bind_param("ss", $email, $email);
    $query->execute();
}

?>
