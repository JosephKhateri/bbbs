<?php
 /**
 * @version April 6, 2023
 * @author Joel
 */


 session_cache_expire(30);
 session_start();
 ini_set("display_errors",1);
 error_reporting(E_ALL);

require_once('include/api.php');
require_once('include/input-validation.php');
require_once('include/output.php');
require_once('database/dbinfo.php');

$loggedIn = false;
$accessLevel = 0;
$userID = null;

// Check if user is logged in
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level']; // Assuming this is set when the user logs in
    $userID = $_SESSION['_id'];
}

// Require user privileges
if ($accessLevel < 1) {
    redirect('login.php');
    die();
}
 
 
 // Create connection
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

<script>
    /*Script making the Get button inactive if no donor is selected*/
    function toggleSubmit() {
        var donorSelect = document.getElementById("donor");
        var submitButton = document.getElementById("submitButton");
        
        // Check if a donor has been selected
        if (donorSelect.value !== "") {
            submitButton.disabled = false; // Enable the submit button
        } else {
            submitButton.disabled = true; // Disable the submit button
        }
    }
</script>

    

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
$query = "SELECT DonationID, Email FROM dbdonations";
$result = mysqli_query($connection, $query);  
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['donor'])) {
    $donationID = $_GET['donor'];

    // Fetch the selected donor's details from the database
    $query = "SELECT * FROM dbdonations WHERE DonationID = $donationID";
    $result = mysqli_query($connection, $query);

    // Check if any donation found
    if ($result && mysqli_num_rows($result) > 0) {
        $donation = mysqli_fetch_assoc($result);
    } else {
        echo "No donation found.";
    }
}

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
<h2>Select a Donation to Edit</h2>
<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="donor">Select Donation:</label>
    <select name="donor" id="donor" onchange="toggleSubmit()">
        <option value="">Select Donation</option>
        <?php
        // Display donor names in dropdown list
        $query = "SELECT do.DonationID, do.DateOfContribution, do.AmountGiven, 
        d.FirstName, d.LastName
        FROM dbDonations do
        INNER JOIN dbDonors d ON do.Email = d.Email";
        $result = mysqli_query($connection, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['DonationID'] . "'>" . $row['FirstName'] . " &nbsp " . $row['LastName'] . " &nbsp &nbsp " . $row['DateOfContribution'] . " &nbsp &nbsp " . $row['AmountGiven'] . "</option>";
        }
        ?>
    </select>
    <input type="submit" value="Get" id="submitButton" disabled>
</form>

<?php
// Display selected donor details for editing
if (!empty($donation)) {
    ?>
    <h2>Edit Donation Details</h2>
    <form method="post" action="editDonation_info.php">
        <input type="hidden" name="DonationID" value="<?php echo $donation['DonationID']; ?>">
        <label for="Email">Donor Email:</label>
        <input type="text" name="Email" id="Email" value="<?php echo $donation['Email']; ?>"><br><br>
        <label for="DateOfContribution">Date of Contribution:</label>
        <input type="text" name="DateOfContribution" id="DateOfContribution" value="<?php echo $donation['DateOfContribution']; ?>"><br><br>
        <label for="ContributionSupportType">Contribution Support Type:</label>
        <input type="text" name="ContributedSupportType" id="ContributedSupportType" value="<?php echo $donation['ContributedSupportType']; ?>"><br><br>
        <label for="ContributionCategory">Contribution Category:</label>
        <input type="text" name="ContributionCategory" id="ContributionCategory" value="<?php echo $donation['ContributionCategory']; ?>"><br><br>
        <label for="AmountGiven">Amount Given:</label>
        <input type="text" name="AmountGiven" id="AmountGiven" value="<?php echo $donation['AmountGiven']; ?>"><br><br>
        <label for="PaymentMethod">Payment Method:</label>
        <input type="text" name="PaymentMethod" id="PaymentMethod" value="<?php echo $donation['PaymentMethod']; ?>"><br><br>
        <label for="Memo">Memo:</label>
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