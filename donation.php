<?php

function processDonationData($donationData, $con) {
    // Assuming donationData has an email as a unique identifier
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
