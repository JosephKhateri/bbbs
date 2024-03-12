<?php
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
        echo 'bad access level';
        die();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        require_once('database/dbUsers.php');
        $args = sanitize($_POST, null);
        $required = array( // I have a feeling that needing an ID to create a user is causing problems here.. May need to have code automatically create ID variable and set its value to that of email
			"id", "email", "password", "first_name", "last_name", "account_type", "role"
		);
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            $id = add_user($args);
            if(!$id){
                echo "Oopsy!";
                die();
            }
            require_once('include/output.php');
            
            $name = htmlspecialchars_decode($args['name']);
            require_once('database/dbMessages.php');
            header("Location: registerUser.php?id=$id&createSuccess");
            die();
        }
    }
    $date = null;

?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Register User</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Register User</h1>
        <main class="date">
            <h2>User Registration</h2>
            <form id="new-animal-form" method="post">
                <!--<label for="name">User ID *</label>
                <input type="text" id="id" name="id" required placeholder="Enter User's ID">-->
                <label for="name">Email *</label>
                <input type="email" id="email" name="email" required placeholder="Enter Email"> 
                <label for="name">Password *</label>
                <input type="password" id="password" name="password" required placeholder="Enter Password">
                <label for="name">First Name *</label>
                <input type="text" id="first_name" name="first_name" required placeholder="Enter First Name">
                <label for="name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required placeholder="Enter Last Name">


                <label for="name">Account Type *</label>
                <select id="text" name="account_type" required>
                    <option value=""></option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>


                <label for="name">Role *</label>
                <select id="text" name="role" required>
                    <option value=""></option>
                    <option value="Executive Director">Executive Director</option>
                    <option value="Fund Development Assistant">Fund Development Assistant</option>
                    <option value="Office Assistant">Office Assistant</option>
                </select>
                
                <input type="submit" value="Create New User">
            </form>
                <?php if ($date): ?>
                    <a class="button cancel" href="calendar.php?month=<?php echo substr($date, 0, 7) ?>" style="margin-top: -.5rem">Return to Calendar</a>
                <?php else: ?>
                    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
                <?php endif ?>
        </main>
    </body>
</html>