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
            <?php elseif(isset($_GET['fileSuccess'])): ?>
                <div class="happy-toast">File uploaded successfully!</div>
            <?php elseif (isset($_GET['fileFail'])): ?>
                <div class="error-toast">File not uploaded correctly!</div>
            <?php elseif (isset($_GET['userNotFound'])): ?>
                <div class="happy-toast">User doesn't exist! Try again later!</div>
            <?php elseif (isset($_GET['noUsers'])): ?>
                <div class="happy-toast">No users exist for this application!</div> <!-- In the event that the database is empty -->
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
                </div>-->
                <!--<div class="dashboard-item" data-link="calendar.php">
                    <img src="images/view-calendar.svg">
                    <span>View Calendar</span>
                </div>-->
                <?php if ($_SESSION['access_level'] >= 2): ?>
                    <!--<div class="dashboard-item" data-link="addEvent.php">
                        <img src="images/new-event.svg">
                        <span>Add Appointment</span>
                    </div>-->
                <?php endif ?>
				<!--<div class="dashboard-item" data-link="addAnimal.php">
                    <img src="images/settings.png">
                    <span>Add Animal</span>
                </div>
				<div class="dashboard-item" data-link="addService.php">
                    <img src="images/settings.png">
                    <span>Add Service</span>
                </div>
				<div class="dashboard-item" data-link="addLocation.php">
                    <img src="images/settings.png">
                    <span>Add Location</span>
                </div>
                <div class="dashboard-item" data-link="findAnimal.php">
                        <img src="images/person-search.svg">
                        <span>Find Animal</span>
                </div>-->
                <!-- Commenting out because volunteers won't be searching events
                <div class="dashboard-item" data-link="eventSearch.php">
                    <img src="images/search.svg">
                    <span>Find Event</span>
                </div>
                -->
                <div class="dashboard-item" data-link="UploadForm.php">
                    <img src="images/volunteer-history.svg">
                    <span>Upload File</span>
                </div>
                <div class="dashboard-item" data-link="report.php">
                    <img src="images/create-report.svg">
                    <span>Create Report</span>
                </div>

                <!--<?php if ($_SESSION['access_level'] >= 2): ?>
                    <div class="dashboard-item" data-link="personSearch.php">
                        <img src="images/person-search.svg">
                        <span>Find Volunteer</span>
                    </div>
                    <div class="dashboard-item" data-link="register.php">
                        <img src="images/add-person.svg">
                        <span>Register Volunteer</span>
                    </div>
                    <div class="dashboard-item" data-link="viewArchived.php">
                        <img src="images/person-search.svg">
                        <span>Archived Animals</span>
                    </div>-->
                    <!--***added User Registration button ?***-->
                    <div class="dashboard-item" data-link="registerUserForm.php">
                        <img src="images/settings.png">
                        <span>Add User</span>
                    </div>
                    <div class="dashboard-item" data-link="adminResetPassword.php">
                        <img src="images/settings.png">
                        <span>Reset User Password</span>
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
                <?php endif ?>
                <?php if ($notRoot) : ?>
                    <!--<div class="dashboard-item" data-link="volunteerReport.php">
                        <img src="images/volunteer-history.svg">
                        <span>View My Hours</span>
                    </div>-->
                    <div class="dashboard-item" data-link="changePassword.php"> <!-- root user can't change password -->
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