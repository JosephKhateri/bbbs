<?php
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
 */
include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/User.php');

/*
 * add a user to dbUsers table: if already there, return false
 */

function add_user($user) {
    if (!$user instanceof User)
        die("Error: add_user type mismatch");
    $con=connect();
    $query = "SELECT * FROM dbUsers WHERE id = '" . $user->get_id() . "'";
    $result = mysqli_query($con,$query);
    //if there's no entry for this id, add it
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_query($con,'INSERT INTO dbUsers VALUES("' .
            $user->get_id() . '","' .
            $user->get_first_name() . '","' .
            $user->get_last_name() . '","' .
            $user->get_email() . '","' .
            $user->get_password() . '","' .
            $user->get_role() . '","' .
            $user->get_access_level() . '","' .
            //$user->is_password_change_required() . '","' .
            '");'
        );							
        mysqli_close($con);
        return true;
    }
    mysqli_close($con);
    return false;
}

/*
 * remove a user from dbUsers table.  If already there, return false
 */

function remove_user($id) {
    $con=connect();
    $query = 'SELECT * FROM dbUsers WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $query = 'DELETE FROM dbUsers WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return true;
}

/*
 * @return a User from dbUsers table matching a particular id.
 * if not in table, return false
 */

function retrieve_user($id) {
    $con=connect();
    $query = "SELECT * FROM dbUsers WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    //file_put_contents('output.txt', $result_row['access_level']);
    // var_dump($result_row);
    $theUser = make_a_user($result_row);
//    mysqli_close($con);
    return $theUser;
}
// Name is first concat with last name. Example 'James Jones'
// return array of Users.
function retrieve_users_by_name ($name) {
	$users = array();
	if (!isset($name) || $name == "" || $name == null) return $users;
	$con=connect();
	$name = explode(" ", $name);
	$first_name = $name[0];
	$last_name = $name[1];
    $query = "SELECT * FROM dbUsers WHERE first_name = '" . $first_name . "' AND last_name = '". $last_name ."'";
    $result = mysqli_query($con,$query);
    while ($result_row = mysqli_fetch_assoc($result)) {
        $the_user = make_a_user($result_row);
        $users[] = $the_user;
    }
    return $users;	
}

function change_password($id, $newPass) {
    $con=connect();
    $query = 'UPDATE dbUsers SET password = "' . $newPass . '", force_password_change="0" WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return $result;
}

function reset_password($id, $newPass) {
    $con=connect();
    $query = 'UPDATE dbUsers SET password = "' . $newPass . '", force_password_change="1" WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return $result;
}

/*
 * @return all rows from dbUsers table ordered by last name
 * if none there, return false
 */

function getall_dbUsers($name_from, $name_to, $venue) {
    $con=connect();
    $query = "SELECT * FROM dbUsers";
    $query.= " WHERE venue = '" .$venue. "'"; 
    $query.= " AND last_name BETWEEN '" .$name_from. "' AND '" .$name_to. "'"; 
    $query.= " ORDER BY last_name,first_name";
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
  @return all rows from dbUsers

*/
function getall_users() {
    $con=connect();
    $query = 'SELECT * FROM dbUsers WHERE id != "vmsroot"';
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


function getall_user_names() {
	$con=connect();
    $type = "volunteer";
	$query = "SELECT first_name, last_name FROM dbUsers WHERE type LIKE '%" . $type . "%' ";
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $result = mysqli_query($con,$query);
    $names = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $names[] = $result_row['first_name'].' '.$result_row['last_name'];
    }
    mysqli_close($con);
    return $names;   	
}

function make_a_user($result_row) {
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
        $result_row['access_level'], //access level isn't working here, but role does. I need to get access level working so that other users dont get an error message
    );
    return $theUser;
}