<?php
/**
 * Plugin Name: Pi Search
 * Description: Search functionality for Wordpress
 * Version: 1.0
 * Author: Piboutique Software LLC
 * Author URI: https://piboutique.com
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PI_SEARCH_DOMAIN', 'pi-search-domain');
define('PI_SEARCH_VERSION', '1.0.0');
define('PI_PLUGIN_PATH', plugin_dir_path(__FILE__) . '');

/*
----------------------------------------
 Dependencies for the app.
----------------------------------------
*/
require_once PI_PLUGIN_PATH . 'includes/utilities.php';
require_once PI_PLUGIN_PATH . 'admin/search-back.php';
require_once PI_PLUGIN_PATH . 'public/search-front.php';



/*
----------------------------------------
 Utility Functions.
----------------------------------------
*/

