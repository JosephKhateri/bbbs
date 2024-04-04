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
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Frequency of Giving</th>
                      <th>Days from Earliest Donation</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                    
                    //Frequency of Giving
                    $FOG="";
                    //echo $row['DateDiff']. "d<br>";
                    if($row['DateDiff']==NULL){
                        $row['DateDiff']=0;
                        $ratio=0;
                    }else{
                    $ratio = $row['Number_Of_Donations'] / ($row['DateDiff'] / 365);
                    }
                    
                    if($ratio==0){
                        $FOG="Not Enough Data";
                    }elseif($ratio<6 && $ratio>=1){
                        $FOG="Yearly";
                    }elseif($ratio>=12){
                        $FOG="Monthly";
                    } else {
                        $FOG = "Sporadic";
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
                    
                    //Frequency of Giving
                    $FOG = "";
                    $Rate=0;
                    if($row['DateDiff']==NULL){
                        $row['DateDiff']=0;
                        $ratio=0;
                    }else{
                    $ratio = $row['Number_Of_Donations'] / ($row['DateDiff'] / 365);
                    }
                    if($ratio==0){
                        $FOG="Not Enough Data";
                    }elseif ($ratio<1 && $ratio>0){
                        $FOG="Less Than Yearly";
                    } elseif($ratio < 6 && $ratio >= 1){
                        $FOG = "Yearly";
                    } elseif($ratio >= 6 && $ratio < 12){ // Either comment this out for now or remove it since bi-monthly isn't needed
                        $FOG = "Bi-Monthly";
                        $Rate=1;
                    } elseif($ratio >= 12){
                        $FOG = "Monthly";
                        $Rate=1;
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

function reportDonationStage($connection){
            //How Donation Stages Work:
            //  1.INTERESTED: If the donor has donated at least once in the past three years  
            //  2.DONOR: If the donor has donated at least once a year in the past three years 
            //  3.ENGANGED: If the donor has donated at least three times in the last five years
            //  4.LOYAL DONOR: If the donor has donated at least five times in the last five years
            //  5.LEADERSHIP DONOR: If the donor has donated over $10,000 since they became a donor
            //  6.DOESN'T FIT ANY CATEGORY: The donor doesn't fit any of these categories
            
            //Represents what category a donor falls into
            $type=6;

            //Array that holds an array of each donor
            $donorArray= array();

            //Get current date
            $currentDate = date("Y-m-d");
            
            //Define the threshold date (five years ago from current date)
            $thresholdDate3 = date('Y-m-d', strtotime('-3 years', strtotime($currentDate)));
            
            //First Query sets each Donor as type 6
            $query = "SELECT d.Email
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            //Give each Donor the DOESN'T FIT ANY CATEGORY type
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $donor_name=$row['Email'];
                    $type=6;
                    //Makes a Key Value pair for that Donor to have that type and push it onto array
                    //Whether they are INTERESTED or DONOR they are pushed to the array
                    $donorArray[$donor_name]=$type;
                }
            }

            //Second Query to check for type 1 and 2 donors in the last three years
            $query = "SELECT d.Email, COUNT(d.email) AS Number_Of_Donations
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    WHERE (d.DateOfContribution > '$thresholdDate3')
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            //  1.INTERESTED: If the donor has donated at least once in the past three years  
            //  2.DONOR: If the donor has donated at least once a year in the past three years 

            // Check for all the Donors in the Last Three Years
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $donor_name=$row['Email'];
                    $don_num=$row['Number_Of_Donations'];

                    //echo $donor_name.": ".$don_num."<br>";

                    if($don_num==1 || $don_num==2){
                        $type=1;//Donor counts as INTERESTED
                    }else{
                        $type=2;//Donor counts as DONOR
                    } 
                         
                    //Makes a Key Value pair for that Donor to have that type and push it onto array
                    //Whether they are INTERESTED or DONOR they are pushed to the array
                    $donorArray[$donor_name]=$type;
                }
            }

            //Define the threshold date (five years ago from current date)
            $thresholdDate5 = date('Y-m-d', strtotime('-5 years', strtotime($currentDate)));
            
            //Third Query to find type 3 and 4 Donors
            $query = "SELECT d.Email, COUNT(d.email) AS Number_Of_Donations
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    WHERE (d.DateOfContribution > '$thresholdDate5')
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            //  3.ENGANGED: If the donor has donated at least three times in the last five years
            //  4.LOYAL DONOR: If the donor has donated at least five times in the last five years
            
            // Check for all the Donors in the Last Three Years
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //Name of Donor
                    $donor_name=$row['Email'];
                    $don_num=$row['Number_Of_Donations'];
                    
                    //echo $donor_name.": ". $row['Number_Of_Donations'] ."<br>";

                    if ($don_num==3 || $don_num==4){
                        $type=3;//Donor counts as ENGAGED
                    } elseif($don_num>=5){
                        $type=4;//Donor counts as LOYAL DONOR
                    }

                    //Makes a Key Value pair for that Donor to have that type and push it onto array
                    //Whether they are ENGAGED or LOYAL DONOR
                    
                    //In a very specific scenario if someone had donated once or twice in the past 5 years
                    //but didn't donate anymore then they could get by the if else statement and get the
                    //type of the previous donor so this if statement is here to stop that
                    if($don_num>=2){
                    $donorArray[$donor_name]=$type;
                    }
                }
            }

            //Fourth Query to check for type 5 donors
            $query = "SELECT d.Email, SUM(AmountGiven) AS Sum_Of_Donations
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email
                    HAVING Sum_Of_Donations >=10000";
            $result = mysqli_query($connection, $query);

            //  5.LEADERSHIP DONOR: If the donor has donated over $10,000 since they became a donor
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    //Name of Donor
                    $donor_name=$row['Email'];
    
                    //echo $donor_name.": ". $row['Sum_Of_Donations'] . "<br>";

                    //Donor has donated more than $10,000 in their time as a donor
                    $type=5;
    
                    //Makes a Key Value pair for that Donor to have that type and push it onto array
                    $donorArray[$donor_name]=$type;
                }
            }

            //5th query to get all the donor's information and then display their Donation Stage
            $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations,
                      DATEDIFF( CURRENT_DATE(), MIN(DateOfContribution)) AS DateDiff, SUM(AmountGiven) AS Sum_Of_Donations
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email";
            $result = mysqli_query($connection, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>Donation Stage of Donors</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Donation Stage</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    //Name of Donor
                    $donor_name=$row['Email'];
                    
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                    
                    $type=$donorArray[$donor_name];
                    
                    //echo $donor_name. ": " . $type . "<br>";
                    
                    $Donation_Stage="Doesn't Fit Any Category";
                    if($type==1){
                        $Donation_Stage="Interested Donor";
                    }else if($type==2){
                        $Donation_Stage="Donor";
                    }else if($type==3){
                        $Donation_Stage="Engaged Donor";
                    }else if($type==4){
                        $Donation_Stage="Loyal Donor";
                    }else if($type==5){
                        $Donation_Stage="Leadership Donor";
                    } 
                    
                    //Checks if the current donor has donated in the past three years if they have then
                    //print. If not then print nothing.
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($formattedPhone) . "</td>
                            <td>" . htmlspecialchars($Donation_Stage) . "</td>      
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Not enough Donors are available to make the report.</p>";
            }
}

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
                    reportDonationStage($connection);
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