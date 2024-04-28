<?php
    /*
     * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook,
     * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan,
     * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker.
     * This program is part of RMH Homebase, which is free software.  It comes with
     * absolutely no warranty. You can redistribute and/or modify it under the terms
     * of the GNU General Public License as published by the Free Software Foundation
     * (see <http://www.gnu.org/licenses/ for more information).
     *
     */

    /*
     * dataSearch page for RMH homebase.
     * @author Johnny Coster
     * @version April 2, 2012
     */


    /**
         * Reviewed by Zack
         * Program Specifications/Correctness - Excellent
         * Readability - Good
         * Code Efficiency - Excellent
         * Documentation - Adequate
         * Assigned Task - Excellent
         */



    // Disable error display, log errors instead
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', 'path/to/error.log'); // Specify the error log file

    ob_start(); // Start output buffering
    if (session_status() == PHP_SESSION_NONE) {
        session_cache_expire(30); // Optional: Set session cache expire time if needed
        session_start();
    }
    //session_start();
    //session_cache_expire(30);

    require_once('database/dbinfo.php');
    require_once('database/dbDonations.php');
    require_once('database/dbDonors.php');
    require_once('domain/Donor.php');
    require_once('domain/Donation.php');
?>
<html>
<head>
<title>Search for data objects</title>
<link rel="stylesheet" href="styles.css" type="text/css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

</head>
<body>
<div id="container"><?php include_once('header.php'); ?>
<div id="content"><?php
include_once('domain/Person.php');
include_once('database/dbPersons.php');

if ($_POST['_form_submit'] != 1 && $_POST['_form_submit'] != 2 && $_POST['_form_submit'] != 3)
include('dataSearch.inc.php'); // the form has not been submitted, so show it

//User has decided to export a Report and now all these if statements are checking
//which report it is and using the appropiate method for the specific report.
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_over_10000') {
	ob_end_clean();
    exportDonorsOver10000();
	exit();
}
//FOG=Frequency of Giving
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_FOG') {
	ob_end_clean();
    exportDonorsFOG();
	exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'export_donors_less_2_years') {
	ob_end_clean();
    exportDonorsLessThanTwoYears();
	exit();
}

//FOG_GTY=Frequncy of Giving Greater Than Yearly
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_FOG_GTY') {
	ob_end_clean();
    exportDonorsFOGGTY();
	exit();
}
//L3Y=Less Than 3 Years No Events
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_L3YNE') {
	ob_end_clean();
    exportDonorsL3YNE();
	exit();
}
//L3Y=Less Than 3 Years Events
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_L3YE') {
	ob_end_clean();
    exportDonorsL3YE();
	exit();
}
//T10=Top 10 Donors
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_T10') {
	ob_end_clean();
    exportDonorsT10();
	exit();
}
//DSF= Donation Stage Funnel
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_DSF') {
    ob_end_clean();
    exportDonorsDSF();
    exit();
}
//RR=Retention Rate
if (isset($_POST['action']) && $_POST['action'] == 'export_donors_RR') {
	ob_end_clean();
    exportDonorsRR();
	exit();
}

process_form();
//pull_shift_data();
include('footer.inc');

