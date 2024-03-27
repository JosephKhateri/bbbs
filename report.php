<?php 

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

    // get animal data from database for form
    // Connect to database
    include_once('database/dbinfo.php'); 
    $con=connect();  
    // Get all the animals from animal table
    $sql = "SELECT * FROM `dbAnimals`";
    $all_animals = mysqli_query($con,$sql); 
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>BBBS | Reports</title>
        <style>
            .report_select{
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 4rem 0;
            }
            @media only screen and (min-width: 1024px) {
                .report_select {
                    /* width: 40%; */
                    width: 35rem;
            }
            main.report {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
	    .column {
		padding: 0 4rem 0 0;
		width: 50%;
	    }
	    .row{
          	display: flex;
            }
	    }
	    .hide {
  		display: none;
	    }

	    .myDIV:hover + .hide {
		display: block;
  		color: red;
	    }
        select, option {
        color: white; /* This sets the font color to white */
        }

        /* To ensure the background color of the dropdown is not white and provides enough contrast to see the white text */
        select {
            background-color: #333; /* A darker background for contrast */
        }
        </style>
    </head>
    <body>
        <?php require_once('header.php');?>
	<h1>Business and Operational Reports</h1>

    <main class="report">
	<?php
	    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_click"])) {
		$args = sanitize($_POST);
		$report = $args['report_type'];
		$name = $args['name'];
        }
	    ?>
        
	<h2>Generate Report</h2>
	<br>

    <form class="report_select" method="get" action="reportsPage.php">
                <div>
                    <label for="report">Select Report</label>
                    <select id="report" name="report" required>
                        <option value="report1">Donors who have donated over $10,000</option>
                        <option value="report2">Every Donor's Frequency of Giving</option>
                        <option value="report3">Donors who have not contributed for the last 2 years</option>
                        <option value="report4">Events Contributed</option>
                        <option value="report5">Donors whose Frequency of Giving is bigger than yearly</option>
                        <option value="report6">Donors who have donated in the past three Years and haven't donated to an Event</option>
                        <option value="report7">Donors who have donated in the past three Years and have Donated to an Event</option>
                        <option value="report8">Top X Donors</option>
                    </select><br/>
                </div>
                <input type="submit" name="submit_click" value="Submit">
            </form>
    </main>

    </body>

</html>
