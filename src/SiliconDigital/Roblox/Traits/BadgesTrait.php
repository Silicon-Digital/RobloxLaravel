<?php

namespace SiliconDigital\Roblox\Traits;

use BadMethodCallException;

trait BadgesTrait
{
    public static $badges_v1_api = 'https://badges.roblox.com/v1/';
    
    /**
     * Gets a badge by it's unique identifier.
     *
     * @param integer $badge_id The badge ID
     * @param array $cache_params Default API cache parameters
     * 
     * @return object
     */
    public static function getBadge(int $badge_id, $cache_params = [])
    {
        return self::get(self::$badges_v1_api . 'badges/' . $badge_id);
    }

    /**
     * Get badges for a universe
     *
     * @param integer $universe_id The universe ID to return badges for
     * @param integer $limit The limit of badges (supported ranges: 10, 25, 50, 100)
     * @param string $sort_order The sort order that will be returned (Asc/ Desc)
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getUniverseBadges(int $universe_id, int $limit = 10, string $sort_order = 'Asc', array $cache_params = [])
    {
        return self::get(self::$badges_v1_api . "universes/{$universe_id}/badges?limit={$limit}&sortOrder={$sort_order}", true, true);
    }

    /**
     * Gets badges for a single user
     *
     * @param integer $user_id User to find badges for 
     * @param integer $limit The limit of badges (supported ranges: 10, 25, 50, 100)
     * @param string $sort_order The sort order that will be returned (Asc/ Desc)
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getUserBadges(int $user_id, int $limit = 10, string $sort_order = 'Asc', array $cache_params = [])
    {
        return self::get(self::$badges_v1_api . "users/{$user_id}/badges?limit={$limit}&sortOrder={$sort_order}", true, true);
    }

    /**
     * Gets badge awarded dates for an array of passed through badge id's for a user.
     *
     * @param integer $user_id The user id to check the badges against
     * @param array $badge_ids An array of badge ID's
     * @param array $cache_params Default API cache parameters
     * 
     * @return array
     */
    public static function getBadgeAwardedDates(int $user_id, array $badge_ids, array $cache_params = [])
    {
        if (empty($badge_ids)) {
            throw new BadMethodCallException('You need to pass through at least one badge ID.');
        }

        $imploded_ids = implode(',', $badge_ids);
        return self::get(self::$badges_v1_api . "users/{$user_id}/badges/awarded-dates?badgeIds={$imploded_ids}", true, true);
    }
}
