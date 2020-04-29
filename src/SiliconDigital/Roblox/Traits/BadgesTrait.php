<?php

namespace SiliconDigital\Roblox\Traits;

trait BadgesTrait
{
    public $api_url = null;

    public function __construct()
    {
        $this->api_url = config('roblox.badges_api');
    }
    
    /**
     * Gets a badge by it's unique identifier.
     *
     * @param integer $badge_id The badge ID
     * 
     * @return object
     */
    public function getBadge(int $badge_id)
    {
        return $this->get("{$this->api_url}/v1/badges/{$badge_id}");
    }
}
