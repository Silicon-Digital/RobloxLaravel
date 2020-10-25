<?php

namespace SiliconDigital\Roblox\Traits;

trait GroupsTrait
{
    public static $groups_v1_api = 'https://groups.roblox.com/v1/';
    public static $groups_v2_api = 'https://groups.roblox.com/v2/';
    
    /**
     * Gets a group by ID
     *
     * @param int $group_id The group id
     * 
     * @return object
     */
    public static function getGroup(int $group_id, $cache_params = [])
    {
        $cache_params['identifier'] = $group_id;
        
        return self::get(self::$groups_v1_api . 'groups/' . $group_id , true, false, false, $cache_params);
    }
}
