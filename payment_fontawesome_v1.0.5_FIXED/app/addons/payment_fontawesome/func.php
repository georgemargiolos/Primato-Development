<?php
/***************************************************************************
*   Payment FontAwesome Add-on for CS-Cart                                *
*   Version: 1.0.8                                                        *
*                                                                          *
*   Adds FontAwesome icon support to payment methods.                     *
*   MINIMAL version - only install/uninstall functions.                   *
*   CS-Cart auto-fetches custom columns from payment_descriptions.        *
****************************************************************************/

if ( !defined('AREA') ) { die('Access denied'); }

use Tygh\Registry;

/**
 * Installation function - adds database columns safely
 * Called during addon installation via addon.xml functions
 */
function fn_payment_fontawesome_install()
{
    $table_prefix = Registry::get('config.table_prefix');
    if (empty($table_prefix)) {
        $table_prefix = 'cscart_';
    }
    
    // Check and add fa_icon_class column
    $column_exists = db_get_field(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = ?s 
         AND COLUMN_NAME = 'fa_icon_class'",
        $table_prefix . 'payment_descriptions'
    );
    
    if (empty($column_exists)) {
        db_query("ALTER TABLE ?:payment_descriptions ADD fa_icon_class VARCHAR(255) DEFAULT ''");
    }
    
    // Check and add fa_icon_style column
    $column_exists = db_get_field(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = ?s 
         AND COLUMN_NAME = 'fa_icon_style'",
        $table_prefix . 'payment_descriptions'
    );
    
    if (empty($column_exists)) {
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
    $table_prefix = Registry::get('config.table_prefix');
    if (empty($table_prefix)) {
        $table_prefix = 'cscart_';
    }
    
    // Check if columns exist before dropping
    $column_exists = db_get_field(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = ?s 
         AND COLUMN_NAME = 'fa_icon_class'",
        $table_prefix . 'payment_descriptions'
    );
    
    if (!empty($column_exists)) {
        db_query("ALTER TABLE ?:payment_descriptions DROP COLUMN fa_icon_class");
    }
    
    $column_exists = db_get_field(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME = ?s 
         AND COLUMN_NAME = 'fa_icon_style'",
        $table_prefix . 'payment_descriptions'
    );
    
    if (!empty($column_exists)) {
        db_query("ALTER TABLE ?:payment_descriptions DROP COLUMN fa_icon_style");
    }
    
    return true;
}
