<?php

    // dbDonors.php
    // Overall Grading:
    // 1. Program specifications/correctness: Good - Program mostly inserts data into dbDonors properly
    // 2. Readability: Adequate - Need further documentation. Variables are named accordingly. Need to indent everything within <php> tags
    // 3. Code efficiency: Excellent - Code is very efficient, but isn't working properly as mentioned above
    // 4. Documentation: Adequate - Need further documentation for the functions
    // 5. Assigned Task: Good - Program mostly inserts data into dbDonors properly but with 1 small error


    require_once('database/dbinfo.php');
    require_once(dirname(__FILE__) . '/../domain/Donor.php');

    /*
     * Parameters:
     *
     * Return type:
     * Pre-condition:
     * Post-condition:
     */
    function add_donor($donor) {
        if (!$donor instanceof Donor)
            die("Error: add_donor type mismatch");
        $con=connect();
        $query = "SELECT * FROM dbDonors WHERE Email = '" . $donor->get_email() . "'";
        $result = mysqli_query($con,$query);
        //if there's no entry for this id, add it
        if ($result == null || mysqli_num_rows($result) == 0) {
            mysqli_query($con,'INSERT INTO dbDonations VALUES("' .
                $donor->get_email() . '","' .
                $donor->get_company() . '","' .
                $donor->get_first_name() . '","' .
                $donor->get_last_name() . '","' .
                $donor->get_phone() . '","' .
                $donor->get_address() . '","' .
                $donor->get_city() . '","' .
                $donor->get_state() . '","' .
                $donor->get_zip() . '","' .
                $donor->get_lifetime_donation() . '");'
            );
            mysqli_close($con);
            return true;
        }
        mysqli_close($con);
        return false;
    }

    /*
     * Parameters:
     *
     * Return type:
     * Pre-condition:
     * Post-condition:
     */
    function remove_donor($email) {
        $con=connect();
        $query = 'SELECT * FROM dbDonors WHERE Email = "' . $email . '"';
        $result = mysqli_query($con,$query);
        if ($result == null || mysqli_num_rows($result) == 0) {
            mysqli_close($con);
            return false;
        }
        $query = 'DELETE FROM dbDonors WHERE Email = "' . $email . '"';
        $result = mysqli_query($con,$query);
        mysqli_close($con);
        return $result;
    }

    /*
     * Parameters:
     *
     * Return type:
     * Pre-condition:
     * Post-condition:
     */
    function update_donor($donor) {
        $con=connect();

        // Get the values from the donation object
        $email = $donor->get_email();
        $company = $donor->get_company();
        $first_name = $donor->get_first_name();
        $last_name = $donor->get_last_name();
        $phone = $donor->get_phone();
        $address = $donor->get_address();
        $city = $donor->get_city();
        $state = $donor->get_state();
        $zip = $donor->get_zip();
        $lifetime = $donor->get_lifetime_donation();

        // Query is broken up into multiple lines for readability
        $query = "UPDATE dbDonors SET ";
        $query .= "Email = '" . $email . "', ";
        $query .= "Company = '" . $company . "', ";
        $query .= "FirstName = '" . $first_name . "', ";
        $query .= "LastName = '" . $last_name . "', ";
        $query .= "PhoneNumber = '" . $phone . "', ";
        $query .= "Address = '" . $address . "', ";
        $query .= "City = '" . $city . "' ";
        $query .= "State = '" . $state . "' ";
        $query .= "Zip = '" . $zip . "' ";
        $query .= "LifetimeDonation = '" . $lifetime . "' ";
        $query .= "WHERE Email = '" . $email . "'";
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
    function retrieve_donor($email) {
        $con=connect();
        $query = "SELECT * FROM dbDonors WHERE Email = '" . $email . "'";
        $result = mysqli_query($con,$query);
        if (mysqli_num_rows($result) !== 1) {
            mysqli_close($con);
            return false; // need to handle this properly in any code that calls this function
        }
        $result_row = mysqli_fetch_assoc($result);
        $theDonation = make_a_donor($result_row);
    //    mysqli_close($con);
        return $theDonation;
    }

    /*
    * Parameters: None
    * This function retrieves all donors from the dbDonors table and returns an array of donor objects
    * Return type: Array of donors
    * Pre-condition: None
    * Post-condition: Array of donors is returned
    */
    function get_all_donors() {
        $con = connect();
        $query = 'SELECT * FROM dbDonors';
        $result = mysqli_query($con, $query);
        if ($result == null || mysqli_num_rows($result) == 0) {
            mysqli_close($con);
            return false;
        }
        $theDonors = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            // Create donor object and add to the array
            $theDonor = make_a_donor($result_row);
            $theDonors[] = $theDonor;
        }
        mysqli_close($con);
        return $theDonors;
    }

    function get_donor_retention($donorEmail) : string {
        $donations = retrieve_donations_by_email($donorEmail);

        // Get dates of all donations
        $dates = array();
        foreach ($donations as $donation) {
            $dates[] = $donation->get_contribution_date();
        }

        // Sort dates in ascending order
        sort($dates);

        // get the years of the dates
        $years = array();
        foreach ($dates as $date) {
            $years[] = date('Y', strtotime($date));
        }

        // Remove duplicate years
        $unique_years = array_unique($years);

        // If the donor's first donation was from this year
        if (in_array(date('Y'), $years) && count($unique_years) == 1) {
            return "New Donor";
        }
        // if the donor made a donation last year and this year
        elseif (in_array(date('Y') - 1, $years) && in_array(date('Y'), $years)) {
            return "Multiyear Donor";
        }
        // If the donor donated this year, but their 2nd most recent donation was from 2 or more years ago
        elseif (in_array(date('Y'), $years) && isset($years[1]) && $years[1] <= date('Y') - 2) {
            return "Returning Donor";
        }
        else { // if the donor has donated previously, but not in the last year, return "Inactive Donor"
            return "Inactive Donor";
        }
    }

    function determine_donation_funnel($donorEmail) : string {
        $donations_last_3_years = count_donations_within_years($donorEmail, 3);
        $donations_last_5_years = count_donations_within_years($donorEmail, 5);
        $total_donations = get_total_amount_donated($donorEmail);

        /*$funnel = "No Funnel";
        if ($donation_count_last_3_years >= 1) {
            $funnel = "INTERESTED";
        }

        if ($donation_count_last_3_years >= 3) {
            $funnel = "ENGAGED";
        }

        if ($donation_count_last_5_years >= 5) {
            $funnel = "LOYAL DONOR";
        }

        if ($donation_count_last_3_years >= 1 and $donation_count_last_3_years == $donation_count_last_5_years) {
            $funnel = "DONOR";
        }
        if ($total_donation_amount >= 10000) {
            $funnel = "LEADERSHIP DONOR";
        }
        return $funnel;*/

        if ($donations_last_3_years >= 1) {
            if ($donations_last_5_years >= 5) {
                if ($total_donations > 10000) {
                    return "LEADERSHIP DONOR";
                }
                return "LOYAL DONOR";
            }
            if ($donations_last_5_years >= 3) {
                return "ENGAGED";
            }
            return "DONOR";
        }
        return "INTERESTED";
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function retrieves the donation frequency of a donor
     * Return type: A string that represents the donation frequency of the donor
     * Pre-condition: $donorEmail is a string
     * Post-condition: The donation frequency of the donor is returned
     */
    function get_donation_frequency($donorEmail) : string {
        $con = connect();
        $query = "SELECT DISTINCT DATE_FORMAT(DateOfContribution, '%Y-%m') AS donation_month FROM dbDonations WHERE Email = '" . $donorEmail . "'";
        $result = mysqli_query($con,$query);
        $donation_dates = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $donation_dates[] = $result_row['donation_month'];
        }

        // Sort the donation dates in ascending order
        sort($donation_dates);

        $donation_count = count($donation_dates);

        // If the donor has less than 3 donations, categorize as sporadic
        if ($donation_count < 3) {
            return "Sporadic";
        }

        // Calculate the average time difference between donations
        $total_diff = 0;
        for ($i = 1; $i < $donation_count; $i++) {
            $total_diff += strtotime($donation_dates[$i]) - strtotime($donation_dates[$i - 1]);
        }
        $average_diff = $total_diff / ($donation_count - 1);

        // Tolerance levels for variations
        $monthly_tolerance = 5 * 24 * 60 * 60; // 5 days
        $yearly_tolerance = 30 * 24 * 60 * 60; // 30 days

        // Check if the donor donated approximately once a month for the last 3 months
        $is_monthly = abs($average_diff - (31 * 24 * 60 * 60)) <= $monthly_tolerance;

        // Check if the donor donated approximately once a year for the last 3 years
        $is_yearly = abs($average_diff - (365 * 24 * 60 * 60)) <= $yearly_tolerance;

        if ($is_monthly) {
            return "Monthly";
        } elseif ($is_yearly) {
            return "Yearly";
        } else {
            return "Sporadic";
        }
    }

    /*
     * Parameters: $result_row = An associative array that represents a row in the dbDonations table
     * This function constructs a new Donation object with the given parameters
     * Return type: A Donation object
     * Pre-condition: $result_row is a valid associative array
     * Post-condition: A new Donation object is created
     */
    function make_a_donor($result_row) {
        $theDonor = new Donor(
            $result_row['Email'],
            $result_row['Company'],
            $result_row['FirstName'],
            $result_row['LastName'],
            $result_row['PhoneNumber'],
            $result_row['Address'],
            $result_row['City'],
            $result_row['State'],
            $result_row['Zip'],
            $result_row['LifetimeDonation']
        );
        return $theDonor;
    }

    function checkDonorExists($email, $con) {
        $query = $con->prepare("SELECT Email FROM dbdonors WHERE Email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        return $result->num_rows > 0;
    }

    function addDonor($donorData, $con) {
        // Ensure email validation has already been done
        $email = trim($donorData[7]); // Already validated in upload.php

        // Ensure the order of `$donorData` elements matches the CSV columns order exactly (please work im begging)
        $query = $con->prepare("INSERT INTO dbdonors (Email, Company, FirstName, LastName, PhoneNumber, Address, City, State, Zip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("ssssissss", $email, $donorData[4], $donorData[5], $donorData[6], $donorData[8], $donorData[9], $donorData[10], $donorData[11], $donorData[12]);

        if (!$query->execute()) {
            error_log("Failed to add donor: " . $query->error);
        }
    }


    function combineDonor($donorData, $con) {
        // Prepare the SQL query to update an existing donor
        $query = $con->prepare("UPDATE dbdonors SET Company = ?, FirstName = ?, LastName = ?, PhoneNumber = ?, Address = ?, City = ?, State = ?, Zip = ? WHERE Email = ?");
        $query->bind_param("sssisssss", $donorData['Company'], $donorData['First Name'], $donorData['Last Name'], $donorData['Phone Number'], $donorData['Address'], $donorData['City'], $donorData['State'], $donorData['Zip'], $donorData['Email']);
        $query->execute();
    }

    function processDonorData($donorData, $con){
        // Assuming donorData has the email as the unique identifier in the first position -- KEY WORD IS ASSUMING!!!
        $donorEmail = $donorData[7];
        if (empty($donorEmail)) {
            // Handle rows without email or log an error
            error_log("Email column is empty for a row, skipping...");
            return;
        }

        // Check if donor exists
        $donorExists = checkDonorExists($donorEmail, $con);

        if (!$donorExists) {
            // Add new donor
            addDonor($donorData, $con);
        } else {
            // Combine donor data
            combineDonor($donorData, $con);
        }
    }
