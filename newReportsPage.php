<?php 
    /**
    * @version April 6, 2023
    * @author Alip Yalikun
    */


    /**
     * Reviewed by Zack
     * Program Specifications/Correctness - Excellent
     * Readability - Good
     * Code Efficiency - Excellent
     * Documentation - Developing
     * Assigned Task - Excellent
     */


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

    require_once('include/input-validation.php');
    require_once('database/dbPersons.php');
    require_once('database/dbEvents.php');
    require_once('include/output.php');
    require_once('database/dbinfo.php');
    require_once('database/dbDonations.php');
    require_once('database/dbDonors.php');
    require_once('domain/Donor.php');
    require_once('domain/Donation.php');

    $connection = connect();
    $donorsOver10K = [];
    $donationQuery = "SELECT Email, SUM(AmountGiven) as TotalDonation FROM dbdonations GROUP BY Email HAVING TotalDonation > 10000";
    $donationResult = mysqli_query($connection, $donationQuery);
    while($row = mysqli_fetch_assoc($donationResult)) {
      $donorsOver10K[] = $row;
    }
  
// Report 1: List of Donors Who Donated Over $10,000
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
function reportDonorsOver10K($connection) {
                // Modified SQL query to join Donations with Donors table and fetch required details
                $query = "SELECT d.Email, SUM(d.AmountGiven) AS TotalDonation, p.FirstName, p.LastName, p.PhoneNumber 
                FROM dbdonations AS d
                JOIN dbdonors AS p ON d.Email = p.Email
                GROUP BY d.Email
                HAVING TotalDonation > 10000";
        $result = mysqli_query($connection, $query);

        // Check if we have results
        if (mysqli_num_rows($result) > 0) {
            echo "<h2 style='text-align: center;'>Donors Who Donated Over $10,000</h2>";
            echo "<table>";
            echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Total Donation</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                // Format the phone number
                $phone = $row['PhoneNumber'];
                $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
            
                echo "<tr>
                        <td>" . htmlspecialchars($row['Email']) . "</td>
                        <td>" . htmlspecialchars($row['FirstName']) . "</td>
                        <td>" . htmlspecialchars($row['LastName']) . "</td>
                        <td>" . htmlspecialchars($formattedPhone) . "</td>
                        <td>$" . number_format($row['TotalDonation'], 2) . "</td>
                      </tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No donors have donated over $10,000.</p>";
        }
}

// Report 2: Frequency of Giving
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
function reportFrequencyOfGiving($connection) {
            // Modified SQL query to join Donations with Donors table and fetch required details
            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                             DATEDIFF( CURRENT_DATE(), MIN(DateOfContribution)) AS DateDiff  
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>Donors' Frequency of Giving</h2>";
                echo "<br>";

                // Display the donation frequency categories
                /*echo "<h4 style='margin: 0; padding: 0; margin-left: 150px'> Donation Frequency Categories:</h4>";
                echo "<p style='margin: 0; padding: 0; margin-left: 150px'> 
                        - Monthly: Donated at least twice each month for the past 2 months</p>";
                echo "<p style='margin: 0; padding: 0; margin-left: 150px'> 
                        - Yearly: Donated at least once each year for the past 2 years</p>";
                echo "<p style='margin: 0; padding: 0; margin-left: 150px'> 
                        - Sporadic: Donates inconsistently</p>";
                echo "<br>";*/

                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Frequency of Giving</th>
                      <th>Days from Earliest Donation</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);

                    // Calculate the donor's frequency of giving
                    $FOG = get_donation_frequency($row["Email"]);

                    echo "<tr>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($formattedPhone) . "</td>
                            <td>" . htmlspecialchars($FOG) . "</td>
                            <td>" . number_format($row['DateDiff']) . "</td>
                          </tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>Not enough Donors are available to make the report.</p>";
            }
}

