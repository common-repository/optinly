<?php

namespace Optinly\App;
defined('ABSPATH') or die;

use Optinly\App\Controllers\Admin\Main;
use Optinly\App\Controllers\Site;

class Router
{
    /**
     * Init all hooks required
     */
    function initHooks()
    {
        if (is_admin() || wp_doing_ajax()) {
            $admin = new Main();
            // add_action('init',array(&$admin,'initRegisterHook'));
            add_action('admin_menu', array($admin, 'addMenu'));
            add_action('admin_enqueue_scripts', array($admin, 'adminScripts'), 100);
            // add_action('wp_ajax_optinly_validate_app_id', array($admin, 'validateAppId'));
            // add_action('wp_ajax_optinly_disconnect_app', array($admin, 'disconnectApp'));
            // add_action('wp_ajax_save_optinly_settings', array($admin, 'saveAppSettings'));
            add_action('optinly_admin_tab_content_connection', array($admin, 'connectionPage'));
            add_action('optinly_admin_tab_content_settings', array($admin, 'settingsPage'));
        } else {
            $site = new Site();
            add_action('wp_footer', array($site, 'includeScript'));
            add_shortcode('optinly-campaign', array($site, 'addShortcode'));
            add_action('rest_api_init', array($site, 'registerAPIEndpoints'));
        }
    }
}
