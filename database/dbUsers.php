<?php
/* Code Review by Joseph
Program Specifications/Correctness - Excellent
Readability - Good - Might be better to put comments directly in the code instead of before each function
Code Efficiency - Excellent
Documentation - Excellent
Assigned Task - Excellent
*/

/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook, 
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan, 
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker. 
 * This program is part of RMH Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/**
 * @version March 1, 2012
 * @author Oliver Radwan and Allen Tucker
 * Edited by Megan and Noor for BBBS in Spring 2024
 */

 /**
  * Review for Noor by Conor Gill
  * Program Specificness/Correctness: Execellent, I tested adding a user and since I can login into the system I know everything is working correctly
  * Readbility: Excellent, There was plenty of documentation and there were headers detailing what I needed to know
  * Code Efficency:Excellent, Didn't see any big errors or ineffecincies in the code
  * Documentation: Excellent, Didn't see any errors and there was plenty of documentation
  * Assigned Tasked: Completed all her tasks
  */
include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/User.php');

/*
 * Parameters: $user = A User object
 * This function adds a User to the dbusers table
 * Return type: A boolean value that represents if the User was added to the dbusers table
 * Pre-condition: $user is a User object
 * Post-condition: A User is added to the dbusers table if it doesn't already exist, otherwise nothing happens
 */
function add_user($user) {
    if (!$user instanceof User)
        die("Error: add_user type mismatch");
    $con=connect();
    $query = "SELECT * FROM dbusers WHERE id = '" . $user->get_id() . "'";
    $result = mysqli_query($con,$query);
    //if there's no entry for this id, add it
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_query($con,'INSERT INTO dbusers VALUES("' .
            $user->get_id() . '","' .
            $user->get_email() . '","' .
            $user->get_password() . '","' .
            $user->get_first_name() . '","' .
            $user->get_last_name() . '","' .
            $user->get_access_level() . '","' .
            $user->get_role() . '");'
        );							
        mysqli_close($con);
        return true;
    }
    mysqli_close($con);
    return false;
}

/*
 * remove a user from dbusers table.  If already there, return false
 */

function remove_user($id) {
    $con=connect();
    $query = 'SELECT * FROM dbusers WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $query = 'DELETE FROM dbusers WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return true;
}

/*
 * Parameters: $id = A string that represents the identifying email of a User, $login = boolean value that signifies if the User object is being created during a login attempt; is an optional argument
 * This function retrieves a User from the dbusers table that matches the given id
 * Return type: A User object or a boolean value of "false"
 * Pre-condition: $id is a string and $login is a boolean value if passed
 * Post-condition: A User object is returned or the boolean "false" is returned if no user exists with the given id
 */
function retrieve_user($id, $login = null) {
    $con=connect();
    $query = "SELECT * FROM dbusers WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false; // need to handle this properly in any code that calls this function
    }
    $result_row = mysqli_fetch_assoc($result);
    $theUser = make_a_user($result_row, $login);
//    mysqli_close($con);
    return $theUser;
}

/*
 * Parameters: $id = A string that represents the identifying email of a User, $newPass = A string that represents the new password
 * This function changes the password of a User in the dbusers table that matches the given id
 * Return type: A boolean value of "true" or "false"
 * Pre-condition: $id and $newPass are strings
 * Post-condition: The password of a User is changed in the dbusers table if the User exists with the given id
 */
function change_password($id, $newPass) {
    $con=connect();
    $query = 'UPDATE dbusers SET password = "' . $newPass . '" WHERE id = "' . $id . '"';
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return $result;
}

/*
 * Parameters: None
 * This function retrieves all Users from the dbusers table except vmsroot
 * Return type: An array of User objects or a boolean value of "false" in the event that no Users exist in the dbusers table
 * Pre-condition: None
 * Post-condition: An array of User objects is returned or the boolean "false" is returned if no Users exist in the dbusers table
 */
function get_all_users() {
    $con=connect();
    $query = 'SELECT * FROM dbusers WHERE id != "vmsroot"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $result = mysqli_query($con,$query);
    $theUsers = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theUser = make_a_user($result_row);
        $theUsers[] = $theUser;
    }

    return $theUsers;
}

/*
 * Parameters: $result_row = a row from the dbusers table, $login = boolean value that signifies if the User object is being created during a login attempt; is an optional argument
 * This function constructs a new User object with the row from the dbusers table
 * Return type: User
 * Pre-condition: $result_row is an associative array and $login is a boolean value if passed
 * Post-condition: a new User object is created
 */
//Note from Conor: Needed an explanation on the purpose of login. It's not clear from the documentation here
//the purpose of Login and what it is doing by existing. A further explanation at the end in a comment or in the
//header would do good.
function make_a_user($result_row, $login = null) {
    $theUser = new User(
        /*$result_row['first_name'],
        $result_row['last_name'],
        $result_row['email'],
        $result_row['type'],
        $result_row['password'],
        $result_row['force_password_change'],*/

        $result_row['email'],
        $result_row['password'],
        $result_row['first_name'],
        $result_row['last_name'],
        $result_row['role'],
        $result_row['account_type'],
        $login
    );
    return $theUser;
}

/*
 * Parameters: None
 * This function retrieves all standard users from the dbusers table (role = "user")
 * Return type: An array of user objects or it's "false" if there's no standard users that get retrieved
 * Pre-condition: None
 * Post-condition: An array of user objects is returned or it's "false" if no standard users exist
 */

function get_all_standard_users() {
    $con = connect();
    $query = 'SELECT * FROM dbusers WHERE account_type = "user"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $theUsers = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        // Create user object and add to the array
        $theUser = make_a_user($result_row);
        $theUsers[] = $theUser;
    }
    mysqli_close($con);
    return $theUsers;
}