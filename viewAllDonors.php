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
    } elseif (isset($_GET['export'])) {
        exportAllDonorInfo();
    } elseif (isset($_GET['query'])) {
        // Retrieve the search query
        $search_query = $_GET['query'];
        $donors = get_all_donors();
        $matching_donors = array_filter($donors, function($donor) use ($search_query) {
            // Array of attributes to search by
            $attributes = array(
                $donor->get_first_name() . " " . $donor->get_last_name(), // Full name
                $donor->get_email(), // Email
                $donor->get_company(), // Company
            );

            // Check if any attribute contains the search query
            foreach ($attributes as $attribute) {
                if (stripos($attribute, $search_query) !== false) {
                    return true; // Match found
                }
            }

            return false; // No match found for any attribute
        });

        // Update $donors with matching donors
        $donors = $matching_donors;
    }

    /**
     * Exports all donors and their information in dbDonors to a CSV file
     */
    function exportAllDonorInfo() : void {
        require_once('database/dbDonors.php');
        require_once('database/dbDonations.php');
        require_once('domain/Donor.php');
        require_once('domain/Donation.php');

        $donors = get_all_donors();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bbbs_donors.csv"');
        $output = fopen("php://output", "w");

        // Write the CSV header for donor information
        fputcsv($output, array('First Name', 'Last Name', 'Company', 'Email', 'Phone Number', 'Address', 'City', 'State', 'Zip'));

        foreach ($donors as $donor) {
            // Write the donor's information to the CSV file
            fputcsv($output, array($donor->get_first_name(), $donor->get_last_name(), $donor->get_company(), $donor->get_email(), preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $donor->get_phone()), $donor->get_address(), $donor->get_city(), $donor->get_state(), $donor->get_zip()));
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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
                font-weight: bold;
                text-decoration: underline;
                cursor: pointer;
            }
        </style>

        <input type="text" id="searchInput" name="query" placeholder="Search donors">
        <div id="searchResults"></div>

        <!-- Filters the contents of the Donor table based on the search query -->
        <script>
            $(document).ready(function() {
                $('#searchInput').on('input', function() {
                    let query = $(this).val();

                    $.ajax({
                        url: 'viewAllDonors.php',
                        method: 'GET',
                        data: { query: query },
                        success: function(response) {
                            $('#donorTable').html($(response).find('#donorTable').html()); // Replace content of the donor table
                        }
                    });
                });
            });
        </script>

        <!-- Table of all donors -->
        <!-- Display all donors in a table, displaying their emails and names -->
        <!-- When a donor is clicked, then will redirect to viewDonor.php with that donor's email passed as a parameter -->
        <table id="donorTable">
            <tr>
                <th onclick="sortTable(0)">Email</th>
                <th onclick="sortTable(1)">First Name</th>
                <th onclick="sortTable(2)">Last Name</th>
                <th onclick="sortTable(3)">Company</th>
            </tr>
            <?php
                foreach ($donors as $donor) {
                    echo "<tr>";
                    echo "<td><a href='viewDonor.php?donor=" . $donor->get_email() . "'>" . $donor->get_email() . "</a></td>";
                    echo "<td>" . $donor->get_first_name() . "</td>";
                    echo "<td>" . $donor->get_last_name() . "</td>";
                    echo "<td>" . $donor->get_company() . "</td>";
                    echo "</tr>";
                }
            ?>
        </table>

        <script>
            function sortTable(n) {
                let table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                table = document.getElementById("donorTable");
                switching = true;
                // Set the sorting direction to ascending:
                dir = "asc";
                /* Make a loop that will continue until
                no switching has been done: */
                while (switching) {
                    // Start by saying: no switching is done:
                    switching = false;
                    rows = table.rows;
                    /* Loop through all table rows (except the
                    first, which contains table headers): */
                    for (i = 1; i < (rows.length - 1); i++) {
                        // Start by saying there should be no switching:
                        shouldSwitch = false;
                        /* Get the two elements you want to compare,
                        one from current row and one from the next: */
                        x = rows[i].getElementsByTagName("TD")[n];
                        y = rows[i + 1].getElementsByTagName("TD")[n];
                        /* Check if the two rows should switch place,
                        based on the direction, asc or desc: */
                        if (dir === "asc") {
                            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                        } else if (dir === "desc") {
                            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    if (shouldSwitch) {
                        /* If a switch has been marked, make the switch
                        and mark that a switch has been done: */
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        // Each time a switch is done, increase this count by 1:
                        switchcount ++;
                    } else {
                        /* If no switching has been done AND the direction is "asc",
                        set the direction to "desc" and run the while loop again. */
                        if (switchcount === 0 && dir === "asc") {
                            dir = "desc";
                            switching = true;
                        }
                    }
                }
            }
        </script>

        <br>
        <!-- Button to export donor information to a CSV file -->
        <form action="viewAllDonors.php" method="GET">
            <!-- Add a hidden input field to indicate the export action -->
            <input type="hidden" name="export" value="true">
            <!-- Submit button -->
            <input type="submit" value="Export All Donors to CSV" style="margin-top: 1rem">
        </form>
        <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
    </main>
</body>
</html>