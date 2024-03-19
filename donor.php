<?php

function processDonorData($donorData, $con) {
    // Assuming donorData has the email as the unique identifier in the first position
    $donorEmail = $donorData[0];

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
