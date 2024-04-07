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
                    // Define the date range (you can adjust the interval as needed)
                    
                    $prev_year = $_POST["prev_year"];
                    $current_year = $_POST["current_year"];

                   
<<<<<<< Updated upstream
                    
         
                        
                    // Calculate the number of donors in the previous period
                    $sql_prev_period = "SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$prev_year-01-01' AND '$prev_year-12-31'";
                    $result_prev_period = $connection->query($sql_prev_period);
                    $num_donors_prev_period = $result_prev_period->num_rows;
                    
                    // Calculate the number of donors in the current period
                    $sql_current_period = "SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$current_year-01-01' AND '$current_year-12-31'";
=======
                   
         
                        
                    // Calculate the number of donors in the previous period
                    $sql_prev_period = "SELECT DISTINCT Email FROM dbdonations WHERE DateOfContribution BETWEEN '$prev_year-01-01' AND '$prev_year-12-31'";
                    $result_prev_period = $connection->query($sql_prev_period);

                    $result_prev_period = $connection->query($sql_prev_period);
        
                    if ($result_prev_period) {
                    $num_donors_prev_period = $result_prev_period->num_rows;
                    } else {
                    // Handle the query failure
                    echo "Error: Unable to execute query. " . $connection->error;
                    }

                    $num_donors_prev_period = $result_prev_period->num_rows;
                    
                    // Calculate the number of donors in the current period
                    $sql_current_period = "SELECT DISTINCT Email FROM dbdonations WHERE DateOfContribution BETWEEN '$current_year-01-01' AND '$current_year-12-31'";
>>>>>>> Stashed changes
                    $result_current_period = $connection->query($sql_current_period);
                    $num_donors_current_period = $result_current_period->num_rows;

                    // Calculate the number of retained donors (donors who contributed in both periods)
<<<<<<< Updated upstream
                    $sql_retained_donors = "SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$prev_year-01-01' AND '$prev_year-12-31' AND DonorID IN (SELECT DISTINCT DonorID FROM dbdonations WHERE DateOfContribution BETWEEN '$current_year-01-01' AND '$current_year-12-31')";
=======
                    $sql_retained_donors = "SELECT DISTINCT Email FROM dbdonations WHERE DateOfContribution BETWEEN '$prev_year-01-01' AND '$prev_year-12-31' AND Email IN (SELECT DISTINCT Email FROM dbdonations WHERE DateOfContribution BETWEEN '$current_year-01-01' AND '$current_year-12-31')";
>>>>>>> Stashed changes
                    

                    $result_retained_donors = $connection->query($sql_retained_donors);
                    $num_retained_donors = $result_retained_donors->num_rows;

<<<<<<< Updated upstream
                        // Calculate donor retention rate
                        if ($num_donors_prev_period > 0) {
                            $retention_rate = ($num_retained_donors / $num_donors_prev_period) * 100;
                        } else {
                            $retention_rate = 0; // Default to 0 if no donors in the previous period
                        }

                    
                    
=======
                    // Calculate donor retention rate
                    if ($num_donors_prev_period > 0) {
                        $retention_rate = ($num_retained_donors / $num_donors_prev_period) * 100;
                    } else {
                        $retention_rate = 0; // Default to 0 if no donors in the previous period
                    }

>>>>>>> Stashed changes
                    //Select retained donor's info
 
                
                    $sql_retained_donors_info = "SELECT dnr.FirstName, dnr.LastName, dnr.Email, dnr.PhoneNumber, dnr.Company
                    FROM dbdonors dnr
                    INNER JOIN (
                      SELECT DISTINCT Email
                      FROM dbdonations
                      WHERE DateOfContribution BETWEEN '$prev_year-01-01' AND '$prev_year-12-31'
                      AND Email IN (
                        SELECT DISTINCT Email
                        FROM dbdonations
                        WHERE DateOfContribution BETWEEN '$current_year-01-01' AND '$current_year-12-31'
                      )
                    ) AS retained_donors ON dnr.Email = retained_donors.Email;";

                    
                    $result_retained_donor_info = mysqli_query($connection, $sql_retained_donors_info);
                echo "</table>";
                        

                        echo "Donor's last year &nbsp".($num_donors_prev_period)."<br>Donor's this year &nbsp".($num_donors_current_period)."<br>Retained Donors &nbsp".$num_retained_donors;
                    // Display the donor retention rate
                    if (mysqli_num_rows($result_retained_donor_info) > 0) {
                    echo "<h2 style='text-align: center;'>Retained Members</h2>";
                    echo "<table>";
                    echo "<tr><th>First Name</th><th> Second Name </th><th> Email</th><th>Phone Number</th><th>Company</th></tr>";
                    while($row = mysqli_fetch_assoc($result_retained_donor_info)) {
                        echo "<tr>";
                        echo "<td>" . $row["FirstName"] . "</td>";
                        echo "<td>" . $row["LastName"] . "</td>";
                        echo "<td>" . $row["Email"] . "</td>";
                        echo "<td>". $row["PhoneNumber"] . "</td>";
                        echo "<td>". $row["Company"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                  echo "No retained donors found.";
                }

                  //End of report 
                   echo "<form action='reportsExport.php' method='post' class='export-form'>
            <input type='hidden' name='action' value='export_donors_retention'>
            
            <input type='submit' value='Export Retention' class='export-btn'>
            </form>";

            //End of report 

            
        ?>
    </section>
    <div class="center_a">
                <a href="report.php">
                <button class = "theB">New Report</button>
                </a>
                <a href="index.php">
                <button class = "theB">Home Page</button>
                </a>
    </div>