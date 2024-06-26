<?php

    require_once('database/dbinfo.php');
    require_once(dirname(__FILE__) . '/../domain/Donor.php');

    /*
     * Parameters: $id = A string that represents the ID number of a donation
     * This function retrieves a donation from the dbdonations table using the ID of the donation
     * Return type: A Donation object
     * Pre-condition: $id is a string
     * Post-condition: A Donation object is returned if it exists, otherwise nothing is returned
     */
    function retrieve_donor($email) {
        $con=connect();
        $query = "SELECT * FROM dbdonors WHERE Email = '" . $email . "'";
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
    * This function retrieves all donors from the dbdonors table and returns an array of donor objects
    * Return type: Array of donors
    * Pre-condition: None
    * Post-condition: Array of donors is returned
    */
    function get_all_donors() {
        $con = connect();
        $query = 'SELECT * FROM dbdonors';
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

    function get_filtered_donors($cityFilters, $stateFilters) : array {
        $con = connect();
        $filteredDonors = [];

        $sql = "SELECT * FROM dbdonors WHERE City = ? AND State = ?";
        $stmt = $con->prepare($sql);

        // Iterate through each pair of city and state filters
        foreach ($cityFilters as $index => $city) {
            $state = $stateFilters[$index];

            // Bind the parameters to the SQL statement
            $stmt->bind_param("ss", $city, $state);

            // Execute the SQL statement
            $stmt->execute();

            // Get the result of the SQL statement
            $result = $stmt->get_result();

            // Iterate through each row in the result
            while ($row = $result->fetch_assoc()) {
                // Create a donor object and add it to the filtered donors array
                $donor = make_a_donor($row);
                $filteredDonors[] = $donor;
            }
        }

        return $filteredDonors;
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function retrieves all donations made by a donor using the donor's email
     * Return type: A string representing the donor's retention status (New Donor, Multiyear Donor, Returning Donor, Formerly Active Donor, Inactive Donor, or Unknown Donor)
     * Pre-condition: $donorEmail is a string
     * Post-condition: The donor's retention status is returned
     */
    function get_donor_status($donorEmail) : string {
        $donations = retrieve_donations_by_email($donorEmail);

        // If the donor has no donations, return "No Donations"
        if (empty($donations)) {
            return "No Donations";
        }

        // Sort donations by date
        sort($donations);

        // Get date variables
        $one_year_ago = date('Y-m-d', strtotime('-1 year')); // Date of one year ago from today
        $two_years_ago = date('Y-m-d', strtotime('-2 year')); // Date of two years ago from today
        $earliest_donation_date = end($donations)->get_contribution_date(); // Date of donor's first donation
        $date_of_last_donation = $donations[0]->get_contribution_date(); // Date of donor's last donation

        // If the donor's first donation was from this year or within the last year
        if ($earliest_donation_date >= $one_year_ago) {
            return "New Donor";
        }

        // Check if the donor made a donation both within the past year and the year before
        $donations_last_year = array_filter($donations, function($donation) use ($one_year_ago) {
            return $donation->get_contribution_date() >= $one_year_ago;
        });
        $donations_year_before = array_filter($donations, function($donation) use ($one_year_ago, $two_years_ago) {
            $donation_date = $donation->get_contribution_date();
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
        if ($date_of_last_donation >= $two_years_ago && $date_of_last_donation < $one_year_ago) {
            return "Formerly Active Donor";
        }

        // If the donor has not donated in 2 or more years from today's date
        if ($date_of_last_donation <= $two_years_ago) {
            return "Inactive Donor";
        }

        // Default case if none of the above conditions met (shouldn't happen)
        return "Unknown Donor";
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function sorts the donor into a donation funnel based on their donation history
     * Return type: A string that represents the donation funnel the donor falls into
     * Pre-condition: $donorEmail is a string
     * Post-condition: The donation funnel the donor falls into is returned or "N/A" if the donor doesn't fit into any funnel
     */
    function determine_donation_funnel($donorEmail) : string {
        /* Funnels:
            - INTERESTED: If the donor has donated at least once in the past three years
            - DONOR: If the donor has donated at least once a year in the past three years
            - ENGAGED: If the donor has donated at least three times in the last five years
            - LOYAL DONOR: If the donor has donated at least five times in the last five years
            - LEADERSHIP DONOR: If the donor has donated over $10,000 since they became a donor
            - N/A: if the donor doesn't fit into any funnel
        */

        $current_date = date('Y-m-d'); // Get the current date
        $three_years_ago = date('Y-m-d', strtotime('-3 years', strtotime($current_date))); // Get the date three years ago

        if (get_total_amount_donated($donorEmail) >= 10000) {
            return "Leadership Donor";
        }

        $con = connect();
        $query = "SELECT DATE_FORMAT(DateOfContribution, '%Y-%m-%d') AS donation_date FROM dbdonations WHERE Email = '" . $donorEmail . "'";
        $result = mysqli_query($con, $query);

        $donation_dates = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $donation_dates[] = $result_row['donation_date'];
        }

        sort($donation_dates); // Sort the donations by oldest to newest

        // if donation dates is empty (no donations were made), return an empty string
        if (empty($donation_dates)) {
            return "";
        }
        // Get date of oldest donation
        $oldest_donation = $donation_dates[0];

        // If oldest donation was over 3 years ago
        if ($oldest_donation < $three_years_ago) {
            $num_donations = 0; // Initialize number of donations

            // Check how many times the donor has donated in the last five years
            $num_years = 5; // Number of years to check for donations
            for ($i = 1; $i <= $num_years; $i++) {
                // Get the year to check for donations
                $year_to_check = date('Y-m-d', strtotime("-$i years", strtotime($current_date)));
                $year_to_check_plus_one = date('Y-m-d', strtotime("+1 year", strtotime($year_to_check))); // Get the year after the year to check

                // Iterate through donation dates to find donations within the year being checked
                foreach ($donation_dates as $donation_date) {
                    if ($donation_date >= $year_to_check && $donation_date <= $year_to_check_plus_one) {
                        $num_donations++;
                    }
                }
            }

            if ($num_donations >= 5) { // If the donor has donated at least five times in the last five years
                return "Loyal Donor";
            } elseif ($num_donations >= 3) { // If the donor has donated at least three times in the last five years
                return "Engaged";
            } else { // Donation was over 5 years ago or donor made less than 3 donations in the last 5 years
                return "N/A";
            }
        } else {
            // Check if the donor has donated at least once a year in the past three years
            $num_years = 3; // Number of years to check for donations
            $yearly_count = check_donations_for_past_x_years($donation_dates, $num_years);

            if ($yearly_count == 3) { // If the donor has donated at least once a year in the past three years
                return "Donor";
            } elseif ($yearly_count >= 1) { // If the donor has donated at least once in the past three years
                return "Interested";
            } else { // If the donor doesn't fit into any funnel after all checks
                return "N/A";
            }
        }
    }

    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function sorts the donor into a donation funnel based on their donation history
     * Return type: A string that represents the donation funnel the donor falls into
     * Pre-condition: $donorEmail is a string
     * Post-condition: The donation funnel the donor falls into is returned or "N/A" if the donor doesn't fit into any funnel
     */
    function determine_donation_GTY($donorEmail) : string {
        $con = connect();
        $query = "SELECT DATE_FORMAT(DateOfContribution, '%Y-%m-%d') AS donation_date FROM dbdonations WHERE Email = '" . $donorEmail . "'";
        $result = mysqli_query($con, $query);

        $donation_dates = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $donation_dates[] = $result_row['donation_date'];
        }

        sort($donation_dates); // Sort the donations by oldest to newest

        // if donation dates is empty (no donations were made), return an empty string
        if (empty($donation_dates)) {
            return "";
        }

        $num_donations_current_year = 0; // Initialize number of donations (current year)
        $num_donations_previous_year = 0; // Initialize number of donations (previous year)

        $current_date = date('Y-m-d');

        // Check number of donations for the current year
        $current_year = date('Y-m-d', strtotime("-1 years", strtotime($current_date)));
        $previous_year = date('Y-m-d', strtotime("+1 year", strtotime($current_year))); // Get the year after the year to check
        foreach ($donation_dates as $donation_date) { // Iterate through donation dates to find donations within the year being checked
            if ($donation_date >= $current_year && $donation_date <= $previous_year) {
                $num_donations_current_year++; // Increment if a donation was found for the current year
            }
        }

        // Get number of donations for the previous year
        $current_year = date('Y-m-d', strtotime("-2 years", strtotime($current_date)));
        $previous_year = date('Y-m-d', strtotime("+1 year", strtotime($current_year))); // Get the year after the year to check
        foreach ($donation_dates as $donation_date) { // Iterate through donation dates to find donations within the year being checked
            if ($donation_date >= $current_year && $donation_date <= $previous_year) {
                $num_donations_previous_year++; // Increment if a donation was found for the previous year
            }
        }

        // Check if the donor has donated more than once each year for each of the past 2 years
        if ($num_donations_current_year >= 2 && $num_donations_previous_year >= 2) {
            return "Greater Than Yearly";
        } else {
            return "NGY"; // The donor does not donate more than once a year for the past 2 years
        }
    }
    /*
     * Parameters: $donorEmail = A string that represents the email of a donor
     * This function retrieves the donation frequency of a donor
     * Return type: A string that represents the donation frequency of the donor
     * Pre-condition: $donorEmail is a string
     * Post-condition: The donation frequency of the donor is returned (Monthly, Yearly, or Sporadic)
     */
    function get_donation_frequency($donorEmail) : string {
        $con = connect();

        // Get today's date
        $current_date = date('Y-m-d');

        // Calculate three months ago from today
        $two_months_ago = date('Y-m-d', strtotime('-2 months', strtotime($current_date)));

        $query = "SELECT DATE_FORMAT(DateOfContribution, '%Y-%m-%d') AS donation_date FROM dbdonations WHERE Email = '" . $donorEmail . "'";
        $result = mysqli_query($con, $query);

        $donation_dates = array();
        while ($result_row = mysqli_fetch_assoc($result)) {
            $donation_dates[] = $result_row['donation_date'];
        }

        // Sort the donation dates in ascending order
        sort($donation_dates);

        // Initialize counter for monthly donations
        $monthly_count = 0;

        // Iterate through donation dates to categorize
        foreach ($donation_dates as $donation_date) {
            // Check if the donation is within the last 3 months
            if ($donation_date >= $two_months_ago) {
                $monthly_count++;
            }
        }

        // Check if the donor has donated at least once each year for the past two years
        $num_years = 2; // Number of years to check for donations
        $yearly_count = check_donations_for_past_x_years($donation_dates, $num_years);

        // Determine the category based on counts
        if ($monthly_count >= 2) {
            $category = "Monthly";
        } elseif ($yearly_count >= 2) {
            $category = "Yearly";
        } else {
            $category = "Sporadic";
        }

        return $category;
    }

    /*
     * Parameters: $term = A string that represents a term
     * This function retrieves the description of a term from an associated array (dictionary)
     * Return type: A string that represents the description of the term
     * Pre-condition: $term is a string
     * Post-condition: The description of the term is returned
     */
    function get_description($term) : string {
        $descriptions = array(
            // Retention status descriptions
            "New Donor" => "Donor made their first donation within the past year",
            "Multiyear Donor" => "Donor made a donation both within the past year and the year before",
            "Returning Donor" => "Donor donated over 2 years ago, then started donating again within the last year",
            "Formerly Active Donor" => "Donor has not donated within the past year, but has donated within the past 2 years",
            "Inactive Donor" => "Donor has not donated in 2 or more years from today's date",

            // Donation funnel descriptions
            "Interested" => "Donor has donated at least once in the past 3 years",
            "Donor" => "Donor has donated at least once a year in the past 3 years",
            "Engaged" => "Donor has donated at least 3 times in the past 5 years",
            "Loyal Donor" => "Donor has donated at least 5 times in the past 5 years",
            "Leadership Donor" => "Donor has donated over $10,000",

            // Donation frequency descriptions
            "Monthly" => "Donor has donated at least once each month for the past 2 months",
            "Yearly" => "Donor has donated at least once each year for the past 2 years",
            "Sporadic" => "Donor donates inconsistently"
        );

        if (array_key_exists($term, $descriptions)) {
            // Return the corresponding value
            return $descriptions[$term];
        }
        else {
            // Return an empty string if the term is not found
            return "";
        }
    }

    /*
     * Parameters: $result_row = An associative array that represents a row in the dbdonations table
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
