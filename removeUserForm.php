<?php
    // Edited by Megan and Noor for BBBS in Spring 2024
    // Purpose: Allows admins to remove a user

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    require_once('database/dbUsers.php');
    require_once('domain/User.php');
    require_once('include/api.php');
    require_once('include/input-validation.php');

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
    if ($accessLevel == 1) {
        redirect('index.php');
        die();
    } elseif ($accessLevel < 1) { // If not logged in, redirect to login page
        redirect('login.php');
        die();
    }

    // get all users from dbUsers table except for vmsroot if the current user is not vmsroot
    if ($userID == 'vmsroot') {
        // vmsroot is allowed to remove any user including admins
        $users = get_all_users();
    } else {
        // regular admins are only allowed to remove standard users
        $users = get_all_standard_users();
    }

    // if users is equal to false (clarification: no users were retrieved from the database), redirect to the dashboard
    if (!$users) {
        redirect('index.php?noUsers');
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // sanitize all input
        $args = sanitize($_POST, null);
        $required = array(
            "user_dropdown" // drop down field
        );

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $error1 = true; // form submission contains unexpected input
        } else {
            // set user ID to the value of the user_dropdown dropdown menu
            $id = $args['user_dropdown'];

            // check if the current user has permission to remove this user
            if ($userID == 'vmsroot' || $accessLevel == 2) {
                $result = remove_user($id);
                if (!$result) {
                    // if user removal fails redirect to the dashboard
                    redirect('index.php?removeFail');
                } else {
                    // user removal was successful redirect to the dashboard
                    echo '<script>document.location = "index.php?removeSuccess";</script>';
                }
            } else {
                // unauthorized access attempt
                redirect('index.php?unauthorized');
                die();
            }
        }
    }
    $date = null;

?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>BBBS | Remove User</title>
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

    </style>

</head>
<body>
<?php require_once('header.php') ?>
<h1>Remove User</h1>
<main class="date">
    <!-- error messages -->
    <?php if (isset($error1)): ?>
        <p class="error-toast">Your form submission contained unexpected input.</p>
    <?php endif ?>

    <!-- form for admin to remove a user -->
    <h2>Select User to Remove</h2>
    <form id="remove-user-form" method="post">
        <label for="user_dropdown">Select a User:</label>
        <select name="user_dropdown" id="user_dropdown">
            <?php //create dropdown menu with all users except for vmsroot
            foreach ($users as $user) {
                echo "<option value='" . $user->get_id() . "'>" . $user->get_id() . " " . "</option>";
            }
            ?>
        </select>
        <br><br> <!-- add line break before remove button -->

        <input type="submit" value="Remove User" onclick="return confirm('Are you sure you want to remove this user?')">
    </form>

    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
</main>
</body>
</html>