function process_form() {

	if ($_POST['_form_submit'] == 1) {
		error_log("exporting data step 1");
		$select_all_regexp = ".";
		if(!isset($_POST['first_name']) || $_POST['first_name'] == "") $_SESSION['first_name'] = $select_all_regexp;
		else $_SESSION['first_name'] = $_POST['first_name'];

		if(!isset($_POST['last_name']) || $_POST['last_name'] == "") $_SESSION['last_name'] = $select_all_regexp;
		else $_SESSION['last_name'] = $_POST['last_name'];

		if(!isset($_POST['gender']) || $_POST['gender'] == "") $_SESSION['gender'] = $select_all_regexp;
		else $_SESSION['gender'] = $_POST['gender'];

		if(!isset($_POST['type'])) $_SESSION['type'] = array();
		else $_SESSION['type'] = $_POST['type'];

		if(!isset($_POST['status']) || $_POST['status'] == "") $_SESSION['status'] = $select_all_regexp;
		else $_SESSION['status'] = $_POST['status'];

		if(!isset($_POST['start_date']) || $_POST['start_date'] == "") $_SESSION['start_date'] = $select_all_regexp;
		else $_SESSION['start_date'] = $_POST['start_date'];

		if(!isset($_POST['city']) || $_POST['city'] == "") $_SESSION['city'] = $select_all_regexp;
		else $_SESSION['city'] = $_POST['city'];

		if(!isset($_POST['zip']) || $_POST['zip'] == "") $_SESSION['zip'] = $select_all_regexp;
		else $_SESSION['zip'] = $_POST['zip'];

		if(!isset($_POST['phone']) || $_POST['phone'] == "") $_SESSION['phone'] = $select_all_regexp;
		else $_SESSION['phone'] = $_POST['phone'];

		if(!isset($_POST['email']) || $_POST['email'] == "") $_SESSION['email'] = $select_all_regexp;
		else $_SESSION['email'] = $_POST['email'];

		error_log("first name = ".$_SESSION['first_name']);
		error_log("last name = ".$_SESSION['last_name']);
		error_log("gender = ".$_SESSION['gender']);
		foreach ($_SESSION['type'] as $t) error_log("type selected ".$t);
		error_log("status = ".$_SESSION['status']);
		error_log("start date = ".$_SESSION['start_date']);
		error_log("city = ".$_SESSION['city']);
		error_log("zip = ".$_SESSION['zip']);
		error_log("phone = ".$_SESSION['phone']);
		error_log("email = ".$_SESSION['email']);
		$result = get_people_for_export("*", $_SESSION['first_name'], $_SESSION['last_name'], $_SESSION['gender'], $_SESSION['type'],
		$_SESSION['status'], $_SESSION['start_date'], $_SESSION['city'], $_SESSION['zip'],
		$_SESSION['phone'], $_SESSION['email']);
		$returned_people = array();

		while ($result_row = mysqli_fetch_assoc($result)) {
			$person = make_a_person($result_row);
			$returned_people[] = $person;
		}
		$_SESSION['returned_people'] = serialize($returned_people);
		error_log("returns ".count($_SESSION['returned_people']). "people");
		include('dataResults.inc.php');
	} else if ($_POST['_form_submit'] == 2) {
		error_log("Exporting data step 2");
		$_SESSION['results'] = $_POST['results_list'];
		if ($_POST['all_export']) {
			$export_people = array();
			error_log("returns ".count(unserialize($_SESSION['returned_people'])). "people");
			foreach(unserialize($_SESSION['returned_people']) as $p) {
				$export_people[] = $p->get_id();
				error_log("Exporting data for " .$p->get_id());
			}
			error_log("Exporting all data.");
			$_SESSION['selected_people'] = $export_people;
			include('dataExport.inc.php');
		}
		else if ($_POST['b_export']) {
			error_log("Exporting selected data");
			if ($_POST['results_list']) {
				$_SESSION['selected_people'] = $_POST['results_list'];
				foreach ($_POST['results_list'] as $export_person) {
					$temp_dude = retrieve_person($export_person);
					error_log("Exporting data for ". $temp_dude->get_first_name() . " " . $temp_dude->get_last_name());
				}
			}
			include('dataExport.inc.php');
		}
	} else if ($_POST['_form_submit'] == 3) {
		error_log("Exporting data step 3");
		$_POST['export_attr'][] = 'id';
		$all_attrs_concat = implode(", ", $_POST['export_attr']);
		echo $all_attrs_concat;
		error_log("All attributes = " .$all_attrs_concat);
		foreach ($_POST['export_attr'] as $attr) { error_log("attr to be exported ".$attr); }
		
		$result = get_people_for_export($all_attrs_concat, $_SESSION['first_name'], $_SESSION['last_name'], $_SESSION['gender'],
										$_SESSION['type'], $_SESSION['status'], $_SESSION['start_date'], $_SESSION['city'], 
										$_SESSION['zip'], $_SESSION['phone'], $_SESSION['email']);
		
		$export_data = array();
		while ($result_row = mysqli_fetch_assoc($result)) {
			if (in_array($result_row['id'], $_SESSION['selected_people'])){
				$temp_person = array($result_row['id']);
				foreach($result_row as $row) {
					if (!isset($row) || $row == "") $row = "";
					$temp_person[] = $row;
				}
				$export_data[] = array_slice($temp_person,0,count($temp_person)-1);
			}
		}
		date_default_timezone_set('America/New_York');
        $current_time = array("Export date: " . date("F j, Y, g:i a"));
		export_data($current_time, array_merge(array("id"),$_POST['export_attr']), $export_data);
	} else if (isset($_POST['action']) && $_POST['action'] == 'export_donors_over_10000') {
		exportDonorsOver10000();
	}
}

