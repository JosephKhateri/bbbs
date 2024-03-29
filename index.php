<?php
    session_cache_expire(30);
    session_start();

    date_default_timezone_set("America/New_York");
    
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        if (isset($_SESSION['change-password'])) {
            header('Location: changePassword.php');
        } else {
            header('Location: login.php');
        }
        die();
    }
        
    include_once('database/dbUsers.php');
    include_once('domain/User.php');
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
            <!--<?php elseif (isset($_GET['deleteService'])): ?>
                <div class="happy-toast">Service successfully removed!</div>
            <?php elseif (isset($_GET['serviceAdded'])): ?>
                <div class="happy-toast">Service successfully added!</div>
            <?php elseif (isset($_GET['animalRemoved'])): ?>
                <div class="happy-toast">Animal successfully removed!</div>
            <?php elseif (isset($_GET['locationAdded'])): ?>
                <div class="happy-toast">Location successfully added!</div>
            <?php elseif (isset($_GET['deleteLocation'])): ?>
                <div class="happy-toast">Location successfully removed!</div>-->
            <?php elseif (isset($_GET['registerSuccess'])): ?>
                <div class="happy-toast">User registered successfully!</div>
            <?php elseif (isset($_GET['removeSuccess'])): ?>
                <div class="happy-toast">User removed successfully!</div>    
            <?php elseif(isset($_GET['fileSuccess'])): ?>
                <div class="happy-toast">File uploaded successfully!</div>
            <?php elseif (isset($_GET['fileFail'])): ?>
                <div class="error-toast">File not uploaded correctly!</div>
            <?php elseif (isset($_GET['userNotFound'])): ?>
                <div class="happy-toast">User doesn't exist! Try again later!</div>
            <?php elseif (isset($_GET['noUsers'])): ?>
                <div class="happy-toast">No users exist for this application!</div> <!-- In the event that the dbUsers database is empty -->
            <?php elseif (isset($_GET['noDonors'])): ?>
                <div class="happy-toast">No donors exist in the system!</div> <!-- In the event that the dbDonors database is empty -->
            <?php endif ?>
            <p>Welcome back, <?php echo $user->get_first_name() ?>!</p>
            <p>Today is <?php echo date('l, F j, Y'); ?>.</p>
            <div id="dashboard">
                <?php
                    require_once('database/dbMessages.php');
                    $unreadMessageCount = get_user_unread_count($user->get_id());
                    $inboxIcon = 'inbox.svg';
                    if ($unreadMessageCount) {
                        $inboxIcon = 'inbox-unread.svg';
                    }
                ?>
                <!--<div class="dashboard-item" data-link="inbox.php">
                    <img src="images/<?php echo $inboxIcon ?>">
                    <span>Notifications<?php 
                        /*if ($unreadMessageCount > 0) {
                            echo ' (' . $unreadMessageCount . ')';
                        }*/
                    ?></span>
                </div> -->

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
                <?php if ($_SESSION['access_level'] >= 2): ?>
                    <!--***added User Registration button ?***-->
                    <div class="dashboard-item" data-link="registerUserForm.php">
                        <img src="images/add-person.svg">
                        <span>Add User</span>
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
                <?php if ($notRoot) : ?>
                    <div class="dashboard-item" data-link="viewProfile.php">
                        <img src="images/view-profile.svg">
                        <span>View Profile</span>
                    </div>
                    <div class="dashboard-item" data-link="editProfile.php">
                        <img src="images/manage-account.svg">
                        <span>Edit Profile</span>
                    </div>
                    <div class="dashboard-item" data-link="changePassword.php">
                        <img src="images/change-password.svg">
                        <span>Change Password</span>
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