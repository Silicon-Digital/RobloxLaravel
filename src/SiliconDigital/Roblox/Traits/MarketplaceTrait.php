<?php

namespace SiliconDigital\Roblox\Traits;

trait MarketplaceTrait
{
    public static $marketplace_api = 'https://api.roblox.com/marketplace/';
    
    /**
     * Gets a single gamepasses info
     *
     * @param integer $gamepass_id The gamepass ID
     * @param array $cache_params Default API cache parameters
     * 
     * @return void
     */
    public static function getGamepassInfo(int $gamepass_id, array $cache_params = [])
    {
        $cache_params['identifier'] = $gamepass_id;

        return self::get(self::$marketplace_api . 'game-pass-product-info?gamePassId=' . $gamepass_id, true, false, false, $cache_params);
    }
}
