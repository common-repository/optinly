<?php

namespace Optinly\App\Controllers\Admin;
defined('ABSPATH') or die;

use Optinly\App\Api\optinlyApi;
use Optinly\App\Controllers\Base;
use Optinly\App\Models\Connection as ConnectionModel;
use Optinly\App\Models\Settings as SettingsModel;

class Main extends Base
{
    /**
     * Add menu for the plugin
     */
    function addMenu()
    {
        add_menu_page(__('Optinly Connection wizard', OPTINLY_TD), __('Optinly', OPTINLY_TD), 'manage_options', OPTINLY_SLUG, array($this, 'manageMenus'), 'dashicons-buddicons-pm');
    }

    /**
     * Adding stylesheets and scripts required by admin
     * @param $hook
     */
    function adminScripts($hook)
    {
        if ($hook != 'toplevel_page_' . OPTINLY_SLUG) {
            return;
        }
        //Enqueue css
        wp_enqueue_style(OPTINLY_SLUG . '-admin', OPTINLY_BASE_URL . 'App/Assets/Css/admin.css', array(), OPTINLY_VERSION);
        if (!wp_script_is(OPTINLY_SLUG . 'track-user-cart', 'enqueued')) {
            //Enqueue js
            wp_enqueue_script(OPTINLY_SLUG . '-admin', OPTINLY_BASE_URL . 'App/Assets/Js/admin.js', array('jquery'), OPTINLY_VERSION);
            $optinly_data = array(
                'slug' => OPTINLY_SLUG,
                'ajax_url' => admin_url('admin-.php'),
                'reconnect_btn_txt' => __('Re-Connect'),
                'connect_btn_txt' => __('Connect'),
                'nonce' => wp_create_nonce('optinly-csrf')
            );
            $optinly_data = apply_filters('optinly_localize_admin_data', $optinly_data);
            wp_localize_script(OPTINLY_SLUG . '-admin', 'optinly_admin_data', $optinly_data);
        }
    }

    /**
     * Manage all pages
     */
    function manageMenus()
    {
        if (isset($_GET["page"]) && sanitize_text_field($_GET["page"]) == OPTINLY_SLUG) {
            $active_tab = isset($_GET["tab"]) ? sanitize_text_field($_GET["tab"]) : 'connection';
            $base_url = rtrim(admin_url(), '/') . '/admin.php?page=' . OPTINLY_SLUG;
            $tabs = array(
                array('src' => add_query_arg(array('tab' => 'connection'), $base_url), 'title' => 'Connection', 'id' => 'connection')
            );
            if ($this->isPluginActive('mailpoet/mailpoet.php')) {
                $tabs[] = array('src' => add_query_arg(array('tab' => 'settings'), $base_url), 'title' => 'Settings', 'id' => 'settings');
            }
            $extra = array();
            $path = rtrim(OPTINLY_BASE_PATH, '/') . '/App/Views/Admin/base.php';
            require_once $path;
        }
    }

    /**
     * Connection tab content
     */
    function connectionPage()
    {
        $app = new optinlyApi();
        $path = rtrim(OPTINLY_BASE_PATH, '/') . '/App/Views/Admin/connection.php';
        $connection_model = new ConnectionModel();
        $app_id = $connection_model->getAppId();
        $is_app_connected = $connection_model->isAppConnected();
        $app_dashboard_url = $app->app_url;
        require_once $path;
    }