// Define the function to handle the export
function exportDonorsOver10000() {
    include_once('database/dbinfo.php'); // Make sure you have your database connection setup here
    $connection = connect();  // This should be your function to establish a database connection
    
    // Your SQL query to fetch the required data
    $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, SUM(d.AmountGiven) AS TotalDonation
              FROM dbdonations AS d
              JOIN dbdonors AS p ON d.Email = p.Email
              GROUP BY d.Email
              HAVING TotalDonation > 10000";

    $result = mysqli_query($connection, $query);
	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_over_10000.csv"');
    
    $output = fopen("php://output", "w");
    
    // Write the CSV header
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number', 'Total Donation'));
    
    // Write rows
    while ($row = mysqli_fetch_assoc($result)) {
		$formattedPhone = '(' . substr($row['PhoneNumber'], 0, 3) . ') ' . substr($row['PhoneNumber'], 3, 3) . '-' . substr($row['PhoneNumber'], 6);
		// Format the total donation to include a dollar sign and commas
		$formattedTotalDonation = '$' . number_format($row['TotalDonation'], 2, '.', ',');
		fputcsv($output, array($row['Email'], $row['FirstName'], $row['LastName'], $formattedPhone, $formattedTotalDonation));
	}
	
    
    fclose($output);
    //exit();
}

// Export Function for the Frequency of Giving Report
function exportDonorsFOG() {
    include_once('database/dbinfo.php'); // Make sure you have your database connection setup here
    $connection = connect();  // This should be your function to establish a database connection
    
    // Your SQL query to fetch the required data
    $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                             DATEDIFF( CURRENT_DATE(), MIN(DateOfContribution)) AS DateDiff  
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    GROUP BY d.Email";
    $result = mysqli_query($connection, $query);
	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_Frequncy_Of_Giving.csv"');
    
    $output = fopen("php://output", "w");

    //Descriptions of Every type
    fputcsv($output, array("Frequency of Giving", "Description"));
    fputcsv($output, array('Monthly', get_description("Monthly")));
    fputcsv($output, array('Yearly', get_description("Yearly")));
    fputcsv($output, array('Sporadic', get_description("Sporadic")));

    //Add a few blank lines to make the thing easier to read
    fputcsv($output, array());
    fputcsv($output, array());
    fputcsv($output, array());
    
    // Write the CSV header
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number', 'Frequency of Giving', 'Days From Earliest Donation'));
    
    // Write rows
    while ($row = mysqli_fetch_assoc($result)) {
		$formattedPhone = '(' . substr($row['PhoneNumber'], 0, 3) . ') ' . substr($row['PhoneNumber'], 3, 3) . '-' . substr($row['PhoneNumber'], 6);
		
		// Get the current donor's frequency of giving
        $FOG = get_donation_frequency($row["Email"]);
		fputcsv($output, array($row['Email'], $row['FirstName'], $row['LastName'], $formattedPhone, $FOG, $row['DateDiff']));
	}
	
    fclose($output);
}

