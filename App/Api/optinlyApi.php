<?php

namespace Optinly\App\Api;
defined('ABSPATH') or die;

use Exception;

class optinlyApi
{
    protected $api_url = "https://apinew.optinly.com/v1/";
    public $app_url = "https://app.optinly.com/";
    public $popup_js_url = "https://cdn.optinly.net/v1/optinly.js";

    /**
     * Verify the app id entered by the user
     * @param $app_id
     * @param $site_url
     * @return bool
     * @throws \Exception
     */
    function validateAppId($app_id, $site_url)
    {
        if (empty($app_id)) {
            throw new Exception("Please enter App Id");
        }
        if (empty($site_url)) {
            throw new Exception("Please enter Site url");
        }
        $body = array(
            "domain_name" => $site_url,
            "app_id" => $app_id
        );
        $url = $this->api_url . 'site/verify';
        $headers = array(
            'Content-Type' => 'application/json'
        );
        $response = $this->request($url, 'post', wp_json_encode($body), $headers);
        $msg = (isset($response->msg) && !empty($response->msg)) ? $response->msg : __('Error in connecting the App!Please try after sometimes!');
        if (!$response->success) {
            throw new Exception($msg);
        }
        return $msg;
    }

    /**
     * get operation for Remote URL
     * @param $url
     * @param $body
     * @param $method
     * @param array $headers
     * @param bool $blocking
     * @return array|bool|mixed|object|string
     */
    function request($url, $method = 'get', $body = '', $headers = array(), $blocking = true)
    {
        $response = '';
        try {
            switch ($method) {
                case 'post':
                    $args = array(
                        'body' => $body,
                        'timeout' => '30',
                        'httpversion' => '1.0',
                        'blocking' => $blocking,
                        'headers' => $headers
                    );
                    $result = wp_remote_post($url, $args);
                    break;
                default:
                case 'get':
                    $args = array(
                        'timeout' => '30',
                        'httpversion' => '1.0',
                        'blocking' => $blocking,
                        'headers' => $headers
                    );
                    $result = wp_remote_get($url, $args);
                    break;
            }
            $body = wp_remote_retrieve_body($result);
            if (is_string($body)) {
                $response = json_decode($body);
            } elseif (is_object($body)) {
                $response = $body;
            } elseif (is_array($body)) {
                $response = (object)$body;
            } else {
                $response = new \stdClass();
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
        return $response;
    }
}
