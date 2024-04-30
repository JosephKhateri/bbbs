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
 * Edited by Megan and Noor for BBBS in Spring 2024
 */

class Donor {
    private $email;     // Unique key; donor's email
    private $company;  // Company the donor belongs to
    private $first_name; // First name of the donor
    private $last_name; // Last name of the donor
    private $phone; // Phone number
    private $address;     // Street address
    private $city; // City the donor lives in
    private $state; // State the donor lives in
    private $zip; // Zip code the donor lives in

    /*
     * Parameters: $email, $company, $first_name, $last_name, $phone, $address, $city, $state, $zip
     * This function constructs a new Donor object with the given parameters
     * Return type: A Donor object
     * Pre-condition: $email, $company, $first_name, $last_name, $phone, $address, $city, $state, $zip, $lifetime_donation are valid values
     * Post-condition: a new Donor object is created
     */
    function __construct($email, $company, $first_name, $last_name, $phone, $address, $city, $state, $zip) {
        $this->email = $email;
        $this->company = $company;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->phone = $phone;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
    }

    function get_email() {
        return $this->email;
    }

    function get_company() {
        return $this->company;
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

    function get_address() {
        return $this->address;
    }

    function get_city() {
        return $this->city;
    }

    function get_state() {
        return $this->state;
    }

    function get_zip() {
        return $this->zip;
    }
}
?>