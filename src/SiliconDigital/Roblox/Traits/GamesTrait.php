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
        if (is_array($universe_id) && count($universe_id) > 100) {
            throw new BadMethodCallException('You cannot pass through more than 100 universe IDs');
        }

        $imploded_ids = implode(',', Arr::wrap($universe_id));

        if (is_array($universe_id)) {
            $cache_params['isMultiget'] = true;
            $cache_params['identifier'] = md5($imploded_ids);
        } else {
            $cache_params['identifier'] = $universe_id;
        }

        $games = self::get(self::$games_v1_api . 'games/?universeIds=' . $imploded_ids, true, true, false, $cache_params);
        
        return $games->count() === 1 ? $games->first() : $games; 
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
        $cache_params['identifier'] = $place_id;

        return self::get(self::$games_v1_api . "games/{$place_id}/servers/{$type}/?sortOrder={$sort_order}&limit={$limit}", true, true, false, $cache_params);
    }

    /**
     * Gets a games product info by it's universe id, also supports an array of up to 100 universe ids.
     *
     * @param integer|array $universe_id The universe ID
     * @param array $cache_params Default API cache parameters

     * @return array
     */
    public static function gameProductInfo($universe_id, $cache_params = [])
    {
        if (is_array($universe_id) && count($universe_id) > 100) {
            throw new BadMethodCallException('You cannot pass through more than 100 universe IDs');
        }

        $imploded_ids = implode(',', Arr::wrap($universe_id));

        if (is_array($universe_id)) {
            $cache_params['isMultiget'] = true;
            $cache_params['identifier'] = md5($imploded_ids);
        } else {
            $cache_params['identifier'] = $universe_id;
        }

        return self::get(self::$games_v1_api . 'games/games-product-info/?universeIds=' . $imploded_ids, true, true);
    }

    /**
     * Lists games from a Roblox sort by passed through model options.
     *
     * @param array $api_model_options Model options found on the Roblox Documentation
     * @param array $cache_params Default API cache parameters

     * @return array
     */
    public static function listGames(array $api_model_options, $cache_params = [])
    {
        if (empty($api_model_options)) {
            throw new BadMethodCallException('You must pass through API model options');
        }

        return self::get(self::$games_v1_api . 'games/list/?' . http_build_query($api_model_options), true, false);
    }

    /**
     * Gets a game by it's place id, also supports an array of up to 100 place ids.
     *
     * @param integer|array $place_id The place ID
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getPlaceDetails($place_id, $cache_params = [])
    {
        if (is_array($place_id) && count($place_id) > 100) {
            throw new BadMethodCallException('You cannot pass through more than 100 place IDs');
        }

        $imploded_ids = implode(',', Arr::wrap($place_id));

        if (is_array($place_id)) {
            $cache_params['isMultiget'] = true;
            $cache_params['identifier'] = md5($imploded_ids);
        } else {
            $cache_params['identifier'] = $place_id;
        }

        $places = self::get(self::$games_v1_api . 'games/multiget-place-details/?placeIds=' . $imploded_ids, true, false, true);
        
        return $places->count() === 1 ? $places->first() : $places; 
    }

    /**
     * Gets extended place details from the Roblox API for a single place ID.
     *
     * @param integer $place_id The place ID
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getExtendedPlaceDetails($place_id, $cache_params = [])
    {
        $cache_params['identifier'] = $place_id;

        return self::get('https://www.roblox.com/places/api-get-details?assetId=' . $place_id, true, false, true);
    }

    /**
     * Gets a games playability status by universe id, also supports an array of up to 100 universe ids.
     *
     * @param integer|array $universe_id The universe ID
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getPlayabilityStatus($universe_id, $cache_params = [])
    {
        if (is_array($universe_id) && count($universe_id) > 100) {
            throw new BadMethodCallException('You cannot pass through more than 100 universe IDs');
        }

        $imploded_ids = implode(',', Arr::wrap($universe_id));

        if (is_array($universe_id)) {
            $cache_params['isMultiget'] = true;
            $cache_params['identifier'] = md5($imploded_ids);
        } else {
            $cache_params['identifier'] = $universe_id;
        }

        return self::get(self::$games_v1_api . 'games/multiget-playability-status/?universeIds=' . $imploded_ids, true, false, true);
    }

    /**
     * Gets reccomendations by algorithm name.
     *
     * @param string $algorithm_name The reccomendation algorithm name
     * @param array $api_model_options Model options found on the Roblox Documentation
     * @param array $cache_params Default API cache parameters

     * @return array
     */
    public static function getReccomendationsByAlgorithm(string $algorithm_name, array $api_model_options = [], $cache_params = [])
    {
        return self::get(self::$games_v1_api . 'games/recommendations/algorithm/' . $algorithm_name . '/?' . http_build_query($api_model_options), true, false);
    }

    
    /**
     * Gets recomendations for a universe id.
     *
     * @param integer $universe_id The universe id
     * @param array $api_model_options Model options found on the Roblox Documentation
     * @param array $cache_params Default API cache parameters

     * @return array
     */
    public static function getGameRecomendations(int $universe_id, array $api_model_options = [], $cache_params = [])
    {
        return self::get(self::$games_v1_api . 'games/recommendations/game/' . $universe_id . '/?' . http_build_query($api_model_options), true, false, true);
    }

    /**
     * Return a game sort by context
     *
     * @param string $sort_context The sort context
     * 
     * @return array
     */
    public static function getSorts(string $sort_context = 'GamesDefaultSorts')
    {
        return self::get(self::$games_v1_api . 'games/sorts?model.gameSortsContext=' . $sort_context, true, false);
    }

    /**
     * Check if an authenticated user has favourited a game.
     *
     * @param integer $universe_id The universe id
     * 
     * @return array
     */
    public static function hasFavorited(int $universe_id, $cache_params = [])
    {
        $cache_params['identifier'] = $universe_id;

        return self::get(self::$games_v1_api . 'games/' . $universe_id . '/favorites', true, false, true);
    }

    /**
     * Get the favourite count for a universe
     *
     * @param integer $universe_id The universe id
     * 
     * @return array
     */
    public static function getFavorites(int $universe_id, $cache_params = [])
    {
        $cache_params['identifier'] = $universe_id;

        return self::get(self::$games_v1_api . 'games/' . $universe_id . '/favorites/count', true, false, true);
    }

    /**
     * Get the gamepasses for a universe id.
     *
     * @param integer $universe_id The universe id
     * @param string $sort_order Sort ordering of the gamepasses (Asc, Desc)
     * @param integer $limit Limit of gamepasses (Max: 100)
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getGamepasses(int $universe_id, string $sort_order = 'Asc', int $limit = 100, $cache_params = [])
    {
        if (is_numeric($limit) && $limit > 100) {
            throw new BadMethodCallException('The limit cannot be greater than 100');
        }

        $cache_params['identifier'] = $universe_id;

        return self::get(self::$games_v1_api . 'games/' . $universe_id . '/game-passes?sortOrder=' . $sort_order . '&limit=' . $limit, true, true, true);
    }
}
