<?php

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
