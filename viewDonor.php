<?php
    // receiver.php

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    ini_set("display_errors",1);
    error_reporting(E_ALL);

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }

    // Require user privileges
    if ($accessLevel < 1) {
        header('Location: login.php');
        die();
    }

    // Include the file that contains the function definition
    require_once('database/dbDonors.php');
    require_once('database/dbDonations.php');
    require_once('domain/Donor.php');
    require_once('domain/Donation.php');

    $donor = null;
    $donations = null;

    // Check if the request method is GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Check if a parameter named 'donor' is present in the GET request
        if (isset($_GET['donor'])) {
            // Retrieve the donor email from the GET request
            $donorEmail = $_GET['donor'];

            // Get the donor's info and their donation
            $donor = retrieve_donor($donorEmail);
            $donations = retrieve_donations_by_email($donorEmail);

            // Check if the export button was clicked
            if (isset($_GET['export']) && $_GET['export'] === 'true') {
                // Export the donor's information to a CSV file
                exportDonorInfo($donor, $donations);
            }

            // If a donor with the provided email is not found, redirect to viewAllDonors.php with an error message
            if (!$donor) {
                header('Location: viewAllDonors.php?donorNotFound');
            }
        } else {
            // If the 'donor' parameter is not provided, redirect to viewAllDonors.php with an error message
            header('Location: viewAllDonors.php?donorNotProvided');
        }
    } else {
        // If the request method is not GET, redirect to viewAllDonors.php with an error message
        header('Location: viewAllDonors.php?invalidRequest');
    }

function exportDonorInfo($donor, $donations) {
    require_once('database/dbDonors.php');
    require_once('database/dbDonations.php');
    require_once('domain/Donor.php');
    require_once('domain/Donation.php');

    // Get donor last and first name and make it the file name
    $donorName = $donor->get_first_name() . '_' . $donor->get_last_name();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $donorName . '.csv"');

    $output = fopen("php://output", "w");

    // Write the CSV header for donor information
    fputcsv($output, array('First Name', 'Last Name', 'Email', 'Phone Number', 'Address', 'City', 'State', 'Zip'));

    // Write the donor's information to the CSV file
    fputcsv($output, array($donor->get_first_name(), $donor->get_last_name(), $donor->get_email(), preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $donor->get_phone()), $donor->get_address(), $donor->get_city(), $donor->get_state(), $donor->get_zip()));

    // Write 3 blank lines to separate the donor information from the donations
    $currLine = 0;
    $blankLines = 3; // Number of blank lines to write
    while ($currLine < $blankLines) {
        fputcsv($output, array());
        $currLine++;
    }

    // Write the CSV header for donations
    fputcsv($output, array('Date', 'Contribution Type', 'Contribution Category', 'Amount', 'Payment Method'));

    // Write the donor's donations to the CSV file
    foreach ($donations as $donation) {
        fputcsv($output, array($donation->get_contribution_date(), $donation->get_contribution_type(), $donation->get_contribution_category(), $donation->get_amount(), $donation->get_payment_method()));
    }

    fclose($output);
    exit(); // may need to toggle this later. However, if this is left out, then the html below gets printed to file
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>BBBS | View Donor Info</title>
    <style>
        /* Targeting the select element and option elements */
        select, option, input {
            color: white; /* Setting the font color to white */
            background-color: #333; /* A darker background for contrast */
        }

        select {
            -webkit-appearance: none; /* For some WebKit browsers */
            -moz-appearance: none;    /* For Firefox */
            appearance: none;         /* Standard syntax */
        }

        /* Optionally, style the select box to include a custom arrow icon */
        /*select {
            background-image: url('path-to-your-custom-arrow-icon');
            background-repeat: no-repeat;
            background-position: right .7em top 50%;
            background-size: .65em auto;
        }*/
    </style>

</head>
<body>
    <?php require_once('header.php') ?>
    <h1><?php echo $donor->get_first_name() . ' ' . $donor->get_last_name() ?></h1>
    <main class="date">
        <style>
            table {
                margin-top: 1rem;
                margin-left: auto;
                margin-right: auto;
                border-collapse: collapse;
                width: 80%;
            }
            td {
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
            }
            th {
                background-color: var(--main-color);
                color: black;
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
                font-weight: 500;
            }
            footer {
                margin-bottom: 5rem;
            }
        </style>

        <!-- Display the donor's information -->
        <h2 style="text-align: center">Donor Information</h2>
        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Zip</th>
            </tr>
            <!-- Display each attribute of the donor -->
            <tr>
                <td><?php echo $donor->get_first_name() ?></td>
                <td><?php echo $donor->get_last_name() ?></td>
                <td><?php echo $donor->get_email() ?></td>
                <td><?php echo preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $donor->get_phone()) ?></td> <!-- Format phone number -->
                <td><?php echo $donor->get_address() ?></td>
                <td><?php echo $donor->get_city() ?></td>
                <td><?php echo $donor->get_state() ?></td>
                <td><?php echo $donor->get_zip() ?></td>
            </tr>
        </table>

        <!-- Add a line break -->
        <tr><td colspan="5">&nbsp;</td></tr>

        <h2 style="text-align: center">Donation History</h2>
        <!-- Display all donations made by the donor -->
        <?php
        if (!empty($donations)) { // if the donor has made any donations, display them in a table
            ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Contribution Type</th>
                    <th>Contribution Category</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                </tr>
                <?php
                foreach ($donations as $donation) {
                    echo "<tr>";
                    echo "<td>" . $donation->get_contribution_date() . "</td>";
                    echo "<td>" . $donation->get_contribution_type() . "</td>";
                    echo "<td>" . $donation->get_contribution_category() . "</td>";
                    echo "<td>$" . $donation->get_amount() . "</td>";
                    echo "<td>" . $donation->get_payment_method() . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <?php
        } else { // There should be no instances where a donor has no donations, but this is a failsafe in case it happens
            echo "<div style='text-align: center;'>This donor has made no donations.</div>";
        }
        ?>

        <!-- Add a line break -->
        <tr><td colspan="5">&nbsp;</td></tr>

        <!-- Table of additional information (lifetime value, retention rate, donation frequency, etc.) will be located in table below -->



        <!-- Button to export donor information to a CSV file -->
        <form action="viewDonor.php" method="GET">
            <!-- For some reason both hidden fields are needed. Not sure why but this is what got the export function to actually work -->

            <!-- Add a hidden input field to hold the donor's email -->
            <input type="hidden" name="donor" value="<?php echo $donor->get_email(); ?>">
            <!-- Add a hidden input field to indicate the export action -->
            <input type="hidden" name="export" value="true">

            <!-- Submit button -->
            <input type="submit" value="Export to CSV" style="margin-top: 1rem">
        </form>


        <!-- Button to return to the list of donors -->
        <a class="button cancel" href="viewAllDonors.php" style="margin-top: -.5rem">Return to Donors</a>
    </main>
</body>
</html>