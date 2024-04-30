<?php
 /**
 * @version April 6, 2023
 * @author Joel
 */


    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    require_once('include/input-validation.php');
    require_once('include/output.php');
    require_once('database/dbinfo.php');
    require_once('include/api.php');

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }

    // Require user privileges
    if ($accessLevel < 1) {
        redirect('login.php');
        die();
    }

    require_once('include/input-validation.php');
  require_once('include/output.php');
  require_once('database/dbinfo.php');
  
  
  $connection = connect();
  
  // Check connection
  if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
    
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>BBBS | Edit Donation Details</title>
    <style>
        /* Targeting the select element and option elements */
        select, option, input {
            color: white; /* Setting the font color to white */
            background-color: #333; /* A darker background for contrast */
        }

        select {
            -webkit-appearance: none; /* For some WebKit browsers */
            -moz-appearance: none;    /* For Firefox */
            appearance: none;         /* Standard syntax */
        }

        /* Optionally, style the select box to include a custom arrow icon */
        /*select {
            background-image: url('path-to-your-custom-arrow-icon');
            background-repeat: no-repeat;
            background-position: right .7em top 50%;
            background-size: .65em auto;
        }*/
    </style>

</head>
<body>
    <?php require_once('header.php') ?>
    <h1>Donors</h1>
    <main class="date">

         

        <style>
            table {
                margin-top: 1rem;
                margin-left: auto;
                margin-right: auto;
                border-collapse: collapse;
                width: 80%;
            }
            td {
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
            }
            th {
                background-color: var(--main-color);
                color: black;
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
                font-weight: 500;
            }
            footer {
                margin-bottom: 5rem;
            }
        </style>
 <?php
// Ensure that the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are set
    if (isset($_POST['Email']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['Company']) && isset($_POST['PhoneNumber']) && isset($_POST['Address']) && isset($_POST['City']) && isset($_POST['State']) && isset($_POST['Zip'])) {
        // Database connection
        require_once('database/dbinfo.php'); // Include your database connection file here
        
        // Create connection
        $connection = connect();
        
        // Check connection
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        // Prepare SQL statement to update donor information
        $email = $_POST['Email'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $company = $_POST['Company'];
        $phoneNumber = $_POST['PhoneNumber'];
        $address = $_POST['Address'];
        $city = $_POST['City'];
        $state = $_POST['State'];
        $zip = $_POST['Zip'];
        
        $sql = "UPDATE dbdonors SET FirstName='$firstName', LastName='$lastName', Company='$company', PhoneNumber='$phoneNumber', Address='$address', City='$city', State='$state', Zip='$zip' WHERE Email='$email'";
        
        if (mysqli_query($connection, $sql)) {
            // Success message
            echo "Donor information updated successfully<br>";
             
        } else {
            // Error message
            echo "Error updating donor information: " . mysqli_error($connection);
             
        }
        
        // Close connection
        mysqli_close($connection);
    } else {
        echo "All fields are required";
    }
} else {
    echo "Invalid request";
}
?>

<br>
         <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
     </main>
 </body>
 </html>

