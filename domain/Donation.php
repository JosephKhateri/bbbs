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

class Donation {
    private $id;         // id (unique key) = donation number
    private $email;     // Email of donor who made the donation
    private $contribution_date;  // Date of donation
    private $contribution_type; // Individual, Business, or Anonymous
    private $contribution_category; // Annual Giving, Event Sponsorship, Gift, or Other
    private $amount; // Donation amount
    private $payment_method;     // Online, Bank Transfer, Credit Card, or Cash
    private $memo; // Additional notes from donor

    /*
     * Parameters: $id, $email, $contribution_date, $contribution_type, $contribution_category, $amount, $payment_method, $memo
     * This function constructs a new Donation object with the given parameters
     * Return type: A Donation object
     * Pre-condition: $id, $email, $contribution_date, $contribution_type, $contribution_category, $amount, $payment_method, $memo are all valid
     * Post-condition: a new Donation object is created
     */
    function __construct($id, $email, $contribution_date, $contribution_type, $contribution_category, $amount, $payment_method, $memo) {
        $this->id = $id;
        $this->email = $email;
        $this->contribution_date = $contribution_date;
        $this->contribution_type = $contribution_type;
        $this->contribution_category = $contribution_category;
        $this->amount = $amount;
        $this->payment_method = $payment_method;
        $this->memo = $memo;
    }

    function get_id() {
        return $this->id;
    }

    function get_email() {
        return $this->email;
    }

    function get_contribution_date() {
        return $this->contribution_date;
    }

    function get_contribution_type() {
        return $this->contribution_type;
    }

    function get_contribution_category() {
        return $this->contribution_category;
    }

    function get_amount() {
        return $this->amount;
    }

    function get_payment_method() {
        return $this->payment_method;
    }

    function get_memo() {
        return $this->memo;
    }
}
?>