//Export report for donations less than 2 years
// Define the function to handle the export
function exportDonorsLessThanTwoYears() {
    include_once('database/dbinfo.php'); // Make sure you have your database connection setup here
    $connection = connect();  // This should be your function to establish a database connection
    
    // Get the current date
    $currentDate = date("Y-m-d");

    // Define the threshold date (two years ago from current date)
    $thresholdDate = date('Y-m-d', strtotime('-2 years', strtotime($currentDate)));

    $query = "SELECT d.FirstName, d.LastName, d.Email, MAX(dd.DateOfContribution) AS LastDonation
                FROM dbdonors d
                LEFT JOIN dbdonations dd ON d.Email = dd.Email
                GROUP BY d.Email
                HAVING LastDonation < '$thresholdDate' OR LastDonation IS NULL
                ORDER BY d.LastName;
                ";
    $result = mysqli_query($connection, $query);
	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_less_than_two_years.csv"');
    
    $output = fopen("php://output", "w");
    
    // Write the CSV header
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Last Donation'));
    
    // Write rows
    while ($row = mysqli_fetch_assoc($result)) {
		 // Format the total donation to include a dollar sign and commas
		$formattedTotalDonation = '$' . number_format($row['AmountGiven'], 2, '.', ',');
		fputcsv($output, array($row['Email'], $row['FirstName'], $row['LastName'], $row['LastDonation']));
	}
	    
    fclose($output);
}

// Export Function for the Report on Donors whose Frequency of Giving is Greater than Yearly
function exportDonorsFOGGTY() {
    include_once('database/dbinfo.php'); // Make sure you have your database connection setup here
	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_Frequncy_Of_Giving_GTY.csv"');
    
    $output = fopen("php://output", "w");

    $donors = get_all_donors();
    
    // Write the CSV header
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number'));
    
    // Write rows
    $greater_than_yearly_donors = []; // Array to store donors who donate greater than yearly
    foreach ($donors as $donor) {
        $FUN= determine_donation_GTY($donor->get_email());
        if ($FUN=="Greater Than Yearly") {
            $greater_than_yearly_donors[] = $donor; // Add the donor to the array
        }
	}

    if (count($greater_than_yearly_donors) > 0) {
        foreach ($greater_than_yearly_donors as $donor) {
            fputcsv($output, array($donor->get_email(), $donor->get_first_name(), $donor->get_last_name(), preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $donor->get_phone())));
        }
    } else {
        fputcsv($output, array('No donors found'));
    }

    //fputcsv($output, array($donor->get_email(), $donor->get_first_name(), $donor->get_last_name(), preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $donor->get_phone())));
    fclose($output);
}
// Export Function for the Report on Donor's in the Past Three Years who haven't donated to an Event
function exportDonorsL3YNE() {
    include_once('database/dbinfo.php'); // Make sure you have your database connection setup here
    $connection = connect();  // This should be your function to establish a database connection
    //Get current date
	$currentDate = date("Y-m-d");
	//Define the threshold date (two years ago from current date)
	$thresholdDate = date('Y-m-d', strtotime('-3 years', strtotime($currentDate)));
    // Your SQL query to fetch the required data
    $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                      MIN(DateOfContribution) AS EarliestDonation, ContributionCategory
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    WHERE (d.DateOfContribution > '$thresholdDate')
                          AND ContributedSupportType != 'Fundraising Events'
                    GROUP BY d.Email ";
            $result = mysqli_query($connection, $query);
	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_Donors_From_Past_Three_Years_No_Events.csv"');
    
    $output = fopen("php://output", "w");
    
    // Write the CSV header
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number', 'Earliest Donation','Type of Donation'));
    
    // Write rows
    while ($row = mysqli_fetch_assoc($result)) {
		$formattedPhone = '(' . substr($row['PhoneNumber'], 0, 3) . ') ' . substr($row['PhoneNumber'], 3, 3) . '-' . substr($row['PhoneNumber'], 6);
		fputcsv($output, array($row['Email'], $row['FirstName'], $row['LastName'], $formattedPhone, $row['EarliestDonation'], $row['ContributionCategory']));
	}
    fclose($output);
}

