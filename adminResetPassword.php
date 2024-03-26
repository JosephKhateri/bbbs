<?php
/* Code Review by Joseph
Program Specifications/Correctness - Excellent
Readability - Excellent
Code Efficiency - Excellent
Documentation - Excellent
Assigned Task - Excellent
*/

    // Edited by Megan and Noor for BBBS in Spring 2024
    // Purpose: Allows admins to reset a user's password

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
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
    // Require admin privileges
    if ($accessLevel < 2) {
        header('Location: login.php');
        die();
    }

    // Get all users from dbUsers table except for vmsroot
    // This list of users will be used to populate the dropdown menu in the HTML form
    require_once('database/dbUsers.php');
    require_once('domain/User.php');
    if ($userID == 'vmsroot') {
        // vmsroot is allowed to change the password of any user except themselves
        $users = get_all_users();
    } else {
        // Regular admins are only allowed to change the password of standard users
        $users = get_all_standard_users();
    }
    //$users = get_all_standard_users(); //just get user objects for admin to be able to change their passwords

    // if users is equal to false (meaning no users were retrieved from the database), redirect to the dashboard
    if (!$users) {
        header('Location: index.php?noUsers');
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        require_once('database/dbUsers.php');
        require_once('domain/User.php');
        $args = sanitize($_POST, null);
        $required = array(
            "user_dropdown", "new_password" // Required fields for the form
        );

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $error1 = true; // Form submission contained unexpected input; alert user and stay on this page
        } else if (!validatePassword($args['new_password'])) {
            $error2 = true; // New password doesn't meet requirements; alert user and stay on this page
        } else {
            // Set user ID to the value of the user_dropdown dropdown menu and retrieve the user from dbUsers
            $id = $args['user_dropdown'];
            $user = retrieve_user($id);

            // Check that new password is different from current password
            if (password_verify($args['new_password'], $user->get_password())) {
                $error3 = true; // new and old passwords are the same
            } else {
                // Change the user's password and redirect admin to the dashboard
                $newPassword = password_hash($args['new_password'], PASSWORD_BCRYPT);
                $result = change_password($id, $newPassword);
                if (!$result) {
                    // If the password change fails, alert the admin and redirect to the dashboard
                    header('Location: index.php?pcFail');
                } else {
                    // Password change was successful, alert the admin and redirect to the dashboard
                    echo '<script>document.location = "index.php?pcSuccess";</script>';
                }
            }
        }
    }
    $date = null;

?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>BBBS | Reset User Password</title>
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
<h1>Reset User Password</h1>
<main class="date">
    <!-- Error messages -->
    <?php if (isset($error1)): ?>
        <p class="error-toast">Your form submission contained unexpected input.</p>
    <?php elseif (isset($error2)): ?>
        <p class="error-toast">Password must meet requirements.</p>
    <?php elseif (isset($error3)): ?>
        <p class="error-toast">New password must be different from current password.</p>
    <?php endif ?>

    <!-- Form for admin to reset a user's password -->
    <h2>Reset Password</h2>
    <form id="new-animal-form" method="post">
        <label for="user_dropdown">Select a User:</label>
        <select name="user_dropdown" id="user_dropdown">
            <?php // Populate dropdown menu with all users except for vmsroot
            foreach ($users as $user) {
                echo "<option value='" . $user->get_id() . "'>" . $user->get_id() . " " . "</option>";
            }
            ?>
        </select>
        <br><br> <!-- Add line break before New Password input -->

        <label for="new_password">New Password *</label>
        <input type="password" id="new_password" name="new_password" required placeholder="Enter New Password">

        <!-- Display password requirements list -->
        <style>
            p1 {
                font-size: small;
                line-height: 1em
            }
        </style>
        <p1 style="text-align: left">Password must meet the following requirements:</p1><br>
        <p1 style="text-align: left">- Minimum length: 8 characters</p1><br>
        <p1 style="text-align: left">- At least one uppercase letter</p1><br>
        <p1 style="text-align: left">- At least one lowercase letter</p1><br>
        <p1 style="text-align: left">- At least one digit</p1><br>
        <p1 style="text-align: left">- At least 1 special character (@$!%*?&)</p1>


        <br><br> <!-- Add line break before Submit button -->

        <input type="submit" value="Change User Password">
    </form>

    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
</main>
</body>
</html>