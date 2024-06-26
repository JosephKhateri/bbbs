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
     * Parameters: $email = A string that represents the email a donation is associated with
     * This function retrieves an array of Donations from the dbdonations table that match the given email
     * Return type: An array of Donation objects
     * Pre-condition: $email is a string
     * Post-condition: An array of Donation objects is returned
     */
    function retrieve_donations_by_email($email) : array {
        $donations = array();
        if (!isset($email) || $email == "" || $email == null) return $donations;
        $con=connect();
        $query = "SELECT * FROM dbdonations WHERE email = '" . $email ."'";
        $result = mysqli_query($con,$query);
        while ($result_row = mysqli_fetch_assoc($result)) {
            $the_donation = make_a_donation($result_row);
            $donations[] = $the_donation;
        }
        return $donations;
    }

    /*
     * Parameters: None
     * This function retrieves all donations from the dbdonations table
     * Return type: An array of Donation objects
     * Pre-condition: None
     * Post-condition: An array of Donation objects is returned
     */
    function get_all_donations() : array {
        $con=connect();
        $query = 'SELECT * FROM dbdonations';
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
        $query = "SELECT SUM(AmountGiven) AS totalAmount FROM dbdonations WHERE Email = '" . $donorEmail . "'";
        $result = mysqli_query($con,$query);
        $totalAmount = mysqli_fetch_assoc($result);
        $totalAmount = (float) $totalAmount['totalAmount']; // Cast the total amount to a float that is returned
        return $totalAmount;
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
        $query = "SELECT ContributionCategory, SUM(AmountGiven) AS TotalAmount FROM dbdonations WHERE ContributedSupportType = 'Fundraising Events' AND Email = '" . $donorEmail . "' GROUP BY ContributionCategory";
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
        $query = "SELECT * FROM dbdonations WHERE ContributedSupportType = 'Fundraising Events' AND Email = '" . $donorEmail . "' GROUP BY ContributionCategory";
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
        $query = "SELECT * FROM dbdonations WHERE ContributedSupportType != 'Fundraising Events' AND Email = '" . $donorEmail . "' GROUP BY ContributionCategory";
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
     * Parameters: $donation_dates = An array of strings that represent the dates of donations made by a donor
     * This function checks if a donor has donated at least once a year for the past X years and is
         * called when calculating the donation frequency and funnel of a donor
         * For donation frequency, the donor is considered "Yearly" if this function returns the provided number of years (2)
         * For donation funnel, the donor is considered "DONOR" if this function returns the provided number of years (3)
     * Return type: An integer that represents the number of years the donor has donated in the past X years
     * Pre-condition: $donation_dates is an array of dates in the format 'YYYY-MM-DD'
     * Post-condition: The number of years the donor has donated in the past three years is returned
     */
    function check_donations_for_past_x_years($donation_dates, $num_years) : int {
        $current_date = date('Y-m-d'); // Get the current date

        // Check if the donor has donated at least once in the past three years
        $yearly_count = 0; // Initialize yearly count

        // Check if there's at least one donation for each of the past X years
        for ($i = 1; $i <= $num_years; $i++) {
            $year_to_check = date('Y-m-d', strtotime("-$i years", strtotime($current_date))); // Get the year to check
            $year_to_check_plus_one = date('Y-m-d', strtotime("+1 year", strtotime($year_to_check))); // Get the year after the year to check

            $has_donation = false; // Flag to track if there's a donation for the year

            // Iterate through donation dates to find donations within the year being checked
            foreach ($donation_dates as $donation_date) {
                if ($donation_date >= $year_to_check && $donation_date <= $year_to_check_plus_one) {
                    $has_donation = true;
                    break; // Exit loop early when a donation is found for the year
                }
            }

            if ($has_donation) {
                $yearly_count++; // Increment yearly count if a donation was found for the year
            }
        }
        return $yearly_count;
    }

    /*
     * Parameters: $result_row = An associative array that represents a row in the dbdonations table
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
// dbdonations.php
// Overall Grading:
// 1. Program specifications/correctness: Adequate - Program doesn't insert data into dbdonations properly
// 2. Readability: Adequate - Need further documentation for the functions. Variables are named accordingly.
    // Need to indent everything within <php> tags
// 3. Code efficiency: Good - Code is very efficient, but there are some issues with the code actually working properly
// 4. Documentation: Adequate - Need further documentation for the functions
// 5. Assigned Task: Adequate - Program doesn't insert data into dbdonations properly
    function checkDonationExists($email, $date, $amount, $con) {
        $query = $con->prepare("SELECT Email, DateOfContribution, AmountGiven FROM dbdonations WHERE Email = ? AND DateOfContribution = ? AND AmountGiven = ?");
        $query->bind_param("ssd", $email, $date, $amount);
        $query->execute();
        $result = $query->get_result();
        return $result->num_rows > 0;
    }

    //Returns total number of donations in db
    function getMaxDonationID(){
        // get the max donation id from dbdonations
        $con = connect();
        $query = "SELECT MAX(DonationID) FROM dbdonations";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_array($result);
        return $row[0];
    }

    function addDonation($donationData, $con, $newID, $support, $category) {
        // Get values from $donationData array
        $dateOfContribution = date('Y-m-d', strtotime($donationData[0])); // Convert date to MySQL-compatible format
        $amountGiven = $donationData[3]; // Ensure this is captured correctly from our CSV
        $email = trim($donationData[7]);
        $paymentMethod = $donationData[13];
        $memo = $donationData[14];

        if (empty($email) || empty($dateOfContribution) || empty($amountGiven)) {
            error_log("Missing essential donation information: " . implode(", ", $donationData));
            return;
        }
        $con = connect();
        // Prepare the SQL query to insert a new donation
        $query = $con->prepare("INSERT INTO dbdonations (Email, DateOfContribution, ContributedSupportType, ContributionCategory, AmountGiven, PaymentMethod, Memo, DonationID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("ssssdssi", $email, $dateOfContribution, $support, $category, $amountGiven, $paymentMethod, $memo, $newID);
        if (!$query->execute()) {
            echo "Failed to upload donation: " . $query->error;
        }
    }

    function processDonationData($donationData, $con, $support, $category) {
        $email = trim($donationData[7]);
        $dateOfContribution = date('Y-m-d', strtotime($donationData[0]));
        $amountGiven = $donationData[3];

        error_log("Attempting to process donation for: $email on $dateOfContribution with amount $amountGiven");

        if (empty($email) || empty($dateOfContribution) || empty($amountGiven)) {
            error_log("Missing essential donation information: " . implode(", ", $donationData));
            return;
        }
    
        if (!checkDonationExists($email, $dateOfContribution, $amountGiven, $con)) {
            $newID = getMaxDonationID() + 1;
            addDonation($donationData, $con, $newID, $support, $category);
        } else {
            error_log("Donation already exists for $email on $dateOfContribution with amount $amountGiven");
        }
    }