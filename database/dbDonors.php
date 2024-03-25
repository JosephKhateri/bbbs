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
    function retrieve_donation($id) {
        $con=connect();
        $query = "SELECT * FROM dbDonations WHERE id = '" . $id . "'";
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
     * Parameters: $email = A string that represents the email a donation is associated with
     * This function retrieves an array of Donations from the dbDonations table that match the given email
     * Return type: An array of Donation objects
     * Pre-condition: $email is a string
     * Post-condition: An array of Donation objects is returned
     */
    function retrieve_donations_by_email ($email) {
        $donations = array();
        if (!isset($email) || $email == "" || $email == null) return $donations;
        $con=connect();
        $query = "SELECT * FROM dbDonations WHERE email = '" . $email ."'";
        $result = mysqli_query($con,$query);
        while ($result_row = mysqli_fetch_assoc($result)) {
            $the_donation = make_a_donor($result_row);
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
    function get_all_donations() {
        $con=connect();
        $query = 'SELECT * FROM dbDonations';
        $result = mysqli_query($con,$query);
        if ($result == null || mysqli_num_rows($result) == 0) {
            mysqli_close($con);
            return false;
        }
        $result = mysqli_query($con,$query);
        $theDonations = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $theDonation = make_a_donor($result_row);
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
    function make_a_donation($result_row) {
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
    
    /*
    * Parameters: None
    * This function retrieves all donors from the dbDonors table and returns an array of donor objects
    * Return type: array of donors
    * Pre-condition: None
    * Post-condition: array of donors is returned
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

?>
