<?php
/**
 * Plugin name: Optinly 
 * Plugin URI: https://www.optinly.com
 * Description: Exit-intent popups, Gamification popups, sign up forms, Lead Capturing, flyers, banners, sidebars for WordPress
 * Author: OptinlyHQ
 * Version: 1.0.20
 * Slug: optinly
 * Text Domain: optinly
 * Domain Path: /languages/
 * Requires at least: 4.6.1
 * Contributers: OptinlyHQ
 */
defined('ABSPATH') or die;
//Define plugin version
defined('OPTINLY_VERSION') or define('OPTINLY_VERSION', '1.0.19');
//Define plugin text domain
defined('OPTINLY_TD') or define('OPTINLY_TD', 'optinly');
//Define plugin text domain
defined('OPTINLY_SLUG') or define('OPTINLY_SLUG', 'optinly');
//Define plugin base path
defined('OPTINLY_BASE_PATH') or define('OPTINLY_BASE_PATH', plugin_dir_path(__FILE__));
//Define plugin base URL
defined('OPTINLY_BASE_URL') or define('OPTINLY_BASE_URL', plugin_dir_url(__FILE__));
if (version_compare(phpversion(), '5.6', '<')) {
    return false;
}
if (!file_exists(__DIR__ . "/vendor/autoload.php")) {
    return false;
} else {
    require __DIR__ . "/vendor/autoload.php";
}
$optinly = new \Optinly\App\Router();
$optinly->initHooks();
