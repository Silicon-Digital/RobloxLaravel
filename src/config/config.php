<?php

/**
 * Some Roblox API's require authentication. Make sure to set the ROBLOX_TOKEN .env with your .ROBLOSECURITY
 * token, we advise using an alternate account.
 */ 

return [
    'debug'        => function_exists('env') ? env('APP_DEBUG', false) : false,

    'BASE_API'     => 'api.roblox.com',
    'GAMES_API'    => 'games.roblox.com',
    'BADGES_API'   => 'badges.roblox.com',
    'ROBLOX_TOKEN' => function_exists('env') ? env('ROBLOX_TOKEN', '') : '',
];
