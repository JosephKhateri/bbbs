<?php
 /**
 * @version April 6, 2023
 * @author Joel
 */

/*
 session_cache_expire(30);
 session_start();
 ini_set("display_errors",1);
 error_reporting(E_ALL);
 $loggedIn = false;
 $accessLevel = 0;
 $userID = null;
 //if (isset($_SESSION['_id'])) {
     $loggedIn = true;
     // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
     $accessLevel = 3;//$_SESSION['access_level'];
     $userID = 'vmsroot';//$_SESSION['_id'];
 //}
*/

 require_once('include/input-validation.php');
 require_once('database/dbPersons.php');
 require_once('include/output.php');
 require_once('database/dbinfo.php');
 

 // Create connection
 $connection = connect();
 
 // Check connection
 if (!$connection) {
     die("Connection failed: " . mysqli_connect_error());
 }
 
 // Fetch donor details from the database
 $query = "SELECT Email, FirstName, LastName, Company, PhoneNumber, Address, City, State, Zip FROM dbdonors";
 $result = mysqli_query($connection, $query);
 
 // Check if any donor found
 if ($result && mysqli_num_rows($result) > 0) {
     $donor = mysqli_fetch_assoc($result);
 } else {
     echo "No donor found.";
 }
 
 // Close connection
 mysqli_close($connection);
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
             /* Styles for the table and form */
             /* Define your styles here */
         </style>
 
         <?php
         // Display selected donor details for editing
         if (!empty($donor)) {
             ?>
             <h2>Edit Donor Details</h2>
             <form method="post" action="editDonor_info.php">
                 <!-- Populate form fields with donor details -->
                 <input type="hidden" name="Email" value="<?php echo $donor['Email']; ?>">
                 <label for="firstName">First Name:</label>
                 <input type="text" name="firstName" id="firstName" value="<?php echo $donor['FirstName']; ?>"><br><br>
                 <label for="lastName">Last Name:</label>
                 <input type="text" name="lastName" id="lastName" value="<?php echo $donor['LastName']; ?>"><br><br>
                 <label for="Company">Company:</label>
                 <input type="text" name="Company" id="Company" value="<?php echo $donor['Company']; ?>"><br><br>
                 <label for="PhoneNumber">Phone Number:</label>
                 <input type="text" name="PhoneNumber" id="PhoneNumber" value="<?php echo $donor['PhoneNumber']; ?>"><br><br>
                 <label for="Address">Address:</label>
                 <input type="text" name="Address" id="Address" value="<?php echo $donor['Address']; ?>"><br><br>
                 <label for="City">City:</label>
                 <input type="text" name="City" id="City" value="<?php echo $donor['City']; ?>"><br><br>
                 <label for="State">State:</label>
                 <input type="text" name="State" id="State" value="<?php echo $donor['State']; ?>"><br><br>
                 <label for="Zip">Zip Code:</label>
                 <input type="text" name="Zip" id="Zip" value="<?php echo $donor['Zip']; ?>"><br><br>
                 <input type="submit" value="Update">
             </form>
             <?php
         } else {
             echo "No donor found.";
         }
         ?>
 
         <br>
         <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
     </main>
 </body>
 </html>
 