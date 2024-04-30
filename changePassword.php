<?php

    // Template for new VMS pages. Base your new page on this one

    // Edited by Megan and Noor for BBBS in Spring 2024
    // Purpose: Allows users to change their password
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    require_once('include/api.php');
    require_once('include/input-validation.php');
    require_once('domain/User.php');
    require_once('database/dbUsers.php');

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level']; // need to test this out to see if it returns string (user, admin) or int (1, 2_
        $userID = $_SESSION['_id'];
    }

    // Require user privileges
    if ($accessLevel < 1) {
        redirect('login.php');
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!wereRequiredFieldsSubmitted($_POST, array('password', 'new-password'))) {
            echo "Args missing";
            die();
        }
        $password = $_POST['password'];
        $newPassword = $_POST['new-password'];
        $user = retrieve_user($userID);

        if (!$user) { // user doesn't exist
            redirect('index.php?userNotFound');
            die();
        } else if (!password_verify($password, $user->get_password())) {
            $error1 = true; // old password provided is incorrect
        } else if($password == $newPassword) { // old password is same as new one
            $error2 = true; // old password is same as new one
        } else if (validatePassword($newPassword) === false) {
            $error3 = true; // new password doesn't meet requirements
        } else {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $change_password_result = change_password($userID, $hash);
            if ($change_password_result === false) { // password change failed
                redirect('index.php?pcFail');
            } else { // password change succeeded
                redirect('index.php?pcSuccess');
            }
            die();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>BBBS | Change Password</title>
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
        <h1>Change Password</h1>
        <main class="login">
            <!-- Error messages -->
            <?php if (isset($error1)): ?>
                <p class="error-toast">Your entry for Current Password was incorrect.</p>
            <?php elseif (isset($error2)): ?>
                <p class="error-toast">New password must be different from current password.</p>
            <?php elseif (isset($error3)): ?>
                <p class="error-toast">Password must meet requirements.</p>
            <?php endif ?>

            <!-- Form for user to change their password -->
            <form id="password-change" method="post">
                <label for="password">Current Password</label>
                <input type="password" id="password" name="password" placeholder="Enter old password" required>
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new-password" placeholder="Enter new password" required>
                <label for="reenter-new-password">New Password</label>
                <input type="password" id="new-password-reenter" placeholder="Re-enter new password" required>

                <!-- Display password requirements list -->
                <style>
                    p1 {
                        font-size: small;
                        line-height: 1em
                        text-align: left
                    }
                </style>

                <!--Not currently aligning left, doesn't do it either in p1 calls. It used to, but not anymore-->
                <p1>Password must meet the following requirements:</p1>
                <p1>- Minimum length: 8 characters</p1>
                <p1>- At least one uppercase letter</p1>
                <p1>- At least one lowercase letter</p1>
                <p1>- At least one digit</p1>
                <p1>- At least 1 special character (@$!%*?&)</p1>


                <p id="password-match-error" class="error hidden">Passwords must match!</p>
                <input type="submit" id="submit" name="submit" value="Change Password">
                <a class="button cancel" href="index.php">Cancel</a>
            </form>
        </main>
    </body>
</html>