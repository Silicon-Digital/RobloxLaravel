<?php

namespace SiliconDigital\Roblox\Facades;

use Illuminate\Support\Facades\Facade;

class Roblox extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'SiliconDigital\Roblox\Roblox';
    }
}
