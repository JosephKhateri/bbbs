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

$accessLevelsByRole = [
    'User' => 1,
    'Admin' => 2,
    'Superadmin' => 3
];

class User {
    private $id;         // id (unique key) = email
    private $first_name; // first name as a string
    private $last_name;  // last name as a string
    private $phone;   // primary phone -- home, cell, or work
    private $email;
    private $role; // Executive Director, Fund Development Assistant, or Office Assistant
    private $access_level;
    private $password;     // password for calendar and database access: default = $id
    private $mustChangePassword;

    function __construct($f, $l, $p, $e, $r, $a, $pass, $mcp) {
        global $accessLevelsByRole;
        $this->id = $e;
        $this->first_name = $f;
        $this->last_name = $l;
        $this->phone = $p;
        $this->email = $e;
        $this->role = $r;
        $this->mustChangePassword = $mcp;
        //$this->access_level = 2;
        //if ($t !== "") {
        //$this->type = explode(',', $t);
        //global $accessLevelsByRole;
        $this->access_level = $accessLevelsByRole[$a];
        //} else {
        //$this->type = array();
        //$this->access_level = 0;
        //}
        if ($pass == "")
            $this->password = password_hash($this->id, PASSWORD_BCRYPT); // default password
        else
            $this->password = $pass;
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

    function get_phone() {
        return $this->phone;
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

    function is_password_change_required() {
        return $this->mustChangePassword;
    }
}
