<?php
/*
 * Copyright 2013 by Allen Tucker.
 * This program is part of RMHC-Homebase, which is free software.  It comes with
 * absolutely no warranty. You can redistribute and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 *
 */

/*
 * Created on Mar 28, 2008
 * @author Oliver Radwan <oradwan@bowdoin.edu>, Sam Roberts, Allen Tucker
 * @version 3/28/2008, revised 7/1/2015
 */

// Since these are getting capitalized, may need to edit other instances of this in other files
$accessLevelsByRole = [
    'user' => 1,
    'admin' => 2,
    'super admin' => 3
];

class User {
    private $id;         // id (unique key) = email
    private $first_name; // first name as a string
    private $last_name;  // last name as a string
    private $email;
    private $role; // Executive Director, Fund Development Assistant, or Office Assistant
    private $access_level; // User, Admin, or Super Admin
    private $password;     // password for calendar and database access
    private $mustChangePassword; //seems to be used to require users to change password every X days for security purposes

    /*
     * Parameters: $email, $password, $first, $last, $role, $access, $login = boolean value that signifies if the User object is being created during a login attempt; is an optional argument
     * This function constructs a new User object with the given parameters
     * Return type: A User object
     * Pre-condition: $email, $password, $first, $last, $role, $access are all strings and $login is a boolean value if passed
     * Post-condition: a new User object is created
     */
    function __construct($email, $password, $first, $last, $role, $access, $login = null) {
        global $accessLevelsByRole;
        $this->id = $email;
        $this->email = $email;
        $this->first_name = $first;
        $this->last_name = $last;
        $this->role = $role;
        //$this->mustChangePassword = $mcp;
        //$this->access_level = $access !== "" ? explode(',', $access) : array(); // Other option for getting access level, keeping for now

        // Due to having issues with call type when constructing a User during login vs add_user, I'm using this if statement to set the access level
        // This may be modified in the future to either include more call types or rewrite the code to not need this at all and it handles the functionality on its own
        if ($login) {
            // If the call type is login, we need to set the access level to the correct value based on user data in the db
            $this->access_level = $accessLevelsByRole[$access] != "" ? explode(',', $access) : array();
        } else {
            $this->access_level = $access;
        }
        $this->password = $password;

        // This creates a default password if one isn't provided. This isn't in use, but I'm keeping it here for now
        /*if ($pass == "")
            $this->password = password_hash($this->id, PASSWORD_BCRYPT); // default password
        else
            $this->password = $pass;*/

    }

    function get_id() {
        return $this->id;
    }

    function get_first_name() {
        return $this->first_name;
    }

    function get_last_name() {
        return $this->last_name;
    }

    function get_email() {
        return $this->email;
    }

    function get_password() {
        return $this->password;
    }

    function get_role() {
        return $this->role;
    }

    function get_access_level() {
        return $this->access_level;
    }

    // Commented out for now, will reinstate later if we want to implement forced password changes
    /*function is_password_change_required() {
        return $this->mustChangePassword;
    }*/
}
