# Roblox Laravel API
Unofficial Roblox API Wrapper for Laravel 9

## Installation
```bash
composer require silicondigital/roblox
```

## Functions

### Badges
* `getBadge(int $badge_id)` - Returns a single badge
* `getUniverseBadges(int $universe_id, int $limit = 10, string $sort_order = 'Asc')` - Returns badges for a universe
* `getUserBadges(int $user_id, int $limit = 10, string $sort_order = 'Asc')` - Returns badges for a single user
* `getBadgeAwardedDates(int $user_id, array $badge_ids)` - Returns the awarded dates for an array of badge ids for a user.

## Caching Responses
To speed up your application this API provides caching functionality using Laravel's built in cache functions. You'll need to make sure this is configured before using this feature. [Laravel Docs: Cache](https://laravel.com/docs/master/cache).
#### All API's will allow you to pass through options for the cache, the key must be unique otherwise it will be overwritten. Typically you should set the key name as what the response will be for example ``badge_{badge_id}``.
| Option  | Functionality | Example             |
| ------------- | ------------- | ------------- |
| key_name  | A **unique** key name for the cached response  | badge_1234  |
| ttl | How long the data will be cached for (in seconds)  | 3600 |
|tags | An array of tags to append to the cached item | ['badges'] |

Internally the API will prefix the cache key with ``roblox_``, if you wish to interact with the cached data directly using the Laravel cache facade see the below examples.
```php
use Illuminate\Support\Facades\Cache;

Cache::get('roblox_{your key name}'); // e.g roblox_badge_1234
Cache::tags(['badges'])->flush();
```