// Report 3: Donors Who Have Not Donated for the Last 2 Years
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
function reportDonorsNotDonatedIn2Years($connection) {
        // Modified SQL query to join Donations with Donors table and fetch required details
        // Get the current date
        $currentDate = date("Y-m-d");

        // Define the threshold date (two years ago from current date)
        $thresholdDate = date('Y-m-d', strtotime('-2 years', strtotime($currentDate)));

        $query = "SELECT d.FirstName, d.LastName, d.Email, MAX(dd.DateOfContribution) AS LastDonation
                FROM DbDonors d
                LEFT JOIN DbDonations dd ON d.Email = dd.Email
                GROUP BY d.Email
                HAVING LastDonation < '$thresholdDate' OR LastDonation IS NULL
                ORDER BY d.LastName;
                ";
        $result = mysqli_query($connection, $query);

        // Check if we have results
        if (mysqli_num_rows($result) > 0) {
            echo "<h2 style='text-align: center;'>Donors Who Have Not Donated for the Last 2 Years</h2>";
            echo "<table>";
            echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Date of Last Donation</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                // Format the phone number
                 
                echo "<tr>
                        <td>" . htmlspecialchars($row['Email']) . "</td>
                        <td>" . htmlspecialchars($row['FirstName']) . "</td>
                        <td>" . htmlspecialchars($row['LastName']) . "</td>
                        <td>" . htmlspecialchars($row['LastDonation']) . "</td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>All donors have contributed in the last 2 years.</p>";
        }
}

// Report 4: Events Donors Have Contributed To
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
function reportEventsDonorsContributed($connection) {
            // Fetch your data for the pie chart here
            $categoryQuery = "SELECT ContributionCategory, SUM(AmountGiven) AS TotalAmount FROM dbdonations WHERE ContributedSupportType = 'Fundraising Events' GROUP BY ContributionCategory";
            $categoryResult = mysqli_query($connection, $categoryQuery);
            $categories = [];
            while($row = mysqli_fetch_assoc($categoryResult)) {
                $categories[] = $row;
            }
            // Pass the PHP array to JavaScript
            echo "<script>let categoryData = " . json_encode($categories) . ";</script>";
            echo "<h2 style='text-align: center;margin-top: 30px;margin-bottom: 20px'>Events Donors Have Contributed To</h2>";
            // Include the Google Charts loader and the pie chart drawing script
            echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
            echo '<script type="text/javascript">
                    google.charts.load("current", {"packages":["corechart"]});
                    google.charts.setOnLoadCallback(drawChart);
                    
                    function drawChart() {
                        let data = new google.visualization.DataTable();
                        data.addColumn("string", "Category");
                        data.addColumn("number", "Amount");
                        categoryData.forEach(function(category) {
                            data.addRow([category.ContributionCategory, parseFloat(category.TotalAmount)]);
                        });
        
                        let options = {
                            title: "Donation Contribution Categories",
                            is3D: true,
                        };
                        let formatter = new google.visualization.NumberFormat({
                            prefix: "$", // Add dollar sign as prefix
                            fractionDigits: 2 // Display two decimal places
                        });
                        formatter.format(data, 1); // Apply formatting to the "Amount" column
        
                        let chart = new google.visualization.PieChart(document.getElementById("piechart"));
                        chart.draw(data, options);
                    }
                  </script>';
        
            // Output the container for the pie chart
            echo '<div id="piechart" style="width: 1200px; height: 700px; margin: auto;"></div>';

}

// Report 5: Frequency of Giving Greater Than Yearly
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
function reportFrequencyGreaterThanYearly($connection) {
            // Modified SQL query to join Donations with Donors table and fetch required details
            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                    DATEDIFF( CURRENT_DATE(), MIN(DateOfContribution)) AS DateDiff  
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>Donors Whose Frequency of Giving is Greater than Yearly</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Frequency of Giving</th>
                      <th>Days from Earliest Donation</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);

                    $FOG = get_donation_frequency($row["Email"]); // Calculate frequency of giving

                    //Checks if the current ratio of the Donor is more than yearly if it isn't then their row
                    //won't appear in the generated table
                    if($FOG == "Monthly") {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($formattedPhone) . "</td>
                            <td>" . htmlspecialchars($FOG) . "</td>
                            <td>" . number_format($row['DateDiff']) . "</td>
                            
                          </tr>";
                        }
                }
                
                echo "</table>";
            } else {
                echo "<p>Not enough Donors are available to make the report.</p>";
            }
}

