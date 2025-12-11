<?php
/***************************************************************************
*   Payment FontAwesome Add-on for CS-Cart                                *
*   Version: 1.0.7                                                        *
*                                                                          *
*   Adds FontAwesome icon support to payment methods.                     *
****************************************************************************/

if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Installation function - adds database columns safely
 * Called during addon installation via addon.xml functions
 */
function fn_payment_fontawesome_install()
{
    // Check and add fa_icon_class column
    if (!fn_payment_fontawesome_column_exists('payment_descriptions', 'fa_icon_class')) {
        db_query("ALTER TABLE ?:payment_descriptions ADD fa_icon_class VARCHAR(255) DEFAULT ''");
    }
    
    // Check and add fa_icon_style column  
    if (!fn_payment_fontawesome_column_exists('payment_descriptions', 'fa_icon_style')) {
        db_query("ALTER TABLE ?:payment_descriptions ADD fa_icon_style VARCHAR(255) DEFAULT ''");
    }
    
    return true;
}

/**
 * Uninstallation function - removes database columns
 * Called during addon uninstallation via addon.xml functions
 */
function fn_payment_fontawesome_uninstall()
{
    // Remove columns if they exist
    if (fn_payment_fontawesome_column_exists('payment_descriptions', 'fa_icon_class')) {
        db_query("ALTER TABLE ?:payment_descriptions DROP COLUMN fa_icon_class");
    }
    
    if (fn_payment_fontawesome_column_exists('payment_descriptions', 'fa_icon_style')) {
        db_query("ALTER TABLE ?:payment_descriptions DROP COLUMN fa_icon_style");
    }
    
    return true;
}

/**
 * Helper function to check if a column exists in a table
 *
 * @param string $table Table name WITHOUT prefix (e.g., 'payment_descriptions')
 * @param string $column Column name
 * @return bool True if column exists
 */
function fn_payment_fontawesome_column_exists($table, $column)
{
    // Get the full table name with prefix
    $full_table = db_quote("?:$table");
    // Remove backticks that db_quote might add
    $full_table = str_replace('`', '', $full_table);
    
    $result = db_get_field(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = ?s 
         AND COLUMN_NAME = ?s",
        $full_table,
        $column
    );
    
    return !empty($result);
}

/**
 * Hook: get_payments_post
 * 
 * Called AFTER payments are fetched from database.
 * We manually fetch fa_icon_class and fa_icon_style from payment_descriptions
 * and add them to each payment in the result array.
 *
 * @param array $params   Query parameters that were used
 * @param array $payments The retrieved payments array (by reference)
 */
function fn_payment_fontawesome_get_payments_post($params, &$payments)
{
    // Skip if no payments
    if (empty($payments)) {
        return;
    }
    
    // Skip if columns don't exist (addon not properly installed)
    static $columns_exist = null;
    if ($columns_exist === null) {
        $columns_exist = fn_payment_fontawesome_column_exists('payment_descriptions', 'fa_icon_class');
    }
    
    if (!$columns_exist) {
        return;
    }
    
    // Get all payment IDs
    $payment_ids = array_keys($payments);
    
    if (empty($payment_ids)) {
        return;
    }
    
    // Determine language code
    $lang_code = !empty($params['lang_code']) ? $params['lang_code'] : CART_LANGUAGE;
    
    // Fetch FontAwesome data for all payments in one query
    $fa_data = db_get_hash_array(
        "SELECT payment_id, fa_icon_class, fa_icon_style 
         FROM ?:payment_descriptions 
         WHERE payment_id IN (?n) AND lang_code = ?s",
        'payment_id',
        $payment_ids,
        $lang_code
    );
    
    // Add FA data to each payment
    foreach ($payments as $payment_id => &$payment) {
        if (isset($fa_data[$payment_id])) {
            $payment['fa_icon_class'] = $fa_data[$payment_id]['fa_icon_class'];
            $payment['fa_icon_style'] = $fa_data[$payment_id]['fa_icon_style'];
        } else {
            $payment['fa_icon_class'] = '';
            $payment['fa_icon_style'] = '';
        }
    }
    unset($payment); // Break the reference
}

/**
 * Hook: update_payment_pre
 * 
 * Called before payment data is saved to database.
 * Sanitizes the FontAwesome fields to prevent XSS.
 *
 * @param array  $payment_data Payment data being saved
 * @param int    $payment_id   Payment ID (0 for new payments)
 * @param string $lang_code    Language code
 */
function fn_payment_fontawesome_update_payment_pre(&$payment_data, $payment_id, $lang_code)
{
    // Sanitize icon class - allow alphanumeric, spaces, hyphens, underscores
    // This covers: fab fa-cc-visa, fa-kit-duotone, fa-2xl, etc.
    if (isset($payment_data['fa_icon_class'])) {
        $payment_data['fa_icon_class'] = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $payment_data['fa_icon_class']);
        $payment_data['fa_icon_class'] = trim($payment_data['fa_icon_class']);
    }
    
    // Sanitize custom style - remove dangerous CSS but allow normal properties
    // Including CSS custom properties like --fa-primary-color: #fff;
    if (isset($payment_data['fa_icon_style'])) {
        $dangerous_patterns = array(
            '/expression\s*\(/i',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/url\s*\([^)]*data:/i',
            '/@import/i',
            '/behavior\s*:/i',
            '/binding\s*:/i',
            '/-moz-binding/i',
            '/<!--|-->/i',
            '/<\s*script/i',
        );
        $payment_data['fa_icon_style'] = preg_replace($dangerous_patterns, '', $payment_data['fa_icon_style']);
        $payment_data['fa_icon_style'] = strip_tags($payment_data['fa_icon_style']);
        $payment_data['fa_icon_style'] = trim($payment_data['fa_icon_style']);
    }
}
