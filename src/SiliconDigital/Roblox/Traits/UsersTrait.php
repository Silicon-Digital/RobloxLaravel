<?php

namespace SiliconDigital\Roblox\Traits;

use BadMethodCallException;

trait UsersTrait
{
    public static $users_v1_api = 'https://users.roblox.com/v1/';
    
    /**
     * Validates a username
     *
     * @param string $display_name The display name
     * @param string $birthdate The new user's birthdate
     * @param array $cache_params Default API cache parameters
     * 
     * @return object
     */
    public static function validateNames(string $display_name, string $birthdate, $cache_params = [])
    {
        $cache_params['identifier'] = urlencode($display_name);
        
        return self::get(self::$users_v1_api . 'display-names/validate/?displayName=' . urlencode($display_name) . '&birthdate' . urlencode($birthdate), true, false, false, $cache_params);
    }

    /**
     * Validates a username
     *
     * @param int $user_id The user id
     * @param string $display_name The display name
     * @param array $cache_params Default API cache parameters
     * 
     * @return object
     */
    public static function validateNamesById(int $user_id, string $display_name, $cache_params = [])
    {
        $cache_params['identifier'] = $user_id;

        return self::get(self::$users_v1_api . 'users/' . $user_id  . '/validate/?displayName=' . urlencode($display_name), true, false, false, $cache_params);
    }

    /**
     * Gets a user by ID
     *
     * @param int $user_id The user id
     * 
     * @return object
     */
    public static function getUser(int $user_id, $cache_params = [])
    {
        $cache_params['identifier'] = $user_id;
        
        return self::get(self::$users_v1_api . 'users/' . $user_id , true, false, false, $cache_params);
    }

    /**
     * Get authenticated user.
     *
     * @return object
     */
    public static function getAuthenticatedUser($cache_params = [])
    {
        return self::get(self::$users_v1_api . 'users/authenticated', true, false, false, $cache_params);
    }

    /**
     * Gets a users status by ID
     *
     * @param int $user_id The user id
     * 
     * @return object
     */
    public static function getUserStatus(int $user_id, $cache_params = [])
    {
        $cache_params['identifier'] = $user_id;

        return self::get(self::$users_v1_api . 'users/' . $user_id , true, false, false, $cache_params);
    }

    /**
     * Search for users
     *
     * @param string $keyword Search keyboard 
     * @param int $limit Limit of search results (Max: 100)
     * 
     * @return object
     */
    public static function searchUsers(string $keyword, int $limit = 10, $cache_params = [])
    {
        if (is_numeric($limit) && $limit > 100) {
            throw new BadMethodCallException('The limit cannot be greater than 100');
        }

        $cache_params['identifier'] = urlencode($keyword);
        
        return self::get(self::$users_v1_api . 'users/search/?keyword=' . urlencode($keyword) . '&limit=' . $limit , true, true, false, $cache_params);
    }
}
