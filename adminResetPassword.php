<?php
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
    $users = get_all_users();

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
        $error1 = false;

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $error1 = true; // Form submission contained unexpected input; alert user and stay on this page
        } else {
            // Create new user with the values from args
            $id = $args['user_dropdown'];

            // Check that new password is different from current password
            $user = retrieve_user($id);
            if (password_verify($args['new_password'], $user->get_password())) {
                $error2 = true; // new and old passwords are the same
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
</head>
<body>
<?php require_once('header.php') ?>
<h1>Reset User Password</h1>
<main class="date">
    <!-- Error messages -->
    <?php if (isset($error1)): ?>
        <p class="error-toast">Your form submission contained unexpected input.</p>
    <?php elseif (isset($error2)): ?>
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
        <input type="text" id="new_password" name="new_password" required placeholder="Enter New Password">

        <br><br> <!-- Add line break before Submit button -->

        <input type="submit" value="Change User Password">
    </form>

    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
</main>
</body>
</html>