<?php
    // Edited by Megan and Noor for BBBS in Spring 2024
    // Purpose: Allows users to view all donors

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

    require_once('database/dbDonors.php');
    require_once('domain/Donor.php');

    // Get all donors to display in the table
    $donors = get_all_donors();

    // if $donors is equal to false (meaning no donors were retrieved from the database), redirect to the dashboard
    if (!$donors) {
        header('Location: index.php?noDonors');
        die();
    }

    // if there's a get request, redirect to the viewDonor.php page with the donor's email as a parameter
    if (isset($_GET['donor'])) {
        // Retrieve the page parameter (donor email) from the URL
        $donorEmail = $_GET['donor'];

        // Redirect to viewDonor.php with the page parameter
        header("Location: viewDonor.php?donor=$donorEmail");
        exit();
    }

function exportDonorInfo($donor, $donations, $donor_type) {
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
    fputcsv($output, array('First Name', 'Last Name', 'Company', 'Email', 'Phone Number', 'Address', 'City', 'State', 'Zip'));

    // Write the donor's information to the CSV file
    fputcsv($output, array($donor->get_first_name(), $donor->get_last_name(), $donor->get_company(), $donor->get_email(), preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $donor->get_phone()), $donor->get_address(), $donor->get_city(), $donor->get_state(), $donor->get_zip()));

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

    $currLine = 0;
    while ($currLine < $blankLines) {
        fputcsv($output, array());
        $currLine++;
    }

    // Write the CSV header for donations
    fputcsv($output, array('Frequency of Giving', 'Lifetime Value', 'Retention', 'Donation Funnel', 'Event or Non-Event Donor'));
    fputcsv($output, array(get_donation_frequency($donor->get_email()), get_total_amount_donated($donor->get_email()), get_donor_retention($donor->get_email()), determine_donation_funnel($donor->get_email()), $donor_type));

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
    <h1>Donors</h1>
    <main class="date">

        <?php if (isset($_GET['donorNotFound'])): ?>
            <div class="error-toast">The donor selected was not found!</div>
        <?php elseif (isset($_GET['donorNotProvided'])): ?>
            <div class="error-toast">Please select a donor!</div>
        <?php elseif (isset($_GET['invalidRequest'])): ?>
            <div class="error-toast">Accessed the page incorrectly!</div>
        <?php endif ?>

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

        <!-- Table of all donors -->
        <!-- Display all donors in a table, displaying their emails and names -->
        <!-- When a donor is clicked, then will redirect to viewDonor.php with that donor's email passed as a parameter -->
        <table>
            <tr>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
            <?php
                foreach ($donors as $donor) {
                    echo "<tr>";
                    echo "<td><a href='viewDonor.php?donor=" . $donor->get_email() . "'>" . $donor->get_email() . "</a></td>";
                    echo "<td>" . $donor->get_first_name() . "</td>";
                    echo "<td>" . $donor->get_last_name() . "</td>";
                    echo "</tr>";
                }
            ?>
        </table>

        <br>
        <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
    </main>
</body>
</html>