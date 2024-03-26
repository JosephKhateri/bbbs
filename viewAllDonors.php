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
            <div class="error-toast">The donor selected was not found! If this message appears after trying to export the donor info file, then the "donor" parameter wasn't passed correctly</div>
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