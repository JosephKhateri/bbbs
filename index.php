<?php
    session_cache_expire(30);
    session_start();

    date_default_timezone_set("America/New_York");

    include_once('database/dbUsers.php');
    include_once('domain/User.php');
    include_once('include/api.php');
    
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        if (isset($_SESSION['change-password'])) {
            redirect('changePassword.php');
        } else {
            redirect('login.php');
        }
        die();
    }

    // Get date?
    if (isset($_SESSION['_id'])) {
        //$person = retrieve_person($_SESSION['_id']);
        $user = retrieve_user($_SESSION['_id']);
    }
    //$notRoot = $person->get_id() != 'vmsroot';
    $notRoot = $user->get_id() != 'vmsroot';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require('universal.inc'); ?>
        <title>BBBS Donor Information System | Dashboard</title>
    </head>
    <body>
        <?php require('header.php'); ?>
        <h1>Dashboard</h1>
        <main class='dashboard'>
            <?php if (isset($_GET['pcSuccess'])): ?>
                <div class="happy-toast">Password changed successfully!</div>
            <?php elseif (isset($_GET['pcFail'])): ?>
                <div class="happy-toast">Password change failed! Try again later!</div>
            <?php elseif (isset($_GET['registerSuccess'])): ?>
                <div class="happy-toast">User registered successfully!</div>
            <?php elseif (isset($_GET['removeSuccess'])): ?>
                <div class="happy-toast">User removed successfully!</div>    
            <?php elseif(isset($_GET['fileSuccess'])): ?>
                <div class="happy-toast">File uploaded successfully!</div>
            <?php elseif (isset($_GET['fileFail'])): ?>
                <div class="error-toast">File not uploaded correctly!</div>
            <?php elseif (isset($_GET['fileTypeFail'])): ?>
                <div class="error-toast">File is not a CSV and could not be uploaded!</div>
            <?php elseif (isset($_GET['userNotFound'])): ?>
                <div class="happy-toast">User doesn't exist! Try again later!</div>
            <?php elseif (isset($_GET['noUsers'])): ?>
                <div class="happy-toast">No users exist for this application!</div> <!-- In the event that the dbUsers database is empty -->
            <?php elseif (isset($_GET['noDonors'])): ?>
                <div class="happy-toast">No donors exist in the system!</div> <!-- In the event that the dbDonors database is empty -->
            <?php elseif (isset($_GET['phoneFormatFail'])): ?>
                <div class="error-toast">Invalid phone number format. Make sure the phone number contains no dashes and is 10 characters long</div>
            <?php elseif (isset($_GET['dateFormatFail'])): ?>
                <div class="error-toast">Invalid date format. Make sure the date is in YYYY-MM-DD format.</div>
            <?php elseif (isset($_GET['emailFormatFail'])): ?>
                <div class="error-toast">Invalid email. Try again with a correct email.</div>
            <?php elseif (isset($_GET['zipFormatFail'])): ?>
                <div class="error-toast">Invalid zip code. Make sure the Zip is only 5 numbers long.</div>
            <?php elseif (isset($_GET['uploadFail'])): ?>
                <div class="error-toast">There was an issue with uploading the data. Please try again later.</div>
            <?php endif ?>
            <p>Welcome back, <?php echo $user->get_first_name() ?>!</p>
            <p>Today is <?php echo date('l, F j, Y'); ?>.</p>
            <div id="dashboard">
                <div class="dashboard-item" data-link="viewAllDonors.php">
                    <img src="images/person.svg">
                    <span>View Donor Info</span>
                </div>
                <div class="dashboard-item" data-link="UploadForm.php">
                    <img src="images/volunteer-history.svg">
                    <span>Upload File</span>
                </div>
                <div class="dashboard-item" data-link="report.php">
                    <img src="images/create-report.svg">
                    <span>Create Report</span>
                </div>
                <?php if ($notRoot) : ?>
                    <div class="dashboard-item" data-link="changePassword.php">
                        <img src="images/change-password.svg">
                        <span>Change Password</span>
                    </div>
                <?php endif ?>
                <?php if ($_SESSION['access_level'] >= 2): ?>
                    <!--***added User Registration button ?***-->
                    <div class="dashboard-item" data-link="registerUserForm.php">
                        <img src="images/add-person.svg">
                        <span>Add User</span>
                    </div>
                     
                    <div class="dashboard-item" data-link="editDonationsInfo.php">
                        <img src="images/delete.svg">
                        <span>Edit Donations Information</span>
                    </div>
                    <div class="dashboard-item" data-link="adminResetPassword.php">
                        <img src="images/settings.png">
                        <span>Reset User Password</span>
                    </div>
                    <div class="dashboard-item" data-link="removeUserForm.php">
                        <img src="images/delete.svg">
                        <span>Remove User</span>
                    </div>
                <?php endif ?>
                <div class="dashboard-item" data-link="logout.php">
                    <img src="images/logout.svg">
                    <span>Log out</span>
                </div>
            </div>
        </main>
    </body>
</html>