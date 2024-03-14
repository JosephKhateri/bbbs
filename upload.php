<?php

function parseCSV($csvFilePath){

    require_once('include/input-validation.php');
    require_once('database/dbPersons.php'); //Replace with dbDonors
    include_once('database/dbinfo.php'); //Replace with dbDonations
    $con=connect(); 

    // Open the CSV file
    $file = fopen($csvFilePath, 'r');

    if ($file) {
        // Array to store the parsed CSV data
        $data = [];

        // Read each line of the CSV file
        while (($line = fgetcsv($file)) !== false) {
            // Add the line to the data array
            $data[] = $line;
        }

        // Close the CSV file
        fclose($file);

        // Convert the data array to a string
        $parsedCSV = var_export($data, true);
        /*
        //For seeing contents of csv before uploading to db
        foreach($data as $line){
            foreach($line as $var){
                echo $var . "<br>";
            }
        }
        */
        
        //
        echo 'CSV file parsed and saved successfully.';
    } else {
        echo 'Failed to open the CSV file.';
    }
}
?>
