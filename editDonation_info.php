<?php
    // Edited by Megan and Noor for BBBS in Spring 2024
    // Purpose: Allows users to view all donors

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    ini_set("display_errors",1);
    error_reporting(E_ALL);

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
        header('Location: login.php');
        die();
    }

    require_once('include/input-validation.php');
  require_once('database/dbPersons.php');
  require_once('include/output.php');
  require_once('database/dbinfo.php');
  
  
  //$connection = connect();
  $servername = "localhost";
  $username = "bbbs";
  $password = "bbbs";
  $dbname = "bbbs";
  
  // Create connection
  $connection = mysqli_connect($servername, $username, $password, $dbname);
  
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
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $DonationID = $_POST['DonationID'];
    $Email = $_POST['Email'];
    $DateOfContribution = $_POST['DateOfContribution'];
    $ContributionSupportType = $_POST['ContributionSupportType'];
    $ContributionCategory = $_POST['Con tributionCategory'];
    $AmountGiven = $_POST['AmountGiven'];
    $PaymentMethod = $_POST['PaymentMethod'];
    $Memo = $_POST['Memo'];
    // Add other fields as needed

    // Update donor details in the database
    $query = "UPDATE dbdonations SET Email='$Email', DateOfContribution='$DateOfContribution', 
    ContributionSupportType='$ContributionSupportType', 
    Con tributionCategory='$ContributionCategory', AmountGiven='$AmountGiven', 
    DateOfContribution='$DateOfContribution', PaymentMethod='$PaymentMethod', Memo='$Memo' WHERE DonorID=$donorID";
    // Add other fields to the query if needed

    if (mysqli_query($connection, $query)) {
        echo "Donation details updated successfully.";
    } else {
        echo "Error updating donation details: " . mysqli_error($connection);
    }
}
?>

        <br>
        <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
    </main>
</body>
</html>