// Report 6: Donors Who Have Donated in the Past Three Years but Not to Events
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
function reportDonorsDonatedNotToEvents($connection) {
            // Modified SQL query to join Donations with Donors table and fetch required details
            //Get current date
            $currentDate = date("Y-m-d");
            //Define the threshold date (two years ago from current date)
			$thresholdDate = date('Y-m-d', strtotime('-3 years', strtotime($currentDate)));

            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                      MIN(DateOfContribution) AS EarliestDonation, ContributionCategory
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    WHERE (d.DateOfContribution > '$thresholdDate')
                          AND ContributedSupportType != 'Fundraising Events'
                    GROUP BY d.Email ";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>Non-Event Donors Who Have Donated in the Past 3 Years</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Earliest Donation</th>
                      <th>Type of Donation</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                    
                    //Checks if the current donor has donated in the past three years if they have then
                    //print. If not then print nothing.
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($formattedPhone) . "</td>
                            <td>" . htmlspecialchars($row['EarliestDonation']) . "</td> 
                            <td>" . htmlspecialchars($row['ContributionCategory']) . "</td>      
                          </tr>";
                        
                }
                
                echo "</table>";
            } else {
                echo "<p>Not enough Donors are available to make the report.</p>";
            }  
}

// Report 7: Donors Who Have Donated in the Past Three Years to Events
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
function reportDonorsDonatedToEvents($connection) {
            // Modified SQL query to join Donations with Donors table and fetch required details
            //Get current date
            $currentDate = date("Y-m-d");
            //Define the threshold date (two years ago from current date)
			$thresholdDate = date('Y-m-d', strtotime('-3 years', strtotime($currentDate)));

            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                      MIN(DateOfContribution) AS EarliestDonation, ContributionCategory
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    WHERE (d.DateOfContribution > '$thresholdDate')
                          AND ContributedSupportType = 'Fundraising Events'
                    GROUP BY d.Email ";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>Event Donors Who Have Donated in the Past 3 Years</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Earliest Donation</th>
                      <th>Event Sponsored</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                    
                    //Checks if the current donor has donated in the past three years if they have then
                    //print. If not then print nothing.
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($formattedPhone) . "</td>
                            <td>" . htmlspecialchars($row['EarliestDonation']) . "</td>
                            <td>" . htmlspecialchars($row['ContributionCategory']) . "</td>      
                          </tr>";
                        
                }
                
                echo "</table>";
            } else {
                echo "<p>Not enough Donors are available to make the report.</p>";
            }  
}

// Report 8: Top X Donors
// Pre-Condition: User is logged in to be able to access report functionality
// Post-Condition: User will be able to look through the report as a generated table and
//                 be able to export the data as a CSV file
// Get the number of top donors from user input, default to 10
// Modify your query to use the $topXDonors variable
function reportTopXDonors($connection, $topXDonors) {
    $topXDonors = isset($_GET['topXDonors']) ? (int)$_GET['topXDonors'] : 10;
    $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, SUM(d.AmountGiven) AS Sum_Of_Donations
            FROM dbdonations AS d
            JOIN dbdonors AS p ON d.Email = p.Email
            GROUP BY d.Email
            ORDER BY Sum_Of_Donations DESC
            LIMIT ?";
    if ($stmt = mysqli_prepare($connection, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $topXDonors);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo "<h2 style='text-align: center;'>List of Top " . htmlspecialchars($topXDonors) . " Donors</h2>";
            echo "<table>";
            echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Sum of Donations</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                $phone = $row['PhoneNumber'];
                $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);

                echo "<tr>
                        <td>" . htmlspecialchars($row['Email']) . "</td>
                        <td>" . htmlspecialchars($row['FirstName']) . "</td>
                        <td>" . htmlspecialchars($row['LastName']) . "</td>
                        <td>" . htmlspecialchars($formattedPhone) . "</td>
                        <td>$" . htmlspecialchars(number_format($row['Sum_Of_Donations'], 2)) . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Not enough Donors are available to make the report.</p>";
        }
    } else {
        echo "<p>Error preparing the query.</p>";
    }
}

