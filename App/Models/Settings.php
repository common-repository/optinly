<?php

namespace Optinly\App\Models;
defined('ABSPATH') or die;

class Settings
{
    public $settings_db_key,$secret_key;

    function __construct()
    {
        $this->settings_db_key = OPTINLY_SLUG . "_settings";
        $this->secret_key = OPTINLY_SLUG . "_secret_key";
    }

    /**
     * Saving settings to the DB
     * @param $settings
     * @return bool
     */
    function saveSettings($settings)
    {
        return update_option($this->settings_db_key, $settings, true);
    }

    /**
     * Saving key to the DB
     * @param $key
     * @return bool
     */
    function saveSecretKey($key)
    {
        return update_option($this->secret_key, $key, true);
    }

    /**
     * Saving key to the DB
     * @return bool
     */
    function getSecretKey()
    {
        return get_option($this->secret_key, false);
    }

    /**
     * get is app id is connected or not
     * @return mixed|void
     */
    function getSettings()
    {
        return get_option($this->settings_db_key, array());
    }

    /**
     * get value from array
     * @param $settings
     * @param $key
     * @param $default
     * @return mixed
     */
    function getOption($settings, $key, $default = null)
    {
        if (isset($settings[$key])) {
            return $settings[$key];
        }
        return $default;
    }
}