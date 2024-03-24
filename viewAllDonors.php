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
    if (isset($_GET['page'])) {
        // Retrieve the page parameter (donor email) from the URL
        $donorEmail = $_GET['page'];

        // Redirect to viewDonor.php with the page parameter
        header("Location: viewDonor.php?page=$donorEmail");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>BBBS | Reset User Password</title>
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

        <!-- Table of all donors -->
        <h2>Donors</h2>
            <!-- Display all donors in a table, displaying their emails and names -->
            <!-- When a donor is clicked, then will redirect to viewDonor.php with that donor's ID passed as a parameter -->
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                <?php foreach ($donors as $donor): ?>
                    <tr>
                        <td><a href="viewAllDonors.php?page=<?php $donor->get_email() ?>"><?php echo $donor->get_first_name() . ' ' . $donor->get_last_name() ?></a></td>
                        <td><a href="viewAllDonors.php"><?php echo $donor->email() ?></a></td>
                    </tr>
                <?php endforeach ?>
            </table>

        <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
    </main>
</body>
</html>