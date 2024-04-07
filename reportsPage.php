<?php 
/**
 * @version April 6, 2023
 * @author Alip Yalikun
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
  
  
  $connection = connect();
  /*
  if(isset($_GET['animal'])){
    $selected_animal_name = $_GET['animal'];
    
    $query = "select * from dbAnimals where name = '$selected_animal_name'";
    $result = mysqli_query($connection, $query);
    $animal_info = mysqli_fetch_assoc($result);
    } else {
        echo "No animal selected!";
    }*/
  
  $donorsOver10K = [];
  $donationQuery = "SELECT Email, SUM(AmountGiven) as TotalDonation FROM dbdonations GROUP BY Email HAVING TotalDonation > 10000";
  $donationResult = mysqli_query($connection, $donationQuery);
  while($row = mysqli_fetch_assoc($donationResult)) {
      $donorsOver10K[] = $row;
  }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
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
    
        
    <section>
            <?php
        // Check if the 'report' GET parameter is set to 'report1'
        if (isset($_GET['report']) && $_GET['report'] == 'report1') {
            // Modified SQL query to join Donations with Donors table and fetch required details
            $query = "SELECT d.Email, SUM(d.AmountGiven) AS TotalDonation, p.FirstName, p.LastName, p.PhoneNumber 
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email
                    HAVING TotalDonation > 10000";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>List of Donors Who Donated Over $10,000</h2>";
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
        // Check if the 'report' GET parameter is set to 'report2'
        if (isset($_GET['report']) && $_GET['report'] == 'report2') {
            // Modified SQL query to join Donations with Donors table and fetch required details
            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                             DATEDIFF( CURRENT_DATE(), MIN(DateOfContribution)) AS DateDiff  
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>List of Donor's Frequency of Giving</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Frequency of Giving</th>
                      <th>Days from Earliest Donation</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                    
                    //Frequency of Giving
                    $FOG="";
                    $ratio=$row['Number_Of_Donations']/($row['DateDiff']/365);
                    if($ratio<1){
                        $FOG="Less Than Yearly";
                    }elseif($ratio<6 && $ratio>=1){
                        $FOG="Yearly";
                    }elseif($ratio>=6 && $ratio<12){
                        $FOG="Bi-Monthly";
                    }elseif($ratio>=12){
                        $FOG="Monthly";
                    }

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
		// Report: Every Donor's Frequency of Giving
        // Pre-Condition: User is logged in to be able to access report functionality
        // Post-Condition: User will be able to look through the report as a generated table and
        //                 be able to export the data as a CSV file
        if (isset($_GET['report']) && $_GET['report'] == 'report3') {
            // Modified SQL query to join Donations with Donors table and fetch required details
            // Get the current date
				$currentDate = date("Y-m-d");

				// Define the threshold date (two years ago from current date)
				$thresholdDate = date('Y-m-d', strtotime('-2 years', strtotime($currentDate)));

				$query = "SELECT d.FirstName, d.LastName, d.Email, dd.DateOfContribution, dd.AmountGiven
						FROM DbDonors d
						LEFT JOIN DbDonations dd ON d.Email = dd.Email
						WHERE dd.DateOfContribution IS NULL 
						  OR dd.DateOfContribution < '$thresholdDate'
						GROUP BY d.Email
						ORDER BY d.LastName";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>List of Donors Who have not Donated for the last 2 years</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Date</th><th>Amount Donated</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                     
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($row['DateOfContribution']) . "</td>
                            <td>$" . number_format($row['AmountGiven']) . "</td>
                          </tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>All donors have contributed in the last 2 years.</p>";
            }
        }
        //  Report: Events Donor has Sponsered 
        if (isset($_GET['report']) && $_GET['report'] == 'report4') {
            // Fetch your data for the pie chart here
            $categoryQuery = "SELECT ContributionCategory, SUM(AmountGiven) AS TotalAmount FROM dbdonations GROUP BY ContributionCategory";
            $categoryResult = mysqli_query($connection, $categoryQuery);
            $categories = [];
            while($row = mysqli_fetch_assoc($categoryResult)) {
                $categories[] = $row;
            }/*
            // Pass the PHP array to JavaScript
            echo "<script>var categoryData = " . json_encode($categories) . ";</script>";
            echo "<h2 style='text-align: center;margin-top: 30px;margin-bottom: 20px'>Events Donors Have Contributed To</h2>";
            // Include the Google Charts loader and the pie chart drawing script
            echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
            echo '<script type="text/javascript">
                    google.charts.load("current", {"packages":["corechart"]});
                    google.charts.setOnLoadCallback(drawChart);
                    
                    function drawChart() {
                        var data = new google.visualization.DataTable();
                        data.addColumn("string", "Category");
                        data.addColumn("number", "Amount");
                        categoryData.forEach(function(category) {
                            data.addRow([category.ContributionCategory, parseFloat(category.TotalAmount)]);
                        });
        
                        var options = {
                            title: "Donation Contribution Categories",
                            is3D: true,
                        };
        
                        var chart = new google.visualization.PieChart(document.getElementById("piechart"));
                        chart.draw(data, options);
                    }
                  </script>';
        
            // Output the container for the pie chart
            echo '<div id="piechart" style="width: 1200px; height: 700px; margin: auto;"></div>';*/

        }
		// Report:Frequncy of Giving Greater than Yearly
        // Pre-Condition: User is logged in to be able to access report functionality
        // Post-Condition: User will be able to look through the report as a generated table and
        //                 be able to export the data as a CSV file
        if (isset($_GET['report']) && $_GET['report'] == 'report5') {
            // Modified SQL query to join Donations with Donors table and fetch required details
            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                    DATEDIFF( CURRENT_DATE(), MIN(DateOfContribution)) AS DateDiff  
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>List of Donors whose Frequency of Giving is greater than yearly</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Frequency of Giving</th>
                      <th>Days from Earliest Donation</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                    
                    //Frequency of Giving
                    $FOG = "";
                    $Rate = 0;
                    $ratio = $row['Number_Of_Donations'] / ($row['DateDiff'] / 365);
                    if ($ratio<1){
                        $FOG="Less Than Yearly";
                    } elseif($ratio < 6 && $ratio >= 1){
                        $FOG = "Yearly";
                    } elseif($ratio >= 6 && $ratio < 12){ // Either comment this out for now or remove it since bi-monthly isn't needed
                        $FOG = "Bi-Monthly";
                        $Rate = 1;
                    } elseif($ratio >= 12){
                        $FOG = "Monthly";
                        $Rate = 1;
                    }
                    //Checks if the current ratio of the Donor is more than yearly if it isn't then their row
                    //won't appear in the generated table
                    if($Rate == 1){
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
        // Report:Donors who have donated in the past Three Years
        // Pre-Condition: User is logged in to be able to access report functionality
        // Post-Condition: User will be able to look through the report as a generated table and
        //                 be able to export the data as a CSV file
        if (isset($_GET['report']) && $_GET['report'] == 'report6') {
            // Modified SQL query to join Donations with Donors table and fetch required details
            //Get current date
            $currentDate = date("Y-m-d");
            //Define the threshold date (two years ago from current date)
			$thresholdDate = date('Y-m-d', strtotime('-3 years', strtotime($currentDate)));

            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                      MIN(DateOfContribution) AS EarliestDonation
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    WHERE d.DateOfContribution IS NULL 
						  OR d.DateOfContribution > '$thresholdDate'
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>List of Donors who have donated in the past three Years</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Earliest Donation</th></tr>";
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
                          </tr>";
                        
                }
                
                echo "</table>";
            } else {
                echo "<p>Not enough Donors are available to make the report.</p>";
            }
        }

        //report 7
        // Report:Donors Retention Rate
        // Pre-Condition: User is logged in to be able to access report functionality
        // Post-Condition: User will be able to look through the report as a generated table and
        //                 be able to export the data as a CSV file
        if (isset($_GET['report']) && $_GET['report'] == 'report7') {
            ?>
                <h2>Donors Retention Rate Calculator</h2>
                    <form method="POST" action="reportsDonorsPage.php">
                        <label for="prev_year">Previous Year:</label>
                        <input type="number" id="prev_year" name="prev_year" required min="2000" max="2023" style="color:white;"><br><br>
                        <label for="current_year">Current Year:</label>
                        <input type="number" id="current_year" name="current_year" required min="2001" max="2024"  style="color:white;"><br><br>
                        <input type="submit" value="Submit">
                    </form>
            <?php
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

                /*echo  "number of in the current year: " . $num_donors_current_period . "<br>";
                echo "number of retained donors: " . $num_retained_donors . "<br>";
                echo "number of previous donors". $num_donors_prev_period . "<br>";

                echo "Donor Retention Rate for 2024 compared to 2023: " . $retention_rate . "%";*/

            
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
        if (isset($_GET['report']) && $_GET['report'] == 'report1'){
            echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='export_donors_over_10000'>
            <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }
        if (isset($_GET['report']) && $_GET['report'] == 'report2'){
            echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='export_donors_FOG'>
            <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }
		if (isset($_GET['report']) && $_GET['report'] == 'report3'){
            echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='export_donors_less_2_years'>
            <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }
        if (isset($_GET['report']) && $_GET['report'] == 'report5'){
            echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='export_donors_FOG_GTY'>
            <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }
        if (isset($_GET['report']) && $_GET['report'] == 'report6'){
            echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='export_donors_L3Y'>
            <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }

        if (isset($_GET['report']) && $_GET['report'] == 'report7'){
            echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='retention_report'>
            <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }
<<<<<<< Updated upstream
=======
        if (isset($_GET['report']) && $_GET['report'] == 'report8'){
            // Assuming you want to dynamically set the value of topXDonors based on user input
            // For example, if you previously captured this value and stored it in a session or in a variable
            // Ensure to validate and sanitize this value properly to avoid injection attacks or logical errors
            $topXDonorsValue = isset($_GET['topXDonors']) ? (int)$_GET['topXDonors'] : 10; // Default to 10 if not set
            echo "<form action='reportsExport.php' method='post' class='export-form'>
                <input type='hidden' name='action' value='export_donors_T10'>
                <input type='hidden' name='topXDonors' value='" . htmlspecialchars($topXDonorsValue) . "'>
                <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }
        if (isset($_GET['report']) && $_GET['report'] == 'report10'){
            echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='export_donors_L3YE'>
            <input type='submit' value='Export Donors' class='export-btn'>
            </form>";
        }
        
        
>>>>>>> Stashed changes
        ?>

    </section>



	
    </main>
	<div class="center_a">
                <a href="report.php">
                <button class = "theB">New Report</button>
                </a>
                <a href="index.php">
                <button class = "theB">Home Page</button>
                </a>
	</div>
        </main>
    </body>
</html>
