<?php

use BoxyBird\Waffle\App;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Http\Client\Factory;

/**
 * @see BoxyBird\Waffle
 */
if (!function_exists('waffle_app')) {
    function waffle_app()
    {
        return App::getInstance();
    }
}

/**
 * @see Illuminate\Http\Request
 */
if (!function_exists('waffle_request')) {
    function waffle_request()
    {
        return App::getInstance()->make('request');
    }
}

/**
 * @see Illuminate\Config\Repository
 */
if (!function_exists('waffle_config')) {
    function waffle_config()
    {
        return App::getInstance()->make('config');
    }
}

/**
 * @see Illuminate\Validation\Factory
 */
if (!function_exists('waffle_validator')) {
    function waffle_validator(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $default_messages = require __DIR__ . '/../config/validation-messages.php';

        $messages = array_merge($default_messages, $messages);

        return App::getInstance()->get('validation')->make($data, $rules, $messages, $customAttributes);
    }
}

/**
 * @see Illuminate\Database\Capsule\Manager as Capsule
 */
if (!function_exists('waffle_db')) {
    function waffle_db()
    {
        return App::getInstance()->make('db');
    }
}

/**
 * @see Illuminate\Session\SessionManager
 */
if (!function_exists('waffle_session')) {
    function waffle_session()
    {
        return App::getInstance()->make('session');
    }
}

/**
 * @see Illuminate\Cache\CacheManager
 */
if (!function_exists('waffle_cache')) {
    function waffle_cache()
    {
        $cache_manager = App::getInstance()->make('cache');

        $cache_manager->store();

        return $cache_manager;
    }
}

/**
 * @see Illuminate\Support\Collection
 */
if (!function_exists('waffle_collection')) {
    function waffle_collection(array $items = [])
    {
        return Collection::make($items);
    }
}

/**
 * @see \Illuminate\Support\Stringable
 */
if (!function_exists('waffle_str')) {
    function waffle_str(string $string)
    {
        return Str::of($string);
    }
}

/**
 * @see Illuminate\Http\Client\Factory
 */
if (!function_exists('waffle_http')) {
    function waffle_http()
    {
        return new Factory();
    }
}
