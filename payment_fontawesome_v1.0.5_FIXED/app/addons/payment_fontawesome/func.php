<?php
/**
 * Payment FontAwesome Add-on Functions
 * 
 * @package payment_fontawesome
 * @version 1.0.5
 */

defined('BOOTSTRAP') or die('Access denied');

/**
 * Installation function - adds database columns safely
 * Called during addon installation
 */
function fn_payment_fontawesome_install()
{
    $table = '?:payment_descriptions';
    
    // Check and add fa_icon_class column
    if (!fn_payment_fontawesome_column_exists($table, 'fa_icon_class')) {
        db_query("ALTER TABLE $table ADD fa_icon_class VARCHAR(255) DEFAULT ''");
    }
    
    // Check and add fa_icon_style column
    if (!fn_payment_fontawesome_column_exists($table, 'fa_icon_style')) {
        db_query("ALTER TABLE $table ADD fa_icon_style VARCHAR(255) DEFAULT ''");
    }
    
    return true;
}

/**
 * Uninstallation function - removes database columns
 * Called during addon uninstallation
 */
function fn_payment_fontawesome_uninstall()
{
    $table = '?:payment_descriptions';
    
    // Remove fa_icon_class column if exists
    if (fn_payment_fontawesome_column_exists($table, 'fa_icon_class')) {
        db_query("ALTER TABLE $table DROP COLUMN fa_icon_class");
    }
    
    // Remove fa_icon_style column if exists
    if (fn_payment_fontawesome_column_exists($table, 'fa_icon_style')) {
        db_query("ALTER TABLE $table DROP COLUMN fa_icon_style");
    }
    
    return true;
}

/**
 * Helper function to check if a column exists in a table
 *
 * @param string $table Table name (with ?:prefix)
 * @param string $column Column name
 * @return bool True if column exists
 */
function fn_payment_fontawesome_column_exists($table, $column)
{
    $table_name = str_replace('?:', '', $table);
    $result = db_get_field(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = ?s 
         AND COLUMN_NAME = ?s",
        fn_get_table_prefix() . $table_name,
        $column
    );
    
    return !empty($result);
}

/**
 * Helper to get table prefix
 * @return string
 */
if (!function_exists('fn_get_table_prefix')) {
    function fn_get_table_prefix()
    {
        return defined('TABLE_PREFIX') ? TABLE_PREFIX : 'cscart_';
    }
}

/**
 * Hook: get_payments
 * Modifies the SQL query to include fa_icon_class and fa_icon_style fields
 * from the payment_descriptions table.
 *
 * @param array  $params    Query parameters
 * @param string $fields    SQL fields list
 * @param string $join      SQL join clauses
 * @param string $order     SQL order clause
 * @param string $condition SQL where conditions
 * @param string $having    SQL having clause
 */
function fn_payment_fontawesome_get_payments(&$params, &$fields, &$join, &$order, &$condition, &$having)
{
    // Only add fields if columns exist (safety check)
    static $columns_exist = null;
    
    if ($columns_exist === null) {
        $columns_exist = fn_payment_fontawesome_column_exists('?:payment_descriptions', 'fa_icon_class');
    }
    
    if ($columns_exist) {
        // Add the FontAwesome icon fields to the SELECT query
        // The payment_descriptions table is already joined as 'pd' in the core query
        $fields .= ', pd.fa_icon_class, pd.fa_icon_style';
    }
}

/**
 * Hook: get_payments_post
 * Post-processes payment data to ensure fa_icon fields are properly set.
 *
 * @param array $params   Query parameters
 * @param array $payments The retrieved payments array (by reference)
 */
function fn_payment_fontawesome_get_payments_post(&$params, &$payments)
{
    if (empty($payments)) {
        return;
    }
    
    // Ensure fa_icon_class and fa_icon_style are set for each payment
    foreach ($payments as $payment_id => &$payment) {
        if (!isset($payment['fa_icon_class'])) {
            $payment['fa_icon_class'] = '';
        }
        if (!isset($payment['fa_icon_style'])) {
            $payment['fa_icon_style'] = '';
        }
    }
    unset($payment); // Break the reference
}

/**
 * Hook: update_payment_pre
 * Handles saving custom FontAwesome fields when a payment method is updated.
 *
 * @param array  $payment_data Payment data being saved
 * @param int    $payment_id   Payment ID (0 for new payments)
 * @param string $lang_code    Language code
 */
function fn_payment_fontawesome_update_payment_pre(&$payment_data, $payment_id, $lang_code)
{
    // Sanitize the icon class to prevent XSS
    if (isset($payment_data['fa_icon_class'])) {
        // Only allow alphanumeric, spaces, and hyphens (valid FA class names)
        $payment_data['fa_icon_class'] = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $payment_data['fa_icon_class']);
        $payment_data['fa_icon_class'] = trim($payment_data['fa_icon_class']);
    }
    
    // Sanitize custom style - strip dangerous CSS
    if (isset($payment_data['fa_icon_style'])) {
        // Remove potentially dangerous CSS properties and values
        $dangerous_patterns = [
            '/expression\s*\(/i',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/url\s*\(/i',
            '/@import/i',
            '/behavior\s*:/i',
            '/binding\s*:/i',
        ];
        $payment_data['fa_icon_style'] = preg_replace($dangerous_patterns, '', $payment_data['fa_icon_style']);
        $payment_data['fa_icon_style'] = trim($payment_data['fa_icon_style']);
    }
}
