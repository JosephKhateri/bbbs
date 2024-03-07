<?php

// Assuming the CSV filename is known and static for demonstration; adjust as needed
//$csvFileName = 'example.csv'; 
//$csvFilePath = '/Users/josephkhateri/Downloads/CPSC430CSVTEST/' . $csvFileName;

public static function parseCSV($csvFilePath){

    // Check if the CSV file exists
    if (!file_exists($csvFilePath)) {
        die("CSV file does not exist at the specified path.");
    }

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

        // Put it into donor.php
        file_put_contents('donor.php', "<?php \$parsedCSV = " . $parsedCSV . "; ?>");
        
        echo 'CSV file parsed and saved successfully.';
    } else {
        echo 'Failed to open the CSV file.';
    }
}
?>