// Export Function for the Report on Donor's in the Past Three Years who have donated to Events
function exportDonorsL3YE() {
    include_once('database/dbinfo.php'); // Make sure you have your database connection setup here
    $connection = connect();  // This should be your function to establish a database connection
    //Get current date
	$currentDate = date("Y-m-d");
	//Define the threshold date (two years ago from current date)
	$thresholdDate = date('Y-m-d', strtotime('-3 years', strtotime($currentDate)));
    // Your SQL query to fetch the required data
    $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, COUNT(d.email) AS Number_Of_Donations, 
                      MIN(DateOfContribution) AS EarliestDonation, ContributionCategory
                    FROM dbdonations AS d
                    JOIN dbdonors AS p ON d.Email = p.Email
                    WHERE (d.DateOfContribution > '$thresholdDate')
                          AND ContributedSupportType = 'Fundraising Events'
                    GROUP BY d.Email ";
            $result = mysqli_query($connection, $query);
	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_Donors_From_Past_Three_Years_Events.csv"');
    
    $output = fopen("php://output", "w");
    
    // Write the CSV header
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number', 'Earliest Donation','Event Sponsored'));
    
    // Write rows
    while ($row = mysqli_fetch_assoc($result)) {
		$formattedPhone = '(' . substr($row['PhoneNumber'], 0, 3) . ') ' . substr($row['PhoneNumber'], 3, 3) . '-' . substr($row['PhoneNumber'], 6);
		fputcsv($output, array($row['Email'], $row['FirstName'], $row['LastName'], $formattedPhone, $row['EarliestDonation'], $row['ContributionCategory']));
	}
    fclose($output);
}

// Export Function for the Report on Top 10 Donors
function exportDonorsT10() {
    include_once('database/dbinfo.php'); // Your database connection setup
    $connection = connect();  // Establishing a database connection

    // Retrieve the number of top donors from the form submission
    $topXDonors = isset($_POST['topXDonors']) ? (int)$_POST['topXDonors'] : 10; // Default to 10 if not specified

    // Adjust your SQL query to use the $topXDonors variable
    $query = "SELECT d.Email, p.FirstName, p.LastName, p.PhoneNumber, SUM(d.AmountGiven) AS Sum_Of_Donations
              FROM dbdonations AS d
              JOIN dbdonors AS p ON d.Email = p.Email
              GROUP BY d.Email
              ORDER BY Sum_Of_Donations DESC
              LIMIT ?";
    
    // Prepare, bind and execute the query with the dynamic limit
    if ($stmt = mysqli_prepare($connection, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $topXDonors);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="donors_Top_'.$topXDonors.'_Donors.csv"');
        
        $output = fopen("php://output", "w");
        
        // CSV header
        fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number', 'Sum of Donation'));
        
        // Fetch and write each row
        while ($row = mysqli_fetch_assoc($result)) {
            $formattedPhone = '(' . substr($row['PhoneNumber'], 0, 3) . ') ' . substr($row['PhoneNumber'], 3, 3) . '-' . substr($row['PhoneNumber'], 6);
            $formattedSum = '$' . number_format($row['Sum_Of_Donations'], 2);
            fputcsv($output, array($row['Email'], $row['FirstName'], $row['LastName'], $formattedPhone, $formattedSum));
        }
        
        fclose($output);
    } else {
        echo "Error preparing the query.";
    }
}

// Export Function for the Donor's Stage/Funnel
function exportDonorsDSF() {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_Donors_Stage.csv"');
    
    $output = fopen("php://output", "w");

    fputcsv($output, array("Donation Funnel", "Description"));
    fputcsv($output, array('Interested', get_description("Interested")));
    fputcsv($output, array('Donor', get_description("Donor")));
    fputcsv($output, array('Engaged', get_description("Engaged")));
    fputcsv($output, array('Loyal Donor', get_description("Loyal Donor")));
    fputcsv($output, array('Leadership Donor', get_description("Leadership Donor")));

    //Add a few blank lines to make the thing easier to read
    fputcsv($output, array());
    fputcsv($output, array());
    fputcsv($output, array());
    
    // Write the CSV header
    fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number', 'Donation Funnel'));

    // Get all donors
    $donors = get_all_donors();
    
    // Write rows
    if (count($donors) > 0) { // If we have donors, create the file
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
                fputcsv($output, array($donor_email, $donor_first_name, $donor_last_name, $formattedPhone, $funnel));
            }
        }
    }
    fclose($output);
}

