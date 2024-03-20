<?php
    // Template for new VMS pages. Base your new page on this one

    // Edited by Megan and Noor for BBBS in Spring 2024
    // Purpose: Allows users to change their password
    session_cache_expire(30);
    session_start();
    require_once('include/api.php');
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level']; // need to test this out to see if it returns string (user, admin) or int (1, 2_
        $userID = $_SESSION['_id'];
    }

    $forced = false;
    if (isset($_SESSION['change-password']) && $_SESSION['change-password']) {
        $forced = true; // User must change password due to password expiration
    } else if (!$loggedIn) {
        header('Location: login.php');
        die();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        //require_once('domain/Person.php');
        //require_once('database/dbPersons.php');

        require_once('domain/User.php');
        require_once('database/dbUsers.php');
        /*if ($forced) { // User must change password due to password expiration
            if (!wereRequiredFieldsSubmitted($_POST, array('new-password'))) {
                echo "Args missing";
                die();
            }
            $newPassword = $_POST['new-password'];
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            change_password($userID, $hash);
            if ($userID == 'vmsroot') {
                $_SESSION['access_level'] = 3;
            } else {
                $user = retrieve_person($userID); //  if this returns false, we should redirect to index.php and tell the user that the password failed to be changed for any reason
                $_SESSION['access_level'] = $user->get_access_level();
            }
            $_SESSION['logged_in'] = true;
            unset($_SESSION['change-password']);
            header('Location: index.php?pcSuccess');
            die();
        } else { // User is changing password voluntarily*/
        if (!wereRequiredFieldsSubmitted($_POST, array('password', 'new-password'))) {
            echo "Args missing";
            die();
        }
        $password = $_POST['password'];
        $newPassword = $_POST['new-password'];
        $user = retrieve_user($userID);

        if (!$user) { // user doesn't exist
            header('Location: index.php?userNotFound');
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
                header('Location: index.php?pcFail');
            }
            else { // password change succeeded
                header('Location: index.php?pcSuccess');
            }
            die();
        }
        //}
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>BBBS Donor Information System | Change Password</title>
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
                <?php if (!$forced): ?>
                    <label for="password">Current Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter old password" required>
                <?php else: ?>
                    <p>You must change your password before continuing.</p>
                <?php endif ?>
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
                <?php if (!$forced): ?>
                    <a class="button cancel" href="index.php">Cancel</a>
                <?php endif ?>
            </form>
        </main>
    </body>
</html>