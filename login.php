<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();
    
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    require_once('domain/User.php');
    require_once('database/dbUsers.php');
    require_once('include/api.php');
    require_once('include/input-validation.php');

    // redirect to index if already logged in
    if (isset($_SESSION['_id'])) {
        redirect('index.php');
        die();
    }

    $badLogin = false;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ignoreList = array('password');
        $args = sanitize($_POST, $ignoreList);
        $required = array('username', 'password');
        if (wereRequiredFieldsSubmitted($args, $required)) {
            $username = strtolower($args['username']);
            $password = $args['password'];
            $login = true; // value to signify a login attempt that the User constructor will evaluate
            $user = retrieve_user($username, $login);
            if (!$user) {
                // User doesn't exist
                $badLogin = true;
            } else if (password_verify($password, $user->get_password())) {
                // User exists and password is correct
                $changePassword = false;
                $_SESSION['logged_in'] = true;
                $types = $user->get_access_level();
                if (in_array('super admin', $types)) {
                    $_SESSION['access_level'] = 3;
                } else if (in_array('admin', $types)) {
                    $_SESSION['access_level'] = 2;
                } else {
                    $_SESSION['access_level'] = 1;
                }
                $_SESSION['f_name'] = $user->get_first_name();
                $_SESSION['l_name'] = $user->get_last_name();
                $_SESSION['_id'] = $user->get_id();
                // hard code root privileges
                if ($user->get_id() == 'vmsroot') {
                    $_SESSION['access_level'] = 3;
                }
                if ($changePassword) {
                    $_SESSION['access_level'] = 0;
                    $_SESSION['change-password'] = true;
                    redirect('changePassword.php');
                    die();
                } else {
                    redirect('index.php');
                    die();
                }
            } else {
                // The user's password was incorrect
                $badLogin = true;
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>BBBS Donor Information System | Log In</title>
        <style>

            select, option, input {
            color: white; /* Setting the font color to white */
            background-color: #333; /* A darker background for contrast */
        }
        </style>
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
