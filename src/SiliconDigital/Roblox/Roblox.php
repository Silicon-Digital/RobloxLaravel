<?php

namespace SiliconDigital\Roblox;

use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use SiliconDigital\Roblox\Traits\BadgesTrait;
use SiliconDigital\Roblox\Traits\GamesTrait;
use Illuminate\Support\Facades\Cache;
use SiliconDigital\Roblox\Traits\MarketplaceTrait;
use SiliconDigital\Roblox\Traits\UsersTrait;

class Roblox
{
    use BadgesTrait;
    use GamesTrait;
    use MarketplaceTrait;
    use UsersTrait;

    /**
     * Store the config values.
     */
    private static $roblox_config;

    /**
     * Store the config values for the parent class.
     */
    private static $parent_config;

    private static $session;

    private static $cache_config = [];

    public static function query($request_url, $requestMethod = 'get', $json_response = true, $data_array = false, $cookies = [])
    {
        $from_cache = self::fromCache();
        if ($from_cache) {
            self::$cache_config = [];
            return $from_cache;
        }
        $request = Http::withCookies($cookies, '.roblox.com')
            ->$requestMethod($request_url);

            if ($request->successful()) {
            $response = null;
            if ($json_response) {
                $response = self::formatRobloxJson($request->json(), $data_array);
            } else {
                $response =  $request->body();
            }

            if ($response) {
                self::cacheResponse($response);
                
                return $response;
            }
        } else if ($request->clientError()) {
            if (
                App::isLocal() &&
                $request->json() &&
                Arr::has($request->json(), 'errors')
            ) {
                return self::handleRobloxApiError(collect($request->json()['errors']));
            }
            throw new BadMethodCallException('A client error occoured when trying to make the request to Roblox');
        } else if ($request->serverError()) {
            throw new BadMethodCallException('The Roblox API returned a server error with the following code ' . $request->status());
        }
    }

    public static function get($path, $is_json = true, $data_array = false, $authenticated = false, $cache_params = [])
    {
        $cookies = [];
        if ($authenticated) {
            $roblox_token = config('roblox.ROBLOX_TOKEN', null);
            if (!$roblox_token) {
                throw new BadMethodCallException('You\'ve not set the ROBLOX_TOKEN variable in your .env file.');
            }

            $cookies = [
                '.ROBLOSECURITY' => $roblox_token
            ];
        }

        self::setCacheParams($cache_params);

        return self::query(
            $path,
            'get',
            $is_json,
            $data_array,
            $cookies,
            $cache_params
        );
    }

    public static function post($name, $parameters = [], $multipart = false, $extension = 'json', $appOnly = false)
    {
        return self::query($name, 'post', $parameters, $multipart, $extension, $appOnly);
    }

    public function delete($name, $parameters = [], $multipart = false, $extension = 'json', $appOnly = false)
    {
        return self::query($name, 'DELETE', $parameters, $multipart, $extension, $appOnly);
    }

    /**
     * Sets the cache paremeters for the request
     *
     * @param array $parameters Cache parameters
     * 
     * @return void
     */
    public static function setCacheParams(array $parameters)
    {
        if (Arr::get($parameters, 'cache') && $parameters['cache']) {
            if (!Arr::get($parameters, 'key')) {
                throw new BadMethodCallException('You did not set a key in the cache configuration.');
            }
            
            if (!Arr::get($parameters, 'ttl')) {
                throw new BadMethodCallException('You did not set a TTL in the cache configuration.');
            }

            self::$cache_config = $parameters;
            self::$cache_config['key'] = 'roblox:' . str_replace('{id}', $parameters['identifier'], $parameters['key']);
        }
    }

    public static function cacheResponse($data)
    {
        if (Arr::get(self::$cache_config, 'cache')) {
            Cache::put(self::$cache_config['key'], $data, self::$cache_config['ttl']);
        }
    }

    public static function fromCache() {
        return Arr::get(self::$cache_config, 'cache') ? Cache::get(self::$cache_config['key']) : false;
    }

    /**
     * Handles a Roblox API error response
     *
     * @param Illuminate\Support\Collection $error
     * 
     * @return string
     */
    private static function handleRobloxApiError(Collection $errors)
    {
        $first_error = $errors->first();
        
        if ($first_error && isset($first_error['message'])) {
            if ($first_error['message'] === 'Authorization has been denied for this request.') {
                throw new BadMethodCallException('To use this API you\'ll need to set the ROBLOX_TOKEN variable in your .env file with a valid .ROBLOSECURITY cookie.');
            }

            return $first_error['message'];
        }

        return 'A Roblox API error occoured';
    }

    private static function formatRobloxJson($json, $data_array)
    {
        $json = $data_array ? $json['data'] : $json;
        $date_casts = ['created', 'updated', 'awardedDate', 'Created', 'Updated'];

        if ($data_array) {
            foreach ($json as &$array_object) {
                foreach ($date_casts as $cast) {
                    if (isset($array_object[$cast])) {
                        $array_object[$cast] = Carbon::parse($array_object[$cast]);
                    }
                }
            }
        } else {
            foreach ($date_casts as $cast) {
                if (isset($json[$cast])) {
                    $json[$cast] = Carbon::parse($json[$cast]);
                }
            }
        }

        return collect($json);
    }
}
