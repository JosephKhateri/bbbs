<?php 
/**
 * @version April 6, 2023
 * @author Alip Yalikun
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
  require_once('database/dbEvents.php');
  require_once('include/output.php');
  require_once('database/dbinfo.php');
  
  
  $connection = connect();
  /*
  if(isset($_GET['animal'])){
    $selected_animal_name = $_GET['animal'];
    
    $query = "select * from dbAnimals where name = '$selected_animal_name'";
    $result = mysqli_query($connection, $query);
    $animal_info = mysqli_fetch_assoc($result);
    } else {
        echo "No animal selected!";
    }*/
  
  // Is user authorized to view this page?
  if ($accessLevel < 2) {
      header('Location: index.php');
      die();
  }
  
  $donorsOver10K = [];
  $donationQuery = "SELECT Email, SUM(AmountGiven) as TotalDonation FROM dbdonations GROUP BY Email HAVING TotalDonation > 10000";
  $donationResult = mysqli_query($connection, $donationQuery);
  while($row = mysqli_fetch_assoc($donationResult)) {
      $donorsOver10K[] = $row;
  }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>BigBrotherBigSister | Report Result</title>
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
                color: var(--button-font-color);
                border: 1px solid #333333;
                text-align: left;
                padding: 8px;
		        font-weight: 500;
            }
          
            tr:nth-child(even) {
                background-color: #f0f0f0;
                /* color:var(--button-font-color); */
		
            }

            @media print {
                tr:nth-child(even) {
                    background-color: white;
                }

                button, header {
                    display: none;
                }

                :root {
                    font-size: 10pt;
                }

                label {
                    color: black;
                }

                table {
                    width: 100%;
                }

                a {
                    color: black;
                }
            }

            .theB{
                width: auto;
                font-size: 15px;
            }
	        .center_a {
                margin-top: 3rem; /* Adjusted from 0 or a smaller value to 3rem */
                margin-bottom: 3rem;
                margin-left:auto;
                margin-right:auto;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .8rem;
            }
            .center_b {
                margin-top: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
		        gap: .8rem;
            }
            #back-to-top-btn {
                bottom: 20px;
            }
            .back-to-top:visited {
                color: white; /* sets the color of the link when visited */  
            }
            .back-to-top {
                color: white; /* sets the color of the link when visited */  
            }
	        .intro {
                display: flex;
                flex-direction: column;
                gap: .5rem;
                padding: 0 0 0 0;
            }
            .export-btn {
                padding: 10px 20px; 
                display: inline-block; 
                margin-top: 20px; 
                max-width: 200px;
            }
            .export-form {
                text-align: center;
                margin-top: 20px; 
            }

	    @media only screen and (min-width: 1024px) {
                .intro{
                    width: 80%;
                }
                main.report {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
            }
        footer {
            margin-bottom: 2rem;
        }
    </style>

    </head>
    <body>
  	<?php require_once('header.php') ?>
    
        
    <section>
    <?php
    // Check if the 'report' GET parameter is set to 'report1'
    if (isset($_GET['report']) && $_GET['report'] == 'report1') {
        // Modified SQL query to join Donations with Donors table and fetch required details
        $query = "SELECT d.Email, SUM(d.AmountGiven) AS TotalDonation, p.FirstName, p.LastName, p.PhoneNumber 
                FROM dbdonations AS d
                JOIN dbdonors AS p ON d.Email = p.Email
                GROUP BY d.Email
                HAVING TotalDonation > 10000";
        $result = mysqli_query($connection, $query);

            // Check if we have results
            if (mysqli_num_rows($result) > 0) {
                echo "<h2 style='text-align: center;'>List of Donors Who Donated Over $10,000</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Total Donation</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Format the phone number
                    $phone = $row['PhoneNumber'];
                    $formattedPhone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                
                    echo "<tr>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($formattedPhone) . "</td>
                            <td>$" . number_format($row['TotalDonation'], 2) . "</td>
                          </tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p>No donors have donated over $10,000.</p>";
            }
        }
        ?>

    </section>
    <form action="reportsExport.php" method="post" class="export-form">
    <input type="hidden" name="action" value="export_donors_over_10000">
    <input type="submit" value="Export Donors" class="export-btn">
    </form>




	
    </main>
	<div class="center_a">
                <a href="report.php">
                <button class = "theB">New Report</button>
                </a>
                <a href="index.php">
                <button class = "theB">Home Page</button>
                </a>
	</div>
        </main>
    </body>
</html>