function reportDonationStage(){
    //How Donation Stages Work:
    //  1.INTERESTED: If the donor has donated at least once in the past three years
    //  2.DONOR: If the donor has donated at least once a year in the past three years
    //  3.ENGANGED: If the donor has donated at least three times in the last five years
    //  4.LOYAL DONOR: If the donor has donated at least five times in the last five years
    //  5.LEADERSHIP DONOR: If the donor has donated over $10,000 since they became a donor
    //  6.DOESN'T FIT ANY CATEGORY: The donor doesn't fit any of these categories

    // Get all donors
    $donors = get_all_donors();

    if (count($donors) > 0) { // If we have donors, display the report
        echo "<h2 style='text-align: center;'>Donation Stage of Donors</h2>";
        echo "<table>";
        echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Donation Funnel</th></tr>";
        foreach ($donors as $donor) {
            // Get the donor details
            $donor_first_name = $donor->get_first_name();
            $donor_last_name = $donor->get_last_name();
            $donor_email = $donor->get_email();
            $phone = $donor->get_phone();

            // Format the phone number
            $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);

            // get the donor's donations
            $donations = retrieve_donations_by_email($donor_email);

            // If the donor has donations, then determine their donation funnel and display the donor
            if (!empty($donations)) {
                // Get the donor's donation funnel
                $funnel = determine_donation_funnel($donor_email);

                // Table entry for donor
                echo "<tr>
                    <td>" . htmlspecialchars($donor_email) . "</td>
                    <td>" . htmlspecialchars($donor_first_name) . "</td>
                    <td>" . htmlspecialchars($donor_last_name) . "</td>
                    <td>" . htmlspecialchars($formattedPhone) . "</td>
                    <td>" . htmlspecialchars($funnel) . "</td>      
                  </tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>Not enough Donors are available to make the report.</p>";
    }
}

//report 10
        // Report:Donors Retention Rate
        // Pre-Condition: User is logged in to be able to access report functionality
        // Post-Condition: User will be able to look through the report as a generated table and
        //                 be able to export the data as a CSV file
        if (isset($_GET['report']) && $_GET['report'] == 'report10') {
            
            echo "<h2>Donors Retention Rate Calculator</h2>
                <form method='post' action='reportsDonorsPage.php'>
                    <label for='prev_year'>Previous Year:</label>
                    <input type='number' id='prev_year' name='prev_year' required min='2000' max='2023' style='color:white;'><br><br>
                    <label for='current_year'>Current Year:</label>
                    <input type='number' id='current_year' name='current_year' required min='2001' max='2024'  style='color:white;'><br><br>
                    <input type='submit' value='Submit'>
                </form>";
         
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Define the date range (you can adjust the interval as needed)
        
        $prev_year = $_POST["prev_year"];
        $current_year = $_POST["current_year"];

        echo "previous year. $prev_year <br> current year. $current_year <br>";

            
        // Calculate the number of donors in the previous period
        $sql_prev_period = "SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$prev_year-01-01' AND '$prev_year-12-31'";
        $result_prev_period = $connection->query($sql_prev_period);
        $num_donors_prev_period = $result_prev_period->num_rows;

        // Calculate the number of donors in the current period
        $sql_current_period = "SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$current_year-01-01' AND '$current_year-12-31'";
        $result_current_period = $connection->query($sql_current_period);
        $num_donors_current_period = $result_current_period->num_rows;

        // Calculate the number of retained donors (donors who contributed in both periods)
        $sql_retained_donors = "SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$prev_year-01-01' AND '$prev_year-12-31' AND DonorID IN (SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$current_year-01-01' AND '$current_year-12-31')";
        $result_retained_donors = $connection->query($sql_retained_donors);
        $num_retained_donors = $result_retained_donors->num_rows;

            // Calculate donor retention rate
            if ($num_donors_prev_period > 0) {
                $retention_rate = ($num_retained_donors / $num_donors_prev_period) * 100;
            } else {
                $retention_rate = 0; // Default to 0 if no donors in the previous period
            }


        
        // Display the donor retention rate
        echo "<table>";
        echo "<tr><th>Donors this year</th><th>Total donors last year</th><th>Retained Donors</th><th>Donor Retention Rate:</th></tr>";
        echo "<tr>
        <td>" . htmlspecialchars($num_donors_current_period) . "</td>
        <td>" . htmlspecialchars($num_donors_prev_period) . "</td>
        <td>" . htmlspecialchars($num_retained_donors) . "</td>
        <td>" . htmlspecialchars(round($retention_rate, 2)) . "%"."</td>
             
      </tr>";
        
        }
    }




    //End of report 

function displayTopDonorsForm($currentValue) {
    echo "<form action='' method='get'>
            <input type='hidden' name='report' value='report8'>
            <label for='topXDonors'>Enter number of top donors to display:</label>
            <input type='number' id='topXDonors' name='topXDonors' value='{$currentValue}' min='1'>
            <input type='submit' value='Update Report'>
          </form>";
}


