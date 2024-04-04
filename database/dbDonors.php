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
                $donor->get_zip() . '");'
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
            $result_row['Zip']
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


?>
