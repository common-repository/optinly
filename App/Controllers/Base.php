<?php

namespace Optinly\App\Controllers;
class Base
{
    /**
     * @param $plugin
     * @return bool
     */
    function isPluginActive($plugin)
    {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins', array()));
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        return in_array($plugin, $active_plugins, false) || array_key_exists($plugin, $active_plugins);
    }

    /**
     * clean the text
     * @param $var
     * @return array|mixed|string
     */
    function clean($var)
    {
        if (is_array($var)) {
            return array_map(array($this, 'clean'), $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }
}