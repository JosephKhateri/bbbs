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
// Fetch donor details from the database
$query = "SELECT DonationID, Email, ContributionSupportType, AmountGiven FROM dbdonations";
$result = mysqli_query($connection, $query);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a donor is selected
    if (!empty($_POST['donor'])) {
        $donationID = $_POST['donor'];

        // Fetch the selected donor's details from the database
        $query = "SELECT * FROM dbdonations WHERE DonationID = $donationID";
        $result = mysqli_query($connection, $query);
        $donation = mysqli_fetch_assoc($result);
    }
}
?>  

<h2>Select a Donation to Edit</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="donor">Select Donation:</label>
    <select name="donar" id="donar">
        <option value="">Select Donation</option>
        <?php
        // Display donations in dropdown list
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['DonationID'] . "'>" . $row['Email'] . " " . $row['ContributionSupportType'] . " " . $row['AmountGiven'] . "</option>";
        }
        ?>
    </select>
    <input type="submit" value="Submit">
</form>

<?php
// Display selected donor details for editing
if (!empty($donor)) {
    ?>
    <h2>Edit Donation Details</h2>
    <form method="post" action="editDonation_info.php">
        <input type="hidden" name="donationID" value="<?php echo $donation['DonationID']; ?>">
        <label for="firstName">Donar Email:</label>
        <input type="text" name="Email" id="Email" value="<?php echo $donation['Email']; ?>"><br><br>
        <label for="lastName">Date of Contribution:</label>
        <input type="text" name="DateOfContribution" id="DateOfContribution" value="<?php echo $donation['DateOfContribution']; ?>"><br><br>
        <label for="lastName">Contribution Support Type:</label>
        <input type="text" name="ContributionSupportType" id="ContributionSupportType" value="<?php echo $donation['ContributionSupportType']; ?>"><br><br>
        <label for="lastName">Contribution Category:</label>
        <input type="text" name="Con tributionCategory" id="Con tributionCategory" value="<?php echo $donation['Con tributionCategory']; ?>"><br><br>
        <label for="lastName">Amount Given:</label>
        <input type="text" name="AmountGiven" id="AmountGiven" value="<?php echo $donation['AmountGiven']; ?>"><br><br>
        <label for="lastName">Payment Method:</label>
        <input type="text" name="PaymentMethod" id="PaymentMethod" value="<?php echo $donation['PaymentMethod']; ?>"><br><br>
        <label for="lastName">Memo:</label>
        <input type="text" name="Memo" id="Memo" value="<?php echo $donation['Memo']; ?>"><br><br>
         
         
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