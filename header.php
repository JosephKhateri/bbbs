<?php
/*
 * Copyright 2013 by Allen Tucker. 
 * This program is part of RMHP-Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */
?>

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>

<header>

    <?PHP
    //Log-in security
    //If they aren't logged in, display our log-in form.
    $showing_login = false;
    if (!isset($_SESSION['logged_in'])) {
        echo '
        <nav>
            <span id="nav-top">
                <span class="logo">
                    <img src="images/bbbs-black-bg.png">
                    <span id="vms-logo"> BBBS Fred </span>
                </span>
                <img id="menu-toggle" src="images/menu.png">
            </span>
            <ul>
                <li><a href="login.php">Log in</a></li>
            </ul>
        </nav>';
        //      <li><a href="register.php">Register</a></li>     was at line 35

    } else if ($_SESSION['logged_in']) {

        /*         * Set our permission array.
         * anything a guest can do, a volunteer and manager can also do
         * anything a volunteer can do, a manager can do.
         *
         * If a page is not specified in the permission array, anyone logged into the system
         * can view it. If someone logged into the system attempts to access a page above their
         * permission level, they will be sent back to the home page.
         */
        // Pages guests are allowed to view
        //$permission_array['index.php'] = 0;
        //$permission_array['about.php'] = 0;
        //$permission_array['apply.php'] = 0;
        $permission_array['logout.php'] = 0;
        $permission_array['register.php'] = 0;
        //pages volunteers can view
        $permission_array['dashboard.php'] = 1;
        //$permission_array['calendar.php'] = 1;
        $permission_array['eventsearch.php'] = 1;
        $permission_array['changepassword.php'] = 1;
        $permission_array['editprofile.php'] = 1;
        $permission_array['inbox.php'] = 1;
        $permission_array['viewprofile.php'] = 1;
        $permission_array['viewnotification.php'] = 1;
        $permission_array['UploadForm.php'] = 1;
        $permission_array['report.php'] = 1;
        //pages only managers can view
        $permission_array['registerUserForm.php'] = 2;


        //Check if they're at a valid page for their access level.
        $current_page = strtolower(substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1));
        //error debugging. commented out line 96 and replaced with line 97.
        //$current_page = substr($current_page, strpos($current_page,"/"));
        $current_page = strtolower(basename($_SERVER['PHP_SELF']));
        
        if (!isset($permission_array[$current_page]) > $_SESSION['access_level']) {
            //in this case, the user doesn't have permission to view this page.
            //we redirect them to the index page.
            echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
            //note: if javascript is disabled for a user's browser, it would still show the page.
            //so we die().
            die();
        } else {
            //This line gives us the path to the html pages in question, useful if the server isn't installed @ root.
            $path = strrev(substr(strrev($_SERVER['SCRIPT_NAME']), strpos(strrev($_SERVER['SCRIPT_NAME']), '/')));

            echo('<nav>');
            echo('<span id="nav-top"><span class="logo"><a class="navbar-brand" href="' . $path . 'index.php"><img src="images/bbbs-black-bg.png"></a>');
            echo('<a class="navbar-brand" id="vms-logo"> BBBS Fred </a></span><img id="menu-toggle" src="images/menu.png"></span>');
            echo('<ul>');

            echo('<li><a class="nav-link active" aria-current="page" href="' . $path . 'index.php">Home</a></li>');

            /*

            //How to make new dropdown bars and links
            echo('<li class="nav-item dropdown">');
            echo('<a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown Name</a>');
            echo('<div class="dropdown-menu">');
	        echo('<a class="dropdown-item" href="' . $path . 'changePassword.php">Change Password</a>');

            echo('</div>');
            echo('</li>');

            */
            //echo('<span class="nav-divider">|</span>');
            echo('<li class="nav-item dropdown">');
            echo('<a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Others</a>');
            echo('<div class="dropdown-menu">');
	        echo('<a class="dropdown-item" href="' . $path . 'changePassword.php">Change Password</a>');

            echo('</div>');
            echo('</li>');

	        echo('<li><a class="nav-link active" aria-current="page" href="' . $path . 'logout.php">Log out</a></li>');
            echo '</ul></nav>';
        }
    }
    ?>
</header>