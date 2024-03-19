<?php
    // Edited by Megan and Noor for BBBS in Spring 2024
    // Purpose: Allows admins to register a new user

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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        require_once('database/dbUsers.php');
        require_once('domain/User.php');
        $args = sanitize($_POST, null);
        $required = array(
			"email", "password", "first_name", "last_name", "account_type", "role" // Required fields for the form
		);
        $errors = false;

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $errors = true;
            echo 'bad form data';
            die();
        } else {
            // Create new user with the values from args
            $email = strtolower($args['email']);
            $email = validateEmail($email);
            if(!$email){
                echo "Invalid Email";
                die();
            }
            $id = $email; // ID and email have the same value
            $passwordError = false;
            if (!validatePassword($args['password'])) {
                $passwordError = true; // Password doesn't meet requirements
            } else {
                $password = password_hash($args['password'], PASSWORD_BCRYPT);
                $first_name = $args['first_name'];
                $last_name = $args['last_name'];
                $role = $args['role'];
                $account_type = $args['account_type'];

                // If there are any errors, stop the script and alert the user
                if ($errors) {
                    echo '<p>Your form submission contained unexpected input.</p>';
                    die();
                }

                // Create a new User object and add it to the database
                $newUser = new User($email, $password, $first_name, $last_name, $role, $account_type);
                $result = add_user($newUser);
                if (!$result) { // If a user with the same email already exists
                    $userExistsError = true;
                } else {
                    echo '<script>document.location = "index.php?registerSuccess";</script>';
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
        <title>BBBS | Register User</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Register User</h1>
        <main class="date">
            <!-- Error messages -->
            <?php if (isset($userExistsError)): ?>
                <p class="error-toast">That email is already in use.</p>
            <?php elseif (isset($error)): ?>
                <p class="error-toast">Your form submission contained unexpected input.</p>
            <?php elseif (isset($passwordError)): ?>
                <p class="error-toast">Password must meet requirements.</p>
            <?php endif ?>

            <!-- Form for registering a new user -->
            <h2>User Registration</h2>
            <form id="new-animal-form" method="post">
                <label for="name">Email *</label>
                <input type="email" id="email" name="email" required placeholder="Enter Email">
                <label for="name">Password *</label>
                <input type="password" id="password" name="password" required placeholder="Enter Password">
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
                <p1 style="text-align: left">- At least 1 special character (@$!%*?&)</p1><br><br>

                <label for="name">First Name *</label>
                <input type="text" id="first_name" name="first_name" required placeholder="Enter First Name">
                <label for="name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required placeholder="Enter Last Name">

                <label for="name">Account Type *</label>
                <select id="text" name="account_type">
                    <option value=""></option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>

                <label for="name">Role *</label>
                <input type="text" id="role" name="role" required placeholder="Enter the User's Organizational Role">
                
                <input type="submit" value="Create New User">
            </form>

            <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
        </main>
    </body>
</html>