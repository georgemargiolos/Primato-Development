<?php
/***************************************************************************
*   Payment FontAwesome Add-on for CS-Cart                                *
*   Version: 1.0.7                                                        *
*                                                                          *
*   Adds FontAwesome icon support to payment methods.                     *
****************************************************************************/

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
    'get_payments_post',  // Fetch FA data AFTER payments are retrieved
    'update_payment_pre'  // Sanitize FA fields before saving
);
