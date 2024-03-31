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

    /**
     * @version March 1, 2012
     * @author Oliver Radwan and Allen Tucker
     * Edited by Megan and Noor for BBBS in Spring 2024
     */
    include_once('dbinfo.php');
    include_once(dirname(__FILE__).'/../domain/Donation.php');

    /*
     * Parameters: $user = A Donation object
     * This function adds a Donation to the dbDonations table
     * Return type: A boolean value that represents if the Donation was added to the dbDonations table
     * Pre-condition: $donation is a Donation object
     * Post-condition: A Donation is added to the dbDonations table if its ID doesn't already exist in the table, otherwise nothing happens
     */
    function add_donation($donation) : bool {
        if (!$donation instanceof Donation)
            die("Error: add_donation type mismatch");
        $con=connect();
        $query = "SELECT * FROM dbDonations WHERE id = '" . $donation->get_id() . "'";
        $result = mysqli_query($con,$query);
        //if there's no entry for this id, add it
        if ($result == null || mysqli_num_rows($result) == 0) {
            mysqli_query($con,'INSERT INTO dbDonations VALUES("' .
                $donation->get_id() . '","' .
                $donation->get_email() . '","' .
                $donation->get_contribution_date() . '","' .
                $donation->get_contribution_type() . '","' .
                $donation->get_contribution_category() . '","' .
                $donation->get_amount() . '","' .
                $donation->get_payment_method() . '","' .
                $donation->get_memo() . '");'
            );
            mysqli_close($con);
            return true;
        }
        mysqli_close($con);
        return false;
    }

    /*
     * Parameters: $id = A string that represents the ID number of a donation
     * This function removes a donation from the dbDonations table using the ID of the donation
     * Return type: A boolean value that represents if the Donation was removed from the dbDonations table
     * Pre-condition: $id is a string
     * Post-condition: A Donation is removed from the dbDonations table if it exists, otherwise nothing happens
     */
    function remove_donation($id) : bool {
        $con=connect();
        $query = 'SELECT * FROM dbDonations WHERE id = "' . $id . '"';
        $result = mysqli_query($con,$query);
        if ($result == null || mysqli_num_rows($result) == 0) {
            mysqli_close($con);
            return false;
        }
        $query = 'DELETE FROM dbDonations WHERE id = "' . $id . '"';
        $result = mysqli_query($con,$query);
        mysqli_close($con);
        return $result;
    }

    /*
     * Parameters: $donation = A Donation object with updated information
     * This function updates a donation in the dbDonations table
     * Return type: A boolean value that represents if the Donation was updated in the dbDonations table
     * Pre-condition: $donation is a Donation object
     * Post-condition: The donation is updated in the dbDonations table if it exists, otherwise nothing happens
     */
    function update_donation($donation) : bool {
        $con=connect();

        // Get the values from the donation object
        $id = $donation->get_id();
        $email = $donation->get_email();
        $date = $donation->get_date();
        $type = $donation->get_type();
        $category = $donation->get_category();
        $amount = $donation->get_amount();
        $method = $donation->get_method();
        $memo = $donation->get_memo();

        // Query is broken up into multiple lines for readability
        $query = "UPDATE dbDonations SET ";
        $query .= "Email = '" . $email . "', ";
        $query .= "DateOfContribution = '" . $date . "', ";
        $query .= "ContributionSupportType = '" . $type . "', ";
        $query .= "ContributionCategory = '" . $category . "', ";
        $query .= "AmountGiven = '" . $amount . "', ";
        $query .= "PaymentMethod = '" . $method . "', ";
        $query .= "Memo = '" . $memo . "' ";
        $query .= "WHERE id = '" . $id . "'";
        $result = mysqli_query($con,$query);
        mysqli_close($con);
        return $result;
    }

    /*
     * Parameters: $id = A string that represents the ID number of a donation
     * This function retrieves a donation from the dbDonations table using the ID of the donation
     * Return type: A Donation object
     * Pre-condition: $id is a string
     * Post-condition: A Donation object is returned if it exists, otherwise nothing is returned
     */
    function retrieve_donation($id) : Donation {
        $con=connect();
        $query = "SELECT * FROM dbDonations WHERE id = '" . $id . "'";
        $result = mysqli_query($con,$query);
        $result_row = mysqli_fetch_assoc($result);

        // Create a donation object
        $theDonation = make_a_donation($result_row);
    //    mysqli_close($con);
        return $theDonation;
    }

    /*
     * Parameters: $email = A string that represents the email a donation is associated with
     * This function retrieves an array of Donations from the dbDonations table that match the given email
     * Return type: An array of Donation objects
     * Pre-condition: $email is a string
     * Post-condition: An array of Donation objects is returned
     */
    function retrieve_donations_by_email ($email): array {
        $donations = array();
        if (!isset($email) || $email == "" || $email == null) return $donations;
        $con=connect();
        $query = "SELECT * FROM dbDonations WHERE email = '" . $email ."'";
        $result = mysqli_query($con,$query);
        while ($result_row = mysqli_fetch_assoc($result)) {
            $the_donation = make_a_donation($result_row);
            $donations[] = $the_donation;
        }
        return $donations;
    }

    /*
     * Parameters: None
     * This function retrieves all donations from the dbDonations table
     * Return type: An array of Donation objects
     * Pre-condition: None
     * Post-condition: An array of Donation objects is returned
     */
    function get_all_donations() : array {
        $con=connect();
        $query = 'SELECT * FROM dbDonations';
        $result = mysqli_query($con,$query);

        // Create array of donations
        $theDonations = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $theDonation = make_a_donation($result_row);
            $theDonations[] = $theDonation;
        }
        return $theDonations;
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function retrieves the total amount donated by a donor
     * Return type: A float that represents the total amount donated by the donor
     * Pre-condition: $donorEmail is a string
     * Post-condition: The total amount donated by the donor is returned
     */
    function get_total_amount_donated($donorEmail) : float {
        $con = connect();
        $query = "SELECT SUM(AmountGiven) AS totalAmount FROM dbDonations WHERE Email = '" . $donorEmail . "'";
        $result = mysqli_query($con,$query);
        $totalAmount = mysqli_fetch_assoc($result);
        $totalAmount = (float) $totalAmount['totalAmount']; // Cast the total amount to a float that is returned
        return $totalAmount;
    }

    function count_donations_within_years($donorEmail, $years) : int {
        $count = 0;
        $current_date = date('Y-m-d');
        $donations = retrieve_donations_by_email($donorEmail);

        foreach ($donations as $donation) {
            $donation_date = $donation->get_contribution_date();
            $years_diff = date_diff(date_create($donation_date), date_create($current_date))->y;
            if ($years_diff <= $years) {
                $count++;
            }
        }
        return $count;
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function retrieves the event donation categories of a donor and the total amount donated to each category
     * Return type: An array of associative arrays that represent the event donation categories of the donor
     * Pre-condition: $donorEmail is a string
     * Post-condition: An array of associative arrays that represent the donation categories of the donor and the total amount donated to each category is returned
     */
    function get_event_donation_categories($donorEmail) : array {
        $con = connect();
        // Query to get the event donation categories and the total amount donated to each category
        // I may want to future-proof this to have it look for donations with Contribution Categories that match the names of the events
        // That would require us to have a list of events in the database
        $query = "SELECT ContributionCategory, SUM(AmountGiven) AS TotalAmount FROM dbDonations WHERE ContributedSupportType = 'Fundraising Events' AND Email = '" . $donorEmail . "' GROUP BY ContributionCategory";
        $result = mysqli_query($con,$query);

        // Create an array of associative arrays that represent the donation categories and the total amount donated to each category
        $categories = [];
        while($result_row = mysqli_fetch_assoc($result)) {
            $categories[] = $result_row;
        }
        return $categories;
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function determines the donation type of the donor (Event, Non-Event, Both, Neither)
     * Return type: A string that represents the donation type of the donor (Event, Non-Event, Both, Neither)
     * Pre-condition: $donorEmail is a string
     * Post-condition: A string that represents the donation type of the donor is returned
     */
    function determine_donor_donation_type($donorEmail) : string {
        // Get the event and non-event donations made by the donor
        $event_donations = get_event_donations($donorEmail);
        $non_event_donations = get_non_event_donations($donorEmail);

        // Determine the donation type of the donor based on the number of event and non-event donations they made
        if (count($event_donations) > 0 && count($non_event_donations) > 0) {
            return "Both"; // Donor has donated to both events and non-events
        } elseif (count($event_donations) > 0) {
            return "Event"; // Donor has donated to events only
        } elseif (count($non_event_donations) > 0) {
            return "Non-Event"; // Donor has donated to non-events only
        } else {
            return "Neither"; // Donor has not donated to any events or non-events (no donations)
        }
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function retrieves the event donations made by the donor
     * Return type: An array of Donation objects
     * Pre-condition: $donorEmail is a string
     * Post-condition: An array of Donation objects that represent the event donations made by the donor is returned
     */
    function get_event_donations($donorEmail) : array {
        // Query to get all event donations made by the donor
        // I may want to future-proof this to have it look for donations with Contribution Categories that match the names of the events
        // That would require us to have a list of events in the database
        $con = connect();
        $query = "SELECT * FROM dbDonations WHERE ContributedSupportType = 'Fundraising Events' AND Email = '" . $donorEmail . "' GROUP BY ContributionCategory";
        $result = mysqli_query($con,$query);

        // Count the number of event donations
        $theDonations = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $theDonation = make_a_donation($result_row);
            $theDonations[] = $theDonation;
        }
        return $theDonations;
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function retrieves the non-event donations made by the donor
     * Return type: An array of Donation objects
     * Pre-condition: $donorEmail is a string
     * Post-condition: An array of Donation objects that represent the non-event donations made by the donor is returned
     */
    function get_non_event_donations($donorEmail) : array {
        // Query to get all non-event donations made by the donor
        // I may want to future-proof this to have it look for donations with Contribution Categories that don't match the names of the events
        // That would require us to have a list of events in the database
        $con = connect();
        $query = "SELECT * FROM dbDonations WHERE ContributedSupportType != 'Fundraising Events' AND Email = '" . $donorEmail . "' GROUP BY ContributionCategory";
        $result = mysqli_query($con,$query);

        // Count the number of non-event donations
        $theDonations = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $theDonation = make_a_donation($result_row);
            $theDonations[] = $theDonation;
        }
        return $theDonations;
    }

    /*
     * Parameters: $result_row = An associative array that represents a row in the dbDonations table
     * This function constructs a new Donation object with the given parameters
     * Return type: A Donation object
     * Pre-condition: $result_row is a valid associative array
     * Post-condition: A new Donation object is created
     */
    function make_a_donation($result_row) : Donation {
        $theDonation = new Donation(
            $result_row['DonationID'],
            $result_row['Email'],
            $result_row['DateOfContribution'],
            $result_row['ContributedSupportType'],
            $result_row['ContributionCategory'],
            $result_row['AmountGiven'],
            $result_row['PaymentMethod'],
            $result_row['Memo']
        );
        return $theDonation;
    }
// dbDonations.php
// Overall Grading:
// 1. Program specifications/correctness: Adequate - Program doesn't insert data into dbDonations properly
// 2. Readability: Adequate - Need further documentation for the functions. Variables are named accordingly.
    // Need to indent everything within <php> tags
// 3. Code efficiency: Good - Code is very efficient, but there are some issues with the code actually working properly
// 4. Documentation: Adequate - Need further documentation for the functions
// 5. Assigned Task: Adequate - Program doesn't insert data into dbDonations properly
    function checkDonationExists($email, $date,$amount,$con){
        $query = $con->prepare("SELECT Email, DateOfContribution, AmountGiven from dbdonations WHERE Email = ? AND DateOfContribution = ? AND AmountGiven = ?");
        $query->bind_param("sdi", $email,$date,$amount);
        $query->execute();
        $result = $query->get_result();
        return $result->num_rows > 0;
    }

    //Returns total number of donations in db
    function getMaxDonationID(){
        return count(get_all_donations());
    }

    function addDonation($donationData, $con,$newID){
        $email = trim($donationData[7]);
        $dateOfContribution = $donationData[0];
        $amountGiven = $donationData[3]; // Ensure this is captured correctly from our CSV
        if (empty($email) || empty($dateOfContribution) || empty($amountGiven)) {
            error_log("Missing essential donation information: " . implode(", ", $donationData));
            return;
        }
        // Prepare the SQL query to insert a new donation
        $query = $con->prepare("INSERT INTO dbdonations (Email, DateOfContribution, ContributedSupportType, ContributionCategory, AmountGiven, PaymentMethod, Memo, DonationID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("ssssdssi", $donationData[7], $donationData[0], $donationData[1], $donationData[2], $donationData[3], $donationData[13], $donationData[14],$newID);
        if (!$query->execute()) {
            error_log("Failed to insert donation: " . $query->error);
        } else {
            // Optionally, call updateLifetime here if it's not automatically triggered elsewhere
            updateLifetime($email, $con);
        }
    }

    function updateDonationInfo($donationData, $con){
        // Prepare the SQL query to update donation info
        $query = $con->prepare("UPDATE dbdonations SET DateOfContribution = ?, ContributedSupportType = ?, ContributionCategory = ?, AmountGiven = ?, PaymentMethod = ?, Memo = ? WHERE Email = ?");
        $query->bind_param("sssdsss", $donationData['Date of Contribution'], $donationData['Contributed Support'], $donationData['Contribution Category'], $donationData['Amount Given'], $donationData['Payment Method'], $donationData['Memo'], $donationData['Email']);
        $query->execute();
    }

    function updateLifetime($email, $con){
        $query = $con->prepare("UPDATE dbdonors SET LifetimeDonation = COALESCE((SELECT SUM(AmountGiven) FROM dbdonations WHERE Email = ?), 0) WHERE Email = ?");
        $query->bind_param("ss", $email, $email);
        if (!$query->execute()) {
            error_log("Failed to update lifetime donation: " . $query->error);
        }
    }

    function processDonationData($donationData, $con){
        // Assuming donationData has the email as the unique identifier in the first position -- KEY WORD IS ASSUMING!!!

        //$x = implode(" ", $donationData);
        //echo $x;

        $donorEmail = $donationData[7];
        $donoDate = $donationData[0];
        $donoAmount = $donationData[3];


        // Check if donation exists for the donor
        $donationExists = checkDonationExists($donorEmail, $donoDate,$donoAmount,$con);

        $newID = getMaxDonationID($con) + 1;

        if (!$donationExists) {
            // Add new donation
            addDonation($donationData, $con,$newID);
        } else {
            // Update donation info
            updateDonationInfo($donationData, $con);
        }

        // Update lifetime donation amount
        updateLifetime($donorEmail, $con);
    }

?>
