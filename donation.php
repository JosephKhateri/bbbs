<?php

// donation.php
// Overall Grading:
// 1. Program specifications/correctness: Adequate - Program doesn't insert data into dbDonations properly
// 2. Readability: Excellent - Has lots of comments throughout the function making it easy to read. Variables are named accordingly
    // Need to indent everything within <php> tags
// 3. Code efficiency: Adequate - Code is very efficient, but isn't working properly as mentioned above
// 4. Documentation: Good - Need doc style comments for the functions
// 5. Assigned Task: Adequate - Program isn't inserting data properly into dbDonations
// 6. Additional comments: Move this file into "domain" folder

function processDonationData($donationData, $con) {
    //     // Assuming donationData has the email as the unique identifier in the first position -- KEY WORD IS ASSUMING!!!
    $donorEmail = $donationData[0];

    // Check if donation exists for the donor
    $donationExists = checkDonationExists($donorEmail, $con);

    if (!$donationExists) {
        // Add new donation
        addDonation($donationData, $con);
    } else {
        // Update donation info
        updateDonationInfo($donationData, $con);
    }

    // Update lifetime donation amount
    updateLifetime($donorEmail, $con);
}

?>
