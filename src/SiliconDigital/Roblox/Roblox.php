<?php

namespace SiliconDigital\Roblox;

use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use RunTimeException;
use SiliconDigital\Roblox\Traits\BadgesTrait;
use SiliconDigital\Roblox\Traits\GamesTrait;

class Roblox
{
    use BadgesTrait;
    use GamesTrait;

    /**
     * Store the config values.
     */
    private static $roblox_config;

    /**
     * Store the config values for the parent class.
     */
    private static $parent_config;

    private static $session;

    public static function query($request_url, $requestMethod = 'get', $json_response = true, $data_array = false)
    {
        $request = Http::$requestMethod($request_url);
        if ($request->successful()) {
            if ($json_response) {
                return self::formatRobloxJson($request->json(), $data_array);
            } else {
                return $request->body();
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

    public static function get($path, $is_json = true, $data_array = false, $cache_params = [])
    {
        return self::query(
            $path,
            'get',
            $is_json,
            $data_array
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
            return $first_error['message'];
        }

        return 'A Roblox API error occoured';
    }

    private static function formatRobloxJson($json, $data_array)
    {
        $json = $data_array ? $json['data'] : $json;
        $date_casts = ['created', 'updated', 'awardedDate'];

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

        return $json;
    }
}
