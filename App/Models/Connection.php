<?php

namespace Optinly\App\Models;
defined('ABSPATH') or die;

class Connection
{
    public $app_id_db_key, $is_app_connected_db_key;

    function __construct()
    {
        $this->app_id_db_key = OPTINLY_SLUG . "_app_id";
        $this->is_app_connected_db_key = OPTINLY_SLUG . "_is_app_connected";
    }

    /**
     * Saving App id to the DB
     * @param $app_id
     * @return bool
     */
    function saveAppId($app_id)
    {
        return update_option($this->app_id_db_key, $app_id, true);
    }

    /**
     * Set app was disconnected
     * @param $status
     * @return bool
     */
    function saveAppStatus($status)
    {
        return update_option($this->is_app_connected_db_key, $status, true);
    }

    /**
     * get is app id is connected or not
     * @return mixed|void
     */
    function isAppConnected()
    {
        return get_option($this->is_app_connected_db_key, 0);
    }

    /**
     * get is app id is connected or not
     * @return mixed|void
     */
    function getAppId()
    {
        return get_option($this->app_id_db_key, NULL);
    }
}