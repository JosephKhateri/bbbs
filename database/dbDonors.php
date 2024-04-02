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

    function get_donor_retention($donorEmail) {
        $donations = retrieve_donations_by_email($donorEmail);

        // If the donor has no donations, return "No Donations"
        if (empty($donations)) {
            return "No Donations";
        }

        // Sort donations by date
        usort($donations, function($a, $b) {
            return strtotime($b->get_contribution_date()) - strtotime($a->get_contribution_date());
        });

        // Get the current date
        $current_date = time();

        // Get the date one year ago
        $one_year_ago = strtotime('-1 year', $current_date);

        // Get the date two years ago
        $two_years_ago = strtotime('-2 year', $current_date);

        // Get the date of the earliest donation
        $earliest_donation_date = strtotime($donations[count($donations) - 1]->get_contribution_date());

        // If the donor's first donation was from this year or within the last year
        if ($earliest_donation_date >= $one_year_ago) {
            return "New Donor";
        }

        // Check if the donor made a donation both within the past year and the year before
        $donations_last_year = array_filter($donations, function($donation) use ($one_year_ago) {
            return strtotime($donation->get_contribution_date()) >= $one_year_ago;
        });
        $donations_year_before = array_filter($donations, function($donation) use ($one_year_ago, $two_years_ago) {
            $donation_date = strtotime($donation->get_contribution_date());
            return $donation_date < $one_year_ago && $donation_date >= $two_years_ago;
        });
        if (!empty($donations_last_year) && !empty($donations_year_before)) {
            return "Multiyear Donor";
        }

        // Check if the donor donated over 2 years ago, then started donating again within the last year
        if ($earliest_donation_date <= $two_years_ago) {
            $donations_between_two_and_one_year_ago = array_filter($donations, function($donation) use ($one_year_ago, $two_years_ago) {
                $donation_date = strtotime($donation->get_contribution_date());
                return $donation_date < $two_years_ago && $donation_date >= $one_year_ago;
            });
            if (!empty($donations_between_two_and_one_year_ago)) {
                return "Returning Donor";
            }
        }

        // Check if the donor has not donated within the past year, but donated within the past 2 years
        if (!empty($donations_last_year) && empty($donations_year_before)) {
            return "Formerly Active Donor";
        }

        // If the donor has not donated in 2 or more years from today's date
        if ($earliest_donation_date <= $two_years_ago) {
            return "Inactive Donor";
        }

        // Default case if none of the above conditions met
        return "Unknown Donor";
    }

    function determine_donation_funnel($donorEmail) : string {
        /* Funnels:
            - INTERESTED: If the donor has donated at least once in the past three years
            - DONOR: If the donor has donated at least once a year in the past three years
            - ENGAGED: If the donor has donated at least three times in the last five years
            - LOYAL DONOR: If the donor has donated at least five times in the last five years
            - LEADERSHIP DONOR: If the donor has donated over $10,000 since they became a donor
            - N/A: if the donor doesn't fit into any funnel
        */

        // Get the number of donations within the last 3 and 5 years from the donor
        $donations_3_years = count_donations_within_years($donorEmail, 3);
        $donations_5_years = count_donations_within_years($donorEmail, 5);

        if (get_total_amount_donated($donorEmail) >= 10000) {
            return "LEADERSHIP DONOR";
        }
        elseif ($donations_5_years >= 5) {
            return "LOYAL DONOR";
        }
        elseif ($donations_3_years >= 3) {
            // this might need to be checked if they've donated at least once a year for 3 years straight, or if 2 years is enough
            // the processing isn't completely done here to check for donations from individual years
            return "DONOR";
        }
        elseif ($donations_5_years >= 3) {
            return "ENGAGED";
        }
        elseif ($donations_3_years >= 1) {
            return "INTERESTED";
        }
        else {
            return "N/A";
        }
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

        // Get today's date
        $current_date = date('Y-m-d');

        // Calculate three years ago from today
        $three_years_ago = date('Y-m-d', strtotime('-3 years', strtotime($current_date)));

        // Calculate three months ago from today
        $three_months_ago = date('Y-m-d', strtotime('-3 months', strtotime($current_date)));

        $query = "SELECT DATE_FORMAT(DateOfContribution, '%Y-%m-%d') AS donation_date FROM dbDonations WHERE Email = '" . $donorEmail . "'";
        $result = mysqli_query($con, $query);

        $donation_dates = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $donation_dates[] = $result_row['donation_date'];
        }

        // Sort the donation dates in ascending order
        sort($donation_dates);

        // Initialize counters
        $monthly_count = 0;

        // Track unique years of donations
        $unique_years = array();

        // Iterate through donation dates to categorize
        foreach ($donation_dates as $donation_date) {
            // Check if the donation is within the last 3 months
            if ($donation_date >= $three_months_ago) {
                $monthly_count++;
            }

            // Track unique years of donations
            $year = date('Y', strtotime($donation_date));
            if (!in_array($year, $unique_years)) {
                $unique_years[] = $year;
            }
        }

        // Check if there's at least one donation in each of the past three years
        $current_year = date('Y');
        $three_years_ago = $current_year - 3;
        $yearly_count = count($unique_years);

        // for each year within the past 3 years from today's date, check if there's at least one donation
        // I'm not totally sur eif this uses today's date or if it just uses the current year. this would need more examining to make it the former
        for ($i = 0; $i < 3; $i++) {
            if (!in_array($current_year - $i, $unique_years)) {
                $yearly_count = 0; // reset the count if there's a missing year
                break;
            }
        }


        // Determine the category based on counts
        if ($monthly_count >= 3) {
            $category = "Monthly";
        } elseif ($yearly_count >= 3) {
            $category = "Yearly";
        } else {
            $category = "Sporadic";
        }

        return $category;
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
