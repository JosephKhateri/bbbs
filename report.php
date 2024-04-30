<?php

    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    require_once('include/input-validation.php');
    require_once ('include/api.php');
    include_once('database/dbinfo.php');

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
        redirect('login.php');
        die();
    }
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

    <form class="report_select" method="get" action="newReportsPage.php">
                <div>
                    <label for="report">Select Report</label>
                    <select id="report" name="report" required>
                        <option value="report1">Donors Who Have Donated Over $10,000</option>
                        <option value="report2">Every Donor's Frequency of Giving</option>
                        <option value="report3">Donors Who Have Not Contributed For the Last 2 Years</option>
                        <option value="report4">Events Contributed</option>
                        <option value="report5">Donors Whose Frequency of Giving is Greater Than Yearly</option>
                        <option value="report6">Non-Event Donors Who Have Donated in the Past Three Years</option>
                        <option value="report7">Event Donors Who Have Donated in the Past Three Years</option>
                        <option value="report8">Top X Donors</option>
                        <option value="report9">Donors' Donation Funnels</option>
                        <option value="report10">Retention Rate</option>
                    </select><br/>
                </div>
                <input type="submit" name="submit_click" value="Submit">
            </form>
    </main>

    </body>

</html>
