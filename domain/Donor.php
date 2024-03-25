<?php

// Donor.php
// Overall Grading:
// 1. Program specifications/correctness: Good - Program mostly inserts data into dbDonors properly
// 2. Readability: Excellent - Has lots of comments throughout the function making it easy to read. Variables are named accordingly.
    // Need to indent everything within <php> tags
// 3. Code efficiency: Excellent - Code is very efficient, but isn't working properly as mentioned above
// 4. Documentation: Good - Need doc style comments for the functions
// 5. Assigned Task: Good - Program mostly inserts data into dbDonors properly but with 1 small error
// 6. Additional comments: Move this file into "domain" folder

function processDonorData($donorData, $con) {
    // Assuming donorData has the email as the unique identifier in the first position -- KEY WORD IS ASSUMING!!!
    $donorEmail = $donorData[7];
    if (empty($donorEmail)) {
        // Handle rows without email or log an error
        error_log("Email column is empty for a row, skipping...");
        return;
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

?>