    /**
     * generate random string
     * @param int $length
     * @return string
     */
    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString . uniqid();
    }

    /**
     * Connection tab content
     */
    function settingsPage()
    {
        $is_mailpoet_plugin_active = $this->isPluginActive('mailpoet/mailpoet.php');
        $settings_model = new SettingsModel();
        $settings = $settings_model->getSettings();
        $is_mailpoet_enabled = $settings_model->getOption($settings, 'is_mailpoet_enabled', 'no');
        $app_secret_key = $settings_model->getSecretKey();
        if (empty($app_secret_key)) {
            $app_secret_key = $this->generateRandomString(20);
            $settings_model->saveSecretKey($app_secret_key);
        }
        $mailpoet_list_id = $settings_model->getOption($settings, 'mailpoet_list_id', array());
        $mailpoet_webhook = rtrim(site_url(), '/') . '/wp-json/optinly/v1/subscribe/mailpoet';
        $mailpoet_lists = array();
        if (class_exists(\MailPoet\API\API::class)) {
            $mailpoet_api = \MailPoet\API\API::MP('v1');
            $mailpoet_lists = $mailpoet_api->getLists();
        }
        $path = rtrim(OPTINLY_BASE_PATH, '/') . '/App/Views/Admin/settings.php';
        require_once $path;
    }

    /**
     * Validate the APP ID entered by the user
     */
    function validateAppId(\WP_REST_Request $response_data)
    {
        $headerid = $response_data->get_headers();
        $app_id= $headerid["app_id"][0];
        $resStatus = false;
        $resData="";

        $connection_model = new ConnectionModel();
        $connection_model->saveAppStatus(0);


        if(!isset($headerid["x_wb_nonce"])) return new \WP_REST_Response("unauthorization",401);

        if( isset($_POST['x_wb_nonce'])  && !wp_verify_nonce( $headerid["x_wb_nonce"][0], 'optinly-csrf' ) ) return new \WP_REST_Response("unauthorization",401);

        // echo"<script>conssole.log(1)</script>";
        
        if (isset($headerid["app_id"])) {


            $connection_model->saveAppId($app_id);

            if (empty($app_id)) {

                wp_send_json_error(__("Please enter App-Id!", OPTINLY_TD));
                $resData = "Please enter App-Id!";

            } else {

                $api = new optinlyApi();

                try {

                    $site_url = $this->removeHttp(site_url());

                    $site_url = apply_filters('optinly_get_site_url_for_verification', $site_url);

                    $api_response = $api->validateAppId($app_id, $site_url);

                    $api_response = apply_filters('optinly_app_id_validation_success', $api_response);

                    $connection_model->saveAppStatus(1);

                    $resData = "optinly_app_id_validation_success";
                    $resStatus = true;
                    wp_send_json_success($api_response);

                } catch (\Exception $e) {
                    do_action('optinly_app_id_validation_failed');

                    $resData = $e->getMessage();

                }
            }
        } else {
            $rescode = "Invalid request found";

        }

        $response = array('success' => $resStatus, 'data'=> $resData);

        $status = 200;
        
        return new \WP_REST_Response($response,$status);
    }

    /**
     * remove http from url
     * @param $url
     * @return mixed
     */
    function removeHttp($url)
    {
        $disallowed = array('http://', 'https://');
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;
    }

    /**
     * Validate the APP ID entered by the user
     */
    function disconnectApp(\WP_REST_Request $response_data)
    {
        $resStatus = false;
        $resData="";

        $headerid = $response_data->get_headers();

        if(!isset($headerid["x_wb_nonce"])) return new \WP_REST_Response('unauthorization',401);

        if(isset($_POST['x_wb_nonce'])  && !wp_verify_nonce(  $headerid["x_wb_nonce"][0],'optinly-csrf') ) return new \WP_REST_Response('unauthorization',401);

        $connection_model = new ConnectionModel();
        $connection_model->saveAppStatus(0);
        $resData ='App disconnected successfully...!';
        $resStatus = true;
        $response = array('success' => $resStatus, 'data'=> $resData);

        $status = 200;
        
        return new \WP_REST_Response($response,$status);


    }

    /**
     * Save the app settings
     */
    function saveAppSettings(\WP_REST_Request $response_data)
    {   
        $headerid = $response_data->get_headers();
        $app_id= $headerid["app_secret_key"][0];
        $resStatus = false;
        $status = 400;
        if(!isset($headerid["x_wb_nonce"])) return new \WP_REST_Response('unauthorization',401);

        if(isset($_POST['x_wb_nonce'])  && !wp_verify_nonce( $headerid["x_wb_nonce"][0],'optinly-csrf') ) return new \WP_REST_Response('unauthorization',401);


        $resData="";
        if(isset($headerid["app_secret_key"])){
            $connection_model = new SettingsModel();
            $connection_model->saveSettings($app_id);
            wp_send_json_success(__('Settings saved successfully', OPTINLY_TD));
            $resStatus=true;
            $resData="Settings saved successfully";
            $status = 200;
        }
        else {
            wp_send_json_error(__('Nonce not found', OPTINLY_TD));
            $resData="app_secret_key";
            $status = 400;
        }
        $response = array('success' => $resStatus, 'data'=> $resData);

        
        return new \WP_REST_Response($response,$status);

    }
}