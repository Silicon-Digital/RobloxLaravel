<?php

namespace SiliconDigital\Roblox\Traits;

use BadMethodCallException;
use Illuminate\Support\Arr;

trait GamesTrait
{
    public static $games_v1_api = 'https://games.roblox.com/v1/';
    public static $games_v2_api = 'https://games.roblox.com/v2/';
    
    /**
     * Gets a game by it's universe id, also supports an array of up to 100 universe ids.
     *
     * @param integer|array $universe_id The universe ID
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getGame($universe_id, $cache_params = [])
    {
        $imploded_ids = implode(',', Arr::wrap($universe_id));

        return self::get(self::$games_v1_api . 'games/?universeIds=' . $imploded_ids, true, true);
    }

    /**
     * Get servers and their respective ping and fps by place ID
     *
     * @param integer $place_id The place id you want to get servers for
     * @param string $type The type of server (Public, Friend, VIP) 
     * @param integer $limit The limit of servers (supported ranges: 10, 25, 50, 100)
     * @param string $sort_order The sort order that will be returned (Asc/ Desc)
     * @param array $cache_params Default API cache parameters

     * @return array
     */
    public static function getServers(int $place_id, $type = 'Public', int $limit = 10, string $sort_order = 'Asc', array $cache_params = [])
    {
        return self::get(self::$games_v1_api . "games/{$place_id}/servers/{$type}/?sortOrder={$sort_order}&limit={$limit}", true, true);
    }

    /**
     * Gets a games product info by it's universe id, also supports an array of up to 100 universe ids.
     *
     *  @param integer|array $universe_id The universe ID
     * @param array $cache_params Default API cache parameters

     * @return array
     */
    public static function gameProductInfo($universe_id, $cache_params = [])
    {
        $imploded_ids = implode(',', Arr::wrap($universe_id));

        return self::get(self::$games_v1_api . 'games/games-product-info/?universeIds=' . $imploded_ids, true, true);
    }
}
