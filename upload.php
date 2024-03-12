<?php

static function parseCSV($csvFilePath){
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
        
        echo 'CSV file parsed and saved successfully.';
    } else {
        echo 'Failed to open the CSV file.';
    }
}
?>
