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

    $locations = array();
    foreach ($donors as $donor) {
        $location = $donor->get_city() . ", " . $donor->get_state();
        // Ensure unique locations
        if (!in_array($location, $locations) and $location != ", ") {
            $locations[] = $location;
        }
    }
    // Sort the locations alphabetically
    sort($locations);

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
    // Check if city and state filters are set
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['city_state_combos'])) {

        // Sanitize the input data
        $cityStateFilters = array_map('htmlspecialchars', $_POST['city_state_combos']);

        // Assuming the city and state are separated by a comma in the checkbox values
        $cityFilters = [];
        $stateFilters = [];
        foreach ($cityStateFilters as $filter) {
            list($city, $state) = explode(', ', $filter);
            $cityFilters[] = $city;
            $stateFilters[] = $state;
        }

        $donors = get_filtered_donors($cityFilters, $stateFilters);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle case when city and state filters are not set
        $donors = get_all_donors();
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
            th > img {
                height: 1.5rem;
                float: right; /* Aligns the image to the right */
                margin-left: 8px; /* Adds some space between text and image */
            }

            #donorTable th,
            #donorTable td {
                width: 150px; /* Adjust the width as per your design */
                /* You can also use percentages for responsive design */
                /* width: 25%; */
            }

            .filter-group {
                display: flex;
                flex-wrap: wrap;
                margin-bottom: 10px; /* Adjust as needed */
            }

            .filter-group label {
                flex: 0 0 25%; /* Each checkbox occupies 25% of the container width */
                margin-bottom: 5px;
            }

            .filter-group input[type="checkbox"] {
                margin-right: 5px;
            }

            .popup {
                display: none;
                position: fixed;
                top: 400px;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: black;
                padding: 20px;
                border: 1px solid #ccc;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                z-index: 9999;
            }

            /* CSS to change button color on hover */
            #clearFiltersButton:hover {
                background-color: darkred;
            }
            /* Add this CSS to change the color of the close button */
            #closeFilterPopupButton {
                background-color: #808080; /* Background color */
                border: none; /* Remove border */
                margin-top: 10px;
                /*padding: 5px 10px; /* Adjust padding if needed */
                border-radius: 5px; /* Optional: Add border-radius for rounded corners */
            }
            /* CSS to lock scrolling */
            .no-scroll {
                overflow: hidden;
            }
            #searchInput {
                margin-bottom: -15px; /* Adjust the value as needed */
            }
        </style>

        <script>
            // Execute script after DOM has loaded
            $(document).ready(function(){
                // Function to load donor data based on selected filters
                function loadDonorData() {
                    // Get selected city and state filters
                    let filters = $("input[name='city_state_combos[]']:checked").map(function(){
                        return $(this).val();
                    }).get();

                    // Make AJAX call to fetch filtered donor data
                    $.ajax({
                        url: 'viewAllDonors.php',
                        type: 'POST',
                        data: {city_state_combos: filters}, // Send filters as an array
                        success: function(response) {
                            // Replace entire content of donor table with filtered data
                            $("#donorTable").html($(response).find('#donorTable').html());
                        },
                        error: function(xhr, status, error) {
                            console.error(error); // Handle errors if any
                        }
                    });
                }

                // Filter button click event
                $("#filterButton").click(function(event){
                    event.preventDefault(); // Prevent default form submission behavior
                    loadDonorData(); // Call function to load donor data
                    closeFilterPopup(); // Close the filter popup after filtering
                });

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

                // Function to open the filter popup
                function openFilterPopup() {
                    $("#filterPopup").show();
                    $("body").addClass("no-scroll"); // Lock scrolling
                }

                // Clear Filters button click event
                $("#clearFiltersButton").click(function(event){
                    event.preventDefault(); // Prevent default form submission behavior
                    // Deselect all checkboxes
                    $("input[name='city_state_combos[]']").prop('checked', false);
                    // Load all donors
                    loadAllDonors();
                    //closeFilterPopup(); // Close the filter popup after clearing filters
                });

                // Function to close the filter popup
                function closeFilterPopup() {
                    $("#filterPopup").hide();
                    $("body").removeClass("no-scroll"); // Unlock scrolling
                }

                function loadAllDonors() {
                    // Make AJAX call to fetch all donors
                    $.ajax({
                        url: 'viewAllDonors.php',
                        type: 'POST',
                        data: {}, // No filters needed
                        success: function(response) {
                            // Replace entire content of donor table with all donors
                            $("#donorTable").html($(response).find('#donorTable').html());
                        },
                        error: function(xhr, status, error) {
                            console.error(error); // Handle errors if any
                        }
                    });
                }

                // Open filter popup when button is clicked
                $("#popupButton").click(openFilterPopup);

                // Close filter popup when close button is clicked
                $("#closeFilterPopupButton").click(closeFilterPopup);

                // Close the filter popup if user clicks outside of it
                $(window).click(function(event) {
                    if (event.target === document.getElementById('filterPopup')) {
                        closeFilterPopup();
                    }
                });
            });
        </script>

        <input type="text" id="searchInput" name="query" placeholder="Search donors" >
        <div id="searchResults"></div>

        <button id="popupButton" style="border-radius: 5px; margin-bottom: 10px;">Filter Donors</button>

        <!-- Filter popup -->
        <div id="filterPopup" class="popup">
            <h6 style="color: #00FC87"><b>Locations:</b></h6>
            <div class="filter-group" style="margin-bottom: 10px;">
                <?php foreach ($locations as $location): ?>
                    <label style="margin-bottom: 5px;"><input type="checkbox" name="city_state_combos[]" value="<?= htmlspecialchars($location) ?>"> <?= htmlspecialchars($location) ?></label>
                <?php endforeach; ?>
            </div>
            <form id="filterForm" action="viewAllDonors.php" method="post">
                <div style="display: flex; justify-content: space-between; margin-top: 40px;">
                    <button type="button" id="filterButton" style="border-radius: 5px; margin-right: 10px;"
                            onmouseover="this.style.backgroundColor='#228B22'"
                            onmouseout="this.style.backgroundColor='#00FC87'">
                        Filter
                    </button>

                    <button type="button" id="clearFiltersButton" style="border-radius: 5px; background-color: red;"
                            onmouseover="this.style.backgroundColor='#B22222'"
                            onmouseout="this.style.backgroundColor='red'">
                        Clear All Filters
                    </button>
                </div>
                <button type="button" id="closeFilterPopupButton" style="border-radius: 5px;"
                        onmouseover="this.style.backgroundColor='#696969'"
                        onmouseout="this.style.backgroundColor='#808080'">
                    Close
                </button>
            </form>
        </div>

        <!-- Table of all donors -->
        <!-- Display all donors in a table, displaying their emails and names -->
        <!-- When a donor is clicked, then will redirect to viewDonor.php with that donor's email passed as a parameter -->
        <table id="donorTable">
            <tr>
                <th onclick="sortTable(0)">
                    Email
                    <img id="emailSortImg" src="images/sort-ascending.png">
                </th>
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
            let currentSortColumn = 0; // Default sorting column (email column)
            let sortDirection = "desc"; // Default sorting direction for email column is descending

            function sortTable(n) {
                console.log("Sorting table...");
                let table, rows, switching, i, x, y, shouldSwitch;
                table = document.getElementById("donorTable");
                switching = true;
                /* Make a loop that will continue until
                no switching has been done: */
                while (switching) {
                    // Start by assuming no switching is done:
                    switching = false;
                    rows = table.rows;
                    /* Loop through all table rows (except the
                    first, which contains table headers): */
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        /* Get the two elements you want to compare,
                        one from the current row and one from the next: */
                        x = rows[i].getElementsByTagName("td")[n];
                        y = rows[i + 1].getElementsByTagName("td")[n];
                        /* Check if the two rows should switch place,
                        based on the sorting direction: */
                        if (sortDirection === "asc") {
                            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                        } else if (sortDirection === "desc") {
                            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    if (shouldSwitch) {
                        // If a switch is needed, perform the switch and mark the switch as done:
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                    }
                }
                // Toggle the sorting direction for the clicked column:
                if (currentSortColumn === n) {
                    sortDirection = (sortDirection === "asc") ? "desc" : "asc";
                } else {
                    currentSortColumn = n;
                    sortDirection = (n === 0) ? "desc" : "asc"; // Set initial sorting direction to descending for email column, ascending for others
                }
                console.log("Sorting completed.");
                // Update sorting images after sorting:
                updateSortingImages();
            }

            function updateSortingImages() {
                console.log("Updating sorting images...");
                let tableHeaders = document.getElementsByTagName("th");
                for (let i = 0; i < tableHeaders.length; i++) {
                    let img = tableHeaders[i].querySelector("img");
                    if (i === currentSortColumn) {
                        if (img) {
                            img.src = (sortDirection === "asc") ? "images/sort-ascending.png" : "images/sort-descending.png";
                        } else {
                            tableHeaders[i].innerHTML += '<img src="images/sort-ascending.png" alt="Sorting">';
                        }
                    } else {
                        if (img) {
                            img.remove();
                        }
                    }
                }
                console.log("Sorting images updated.");
            }

            // Set initial images based on current order of the columns
            window.addEventListener('DOMContentLoaded', (event) => {
                console.log("DOM fully loaded and parsed");
                updateSortingImages();
            });
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