?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <?php require_once('header.php') ?>
        <title>BigBrotherBigSister | Report Result</title>
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
          
            tr:nth-child(even) {
                background-color: black;
                /* color:var(--button-font-color); */
		
            }

            @media print {
                tr:nth-child(even) {
                    background-color: white;
                }

                button, header {
                    display: none;
                }

                :root {
                    font-size: 10pt;
                }

                label {
                    color: black;
                }

                table {
                    width: 100%;
                }

                a {
                    color: black;
                }
            }

            .theB{
                width: auto;
                font-size: 15px;
            }
	        .center_a {
                margin-top: 3rem; /* Adjusted from 0 or a smaller value to 3rem */
                margin-bottom: 3rem;
                margin-left:auto;
                margin-right:auto;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .8rem;
            }
            .center_b {
                margin-top: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
		        gap: .8rem;
            }
            #back-to-top-btn {
                bottom: 20px;
            }
            .back-to-top:visited {
                color: white; /* sets the color of the link when visited */  
            }
            .back-to-top {
                color: white; /* sets the color of the link when visited */  
            }
	        .intro {
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 0 0;
            }
            .export-btn {
                padding: 10px 20px; /* Adjust padding as needed */
                display: inline-block; /* Add this to make the button only as wide as its content plus padding */
                margin-top: 20px; /* This will add space between the table and the button */
                max-width: 200px;
            }
            .export-form {
                text-align: center;
                margin-top: 20px; /* Add top margin to increase space between the table and the form */
            }
            
            /* Targeting the select element and option elements */
            select, option, input {
                color: white; /* Setting the font color to white */
                background-color: #333; /* A darker background for contrast */
            }
	    @media only screen and (min-width: 1024px) {
                .intro{
                    width: 80%;
                }
                main.report {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
            }
        footer {
            margin-bottom: 2rem;
        }
    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    </head>
    <body>
        <?php require_once('header.php') ?>
        <div class="report-container">
        <?php
        if (isset($_GET['report'])) {
            switch ($_GET['report']) {
                case 'report1':
                    reportDonorsOver10K($connection);
                    break;
                case 'report2':
                    reportFrequencyOfGiving($connection);
                    break;
                case 'report3':
                    reportDonorsNotDonatedIn2Years($connection);
                    break;
                case 'report4':
                    reportEventsDonorsContributed($connection);
                    break;
                case 'report5':
                    reportFrequencyGreaterThanYearly($connection);
                    break;
                case 'report6':
                    reportDonorsDonatedNotToEvents($connection);
                    break;
                case 'report7':
                    reportDonorsDonatedToEvents($connection);
                    break;
                case 'report8':
                    displayTopDonorsForm(isset($_GET['topXDonors']) ? (int)$_GET['topXDonors'] : 10); // Display the form with the current or default value
                    $topXDonors = isset($_GET['topXDonors']) ? (int)$_GET['topXDonors'] : 10;
                    reportTopXDonors($connection, $topXDonors);
                    break;
                case 'report9':
                    reportDonationStage();
                    break;
                default:
                    echo "Unknown report requested.";
                    break;
            }
        }
        ?>
    </div>
    <div class="export-container">
        <?php
            if (isset($_GET['report']) && $_GET['report'] != 'report4') {
                // The common part of your form, assuming 'action' might be the same for all
                $formHtml = "<form action='reportsExport.php' method='post' class='export-form'>";

                switch ($_GET['report']) {
                    case 'report1':
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_over_10000'>";
                        break;
                    case 'report2':
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_FOG'>";
                        break;
                    case 'report3':
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_less_2_years'>";
                        break;
                    case 'report5':
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_FOG_GTY'>";
                        break;
                    case 'report6':
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_L3YNE'>";
                        break;
                    case 'report7':
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_L3YE'>";
                        break;
                    case 'report8':
                        $topXDonorsValue = isset($_GET['topXDonors']) ? (int)$_GET['topXDonors'] : 10; // Default to 10 if not set
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_T10'>
                                        <input type='hidden' name='topXDonors' value='" . htmlspecialchars($topXDonorsValue) . "'>";
                        break;
                    case 'report9':
                        $formHtml .= "<input type='hidden' name='action' value='export_donors_DSF'>";
                        break;
                    }
                    $formHtml .= "<input type='submit' value='Export Donors' class='export-btn'></form>";
                    echo $formHtml;
            }
        ?>
    </div>
    <div class="center_a">
        <a href="report.php">
            <button class="theB">New Report</button>
        </a>
        <a href="index.php">
            <button class="theB">Home Page</button>
        </a>
    </div>
</body>
</html>