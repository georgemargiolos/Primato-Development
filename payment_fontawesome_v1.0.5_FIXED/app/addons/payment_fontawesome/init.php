<?php
/***************************************************************************
*   Payment FontAwesome Add-on for CS-Cart                                *
*   Version: 1.0.8                                                        *
*                                                                          *
*   Adds FontAwesome icon support to payment methods.                     *
*   NO runtime hooks needed - CS-Cart auto-fetches custom columns from    *
*   payment_descriptions table.                                           *
****************************************************************************/

if ( !defined('AREA') ) { die('Access denied'); }

// NO hooks registered - CS-Cart already fetches fa_icon_class and fa_icon_style
// automatically from payment_descriptions table
