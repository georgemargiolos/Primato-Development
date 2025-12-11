<?php
/**
 * Payment FontAwesome Add-on Initialization
 * 
 * @package payment_fontawesome
 * @version 1.0.5
 * 
 * This file registers all PHP hooks used by the addon.
 * The hook functions are defined in func.php.
 */

defined('BOOTSTRAP') or die('Access denied');

// Register PHP hooks for extending payment data retrieval and saving
fn_register_hooks(
    // Hook to modify the SQL query when fetching payments
    // This adds fa_icon_class and fa_icon_style to the SELECT fields
    'get_payments',
    
    // Hook to post-process payment data after retrieval
    // Ensures fa_icon fields have default values
    'get_payments_post',
    
    // Hook called before payment is updated/saved
    // Used to sanitize the custom icon fields
    'update_payment_pre'
);
