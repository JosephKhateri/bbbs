<?php

// Path to the CSV file
$csvFile = '/path/to/your/csv/file.csv';

// Open the CSV file
$file = fopen($csvFile, 'r');

// Check if the file was opened successfully
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

    // Save the parsed CSV data into donor.php
    file_put_contents('donor.php', '<?php $parsedCSV = ' . $parsedCSV . ';');
    
    echo 'CSV file parsed and saved successfully.';
} else {
    echo 'Failed to open the CSV file.';
}