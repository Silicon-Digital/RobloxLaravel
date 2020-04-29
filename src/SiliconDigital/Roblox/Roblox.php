<?php

namespace SiliconDigital\Roblox;

use Illuminate\Config\Repository as Config;
use Illuminate\Session\Store as SessionStore;
use RunTimeException;
use SiliconDigital\Roblox\Traits\BadgesTrait;

class Roblox
{
    use BadgesTrait;

    /**
     * Store the config values.
     */
    private $roblox_config;

    /**
     * Store the config values for the parent class.
     */
    private $parent_config;

    private $session;

    /**
     * Only for debugging.
     */
    private $debug;

    public function __construct(Config $config, SessionStore $session)
    {
        if ($config->has('roblox::config')) {
            $this->roblox_config = $config->get('roblox::config');
        } elseif ($config->get('roblox')) {
            $this->roblox_config = $config->get('roblox');
        } else {
            throw new RunTimeException('No config found');
        }

        $this->debug = (isset($this->roblox_config['debug']) && $this->roblox_config['debug']) ? true : false;
        $this->session = $session;

        $this->parent_config = [];
        $this->parent_config['roblox_token'] = $this->roblox_config['ROBLOX_TOKEN'];

        $config = array_merge($this->parent_config, $this->roblox_config);

        parent::__construct($this->parent_config);
    }

    public function query($request_url, $requestMethod = 'GET', $parameters = [], $multipart = false, $extension = 'json', $appOnly = false)
    {
        $url = parent::url($request_url);

        if ($appOnly) {

            parent::apponly_request(['method' => $requestMethod, 'url' => $request_url]);
        } else {
            parent::user_request([
                'method'    => $requestMethod,
                'host'      => $request_url,
                'url'       => $url,
                'params'    => $parameters,
                'multipart' => $multipart,
            ]);
        }

        $response = $this->response;

        $format = 'object';

        if (isset($parameters['format'])) {
            $format = $parameters['format'];
        }

        $error = $response['error'];

        if ($error) {
            ## Error handling
        }

        if (isset($response['code']) && ($response['code'] < 200 || $response['code'] > 206)) {
            $_response = $this->jsonDecode($response['response'], true);

            if (is_array($_response)) {
                if (array_key_exists('errors', $_response)) {
                    $error_code = $_response['errors'][0]['code'];
                    $error_msg = $_response['errors'][0]['message'];
                } else {
                    $error_code = $response['code'];
                    $error_msg = $response['error'];
                }
            } else {
                $error_code = $response['code'];
                $error_msg = ($error_code == 503) ? 'Service Unavailable' : 'Unknown error';
            }

            throw new RunTimeException('['.$error_code.'] '.$error_msg, $response['code']);
        }

        switch ($format) {
            default:
            case 'object': $response = $this->jsonDecode($response['response']);
            break;
            case 'json': $response = $response['response'];
            break;
            case 'array': $response = $this->jsonDecode($response['response'], true);
            break;
        }

        return $response;
    }

    public function get($name, $parameters = [], $multipart = false, $extension = 'json', $appOnly = false)
    {
        return $this->query($name, 'GET', $parameters, $multipart, $extension, $appOnly);
    }

    public function post($name, $parameters = [], $multipart = false, $extension = 'json', $appOnly = false)
    {
        return $this->query($name, 'POST', $parameters, $multipart, $extension, $appOnly);
    }

    public function delete($name, $parameters = [], $multipart = false, $extension = 'json', $appOnly = false)
    {
        return $this->query($name, 'DELETE', $parameters, $multipart, $extension, $appOnly);
    }

    private function jsonDecode($json, $assoc = false)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            return json_decode($json, $assoc, 512, JSON_BIGINT_AS_STRING);
        } else {
            return json_decode($json, $assoc);
        }
    }
}
