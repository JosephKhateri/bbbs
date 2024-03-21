<?php

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
