<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();
    
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    // redirect to index if already logged in
    if (isset($_SESSION['_id'])) {
        header('Location: index.php');
        die();
    }
    $badLogin = false;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        $ignoreList = array('password');
        $args = sanitize($_POST, $ignoreList);
        $required = array('username', 'password');
        if (wereRequiredFieldsSubmitted($args, $required)) {
            require_once('domain/User.php');
            require_once('database/dbUsers.php');
            require_once('database/dbMessages.php');
            dateChecker();
            $username = strtolower($args['username']);
            $password = $args['password'];
            $user = retrieve_user($username);
            if (!$user) {
                // User doesn't exist
                $badLogin = true;
            } else if (password_verify($password, $user->get_password())) {
                // User exists and password is correct
                $changePassword = false;
                // Commented out for now, will reinstate later if we want to implement forced password changes
                /*if ($user->is_password_change_required()) {
                    $changePassword = true;
                    $_SESSION['logged_in'] = false;
                } else {
                    $_SESSION['logged_in'] = true;
                }*/
                $_SESSION['logged_in'] = true;
                $types = $user->get_access_level();
                if (in_array('superadmin', $types)) {
                    $_SESSION['access_level'] = 3;
                } else if (in_array('admin', $types)) {
                    $_SESSION['access_level'] = 2;
                } else {
                    $_SESSION['access_level'] = 1;
                }
                $_SESSION['f_name'] = $user->get_first_name();
                $_SESSION['l_name'] = $user->get_last_name();
                //$_SESSION['venue'] = $user->get_venue(); // Not sure what this is for, keeping this here
                //$_SESSION['type'] = $user->get_type(); // Not sure what this is for, kleeping this here
                $_SESSION['_id'] = $user->get_id();
                // hard code root privileges
                if ($user->get_id() == 'vmsroot') {
                    $_SESSION['access_level'] = 3;
                }
                if ($changePassword) {
                    $_SESSION['access_level'] = 0;
                    $_SESSION['change-password'] = true;
                    header('Location: changePassword.php');
                    die();
                } else {
                    header('Location: index.php');
                    die();
                }
                die();
            } else {
                // The user's password was incorrect
                $badLogin = true;
            }
        }
    }
    //<p>Or <a href="register.php">register as a new volunteer</a>!</p>
    //Had this line under login button, took user to register page
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>BBBS Donor Information System | Log In</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <main class="login">
            <h1>BBBS Donor Information System Login</h1>
            <?php if (isset($_GET['registerSuccess'])): ?>
                <div class="happy-toast">
                    Your registration was successful! Please log in below.
                </div>
            <?php else: ?>
            <p>Welcome! Please log in below.</p>
            <?php endif ?>
            <form method="post">
                <?php
                    if ($badLogin) {
                        echo '<span class="error">No login with that e-mail and password combination currently exists.</span>';
                    }
                ?>
                <label for="username">Username</label>
        		<input type="text" name="username" placeholder="Enter your e-mail address" required>
        		<label for="password">Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                <input type="submit" name="login" value="Log in">
            </form>
        </main>
    </body>
</html>