// Export Function for the Report on Retention Rate
function exportDonorsRR() {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="donors_Retention_Rate.csv"');
    
    $output = fopen("php://output", "w");

    //Get all Donors
    $donors=get_all_donors();
    //Array for Multi-Year Donors
    $MultiYearDonors=array();
    //Counter for Multi-Year Donors
    $MultiCounter=0;
    foreach($donors as $donor){
        //Go through each donor and see if they are a Multi-Year donor
        //and add to Multi-Year array if they are and increas the Multi
        //Counter.
        $dmail=$donor->get_email();
        $type= get_donor_status($dmail);
        $MultiYearDonors[]=$donor;
        if($type=="Multiyear Donor"){
            $MultiCounter++;
        }
    }

    //Generate Table and Calculate Retention Rate of Multi Year Donors
    if(count($MultiYearDonors)>0){
        $RetentionRate=($MultiCounter/count($donors))*100;
        $RetentionRate=$RetentionRate."%";
        //Put the Number of Multi-Year Donors and Retention Rate in the CSV as well
        fputcsv($output, array('Number of Multi-Year Donors', 'Retention Rate'));
        fputcsv($output, array($MultiCounter, $RetentionRate));
        //Add a few blank lines to make the thing easier to read
        fputcsv($output, array());
        fputcsv($output, array());
        fputcsv($output, array());
        
        //Descriptions of Every type
        fputcsv($output, array("Retention Status", "Description"));
        fputcsv($output, array('New Donor', get_description("New Donor")));
        fputcsv($output, array('Multi-Year Donor', get_description("Multiyear Donor")));
        fputcsv($output, array('Returning Donor', get_description("Returning Donor")));
        fputcsv($output, array('Formerly Active Donor', get_description("Formerly Active Donor")));
        fputcsv($output, array('Inactive  Donor', get_description("Inactive Donor")));

        //Add a few blank lines to make the thing easier to read
        fputcsv($output, array());
        fputcsv($output, array());
        fputcsv($output, array());
        
        // Write the CSV header
        fputcsv($output, array('Email', 'First Name', 'Last Name', 'Phone Number', 'Donor Status'));

        foreach($MultiYearDonors as $donor){
            // Get the donor details
            $donor_first_name = $donor->get_first_name();
            $donor_last_name = $donor->get_last_name();
            $donor_email = $donor->get_email();
            $phone = $donor->get_phone();

            //Format the Phone Number
            $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
            
            //Write Multi-Year Donor's Details to CSV File
            fputcsv($output, array($donor_email, $donor_first_name, $donor_last_name, $formattedPhone, get_donor_status($donor_email),));
        }  
    }
    fclose($output);
}

//End of export
function export_data($current_time, $search_attr, $export_data) {
	$filename = "dataexport.csv";
	$handle = fopen($filename, "w");
	fputcsv($handle, $current_time);
	fputcsv($handle, $search_attr, ',');
	foreach ($export_data as $person_data) 
	   if (count($person_data)>1 && $person_data[1]!="") // anything more than the id, export it, otherwise skip it
	       fputcsv($handle, $person_data, ',','"');
	if (in_array("history",$search_attr)) { // split history into several lines per person 
	   $people_in_past_shifts = get_all_peoples_histories();
	   foreach ($people_in_past_shifts as $p=>$history) 
	        fputcsv($handle, array($p,$history),',','"');  
	}
	fclose($handle);
}
ob_end_flush();
?></div>
</div>
        <?PHP include('footer.inc'); ?>
</body>
</html>
