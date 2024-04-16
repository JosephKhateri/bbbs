<?php
 /**
 * @version April 6, 2023
 * @author Joel
 */


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

 require_once('include/input-validation.php');
 require_once('database/dbPersons.php');
 require_once('include/output.php');
 require_once('database/dbinfo.php');
 
 
 //$connection = connect();
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
    <title>BBBS | View Donor Info</title>
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
// Fetch donor details from the database
$query = "SELECT DonorID, FirstName, LastName FROM dbdonors";
$result = mysqli_query($connection, $query);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a donor is selected
    if (!empty($_POST['donor'])) {
        $donorID = $_POST['donor'];

        // Fetch the selected donor's details from the database
        $query = "SELECT * FROM dbdonors WHERE DonorID = $donorID";
        $result = mysqli_query($connection, $query);
        $donor = mysqli_fetch_assoc($result);
    }
}
?>  

<h2>Select a Donor to Edit</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="donor">Select Donor:</label>
    <select name="donor" id="donor">
        <option value="">Select Donor</option>
        <?php
        // Display donor names in dropdown list
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['DonorID'] . "'>" . $row['FirstName'] . " " . $row['LastName'] . "</option>";
        }
        ?>
    </select>
    <input type="submit" value="Submit">
</form>

<?php
// Display selected donor details for editing
if (!empty($donor)) {
    ?>
    <h2>Edit Donor Details</h2>
    <form method="post" action="editDonor_info.php">
        <input type="hidden" name="donorID" value="<?php echo $donor['DonorID']; ?>">
        <label for="firstName">First Name:</label>
        <input type="text" name="firstName" id="firstName" value="<?php echo $donor['FirstName']; ?>"><br><br>
        <label for="lastName">Last Name:</label>
        <input type="text" name="lastName" id="lastName" value="<?php echo $donor['LastName']; ?>"><br><br>
        <label for="lastName">Company:</label>
        <input type="text" name="Company" id="Company" value="<?php echo $donor['Company']; ?>"><br><br>
        <label for="lastName">Phone Number:</label>
        <input type="text" name="PhoneNumber" id="PhoneNumber" value="<?php echo $donor['PhoneNumber']; ?>"><br><br>
        <label for="lastName">Address:</label>
        <input type="text" name="Address" id="Address" value="<?php echo $donor['Address']; ?>"><br><br>
        <label for="lastName">City:</label>
        <input type="text" name="City" id="City" value="<?php echo $donor['City']; ?>"><br><br>
        <label for="lastName">State:</label>
        <input type="text" name="State" id="State" value="<?php echo $donor['State']; ?>"><br><br>
        <label for="lastName">Zip Code:</label>
        <input type="text" name="Zip" id="Zip" value="<?php echo $donor['Zip']; ?>"><br><br>
         
        <input type="submit" value="Update">
    </form>
    <?php
}
?>

        <br>
        <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
    </main>
</body>
</html>