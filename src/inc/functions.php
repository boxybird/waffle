<?php

use BoxyBird\Waffle\App;
use BoxyBird\Waffle\Scheduler;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

if (!function_exists('waffle_app')) {
    /**
     * @return \BoxyBird\Waffle\App
     */
    function waffle_app()
    {
        return App::getInstance();
    }
}

if (!function_exists('waffle_request')) {
    /**
     * @return \Illuminate\Http\Request
     */
    function waffle_request()
    {
        return App::getInstance()->make('request');
    }
}

if (!function_exists('waffle_config')) {
    /**
     * @return \Illuminate\Config\Repository
     */
    function waffle_config()
    {
        return App::getInstance()->make('config');
    }
}

if (!function_exists('waffle_validator')) {
    /**
     * @return \Illuminate\Validation\Factory
     */
    function waffle_validator(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $default_messages = require __DIR__.'/../config/validation-messages.php';

        $messages = array_merge($default_messages, $messages);

        return App::getInstance()->get('validation')->make($data, $rules, $messages, $customAttributes);
    }
}

if (!function_exists('waffle_db')) {
    /**
     * @return \Illuminate\Database\Capsule\Manager
     */
    function waffle_db()
    {
        return App::getInstance()->make('db');
    }
}


if (!function_exists('waffle_session')) {
    /**
     * @return \Illuminate\Session\SessionManager
     */
    function waffle_session()
    {
        return App::getInstance()->make('session');
    }
}

if (!function_exists('waffle_cache')) {
    /**
     * @return \Illuminate\Cache\CacheManager
     */
    function waffle_cache()
    {
        $cache_manager = App::getInstance()->make('cache');

        $cache_manager->store();

        return $cache_manager;
    }
}

if (!function_exists('waffle_collection')) {
    /**
     * @return \Illuminate\Support\Collection
     */
    function waffle_collection(array $items = [])
    {
        return Collection::make($items);
    }
}

if (!function_exists('waffle_str')) {
    /**
     * @return \Illuminate\Support\Stringable
     */
    function waffle_str(string $string)
    {
        return Str::of($string);
    }
}

if (!function_exists('waffle_arr')) {
    function waffle_arr(): \Illuminate\Support\Arr
    {
        return new Arr();
    }
}

if (!function_exists('waffle_benchmark')) {
    function waffle_benchmark(): Benchmark
    {
        return new Benchmark();
    }
}

if (!function_exists('waffle_uri')) {
    function waffle_uri(): Uri
    {
        return new Uri();
    }
}

if (!function_exists('waffle_http')) {
    function waffle_http(): Factory
    {
        return new Factory();
    }
}

if (!function_exists('waffle_encrypter')) {
    /**
     * @return \Illuminate\Encryption\Encrypter
     */
    function waffle_encrypter()
    {
        return App::getInstance()->make('encrypter');
    }
}

if (!function_exists('waffle_storage')) {
    /**
     * @return \Illuminate\Filesystem\FilesystemManager
     */
    function waffle_storage()
    {
        return App::getInstance()->make('storage');
    }
}

if (!function_exists('waffle_queue')) {
    /**
     * @return \Illuminate\Queue\DatabaseQueue
     */
    function waffle_queue()
    {
        return App::getInstance()->make('queue');
    }
}

if (!function_exists('waffle_worker')) {
    /**
     * @return \BoxyBird\Waffle\Worker
     */
    function waffle_worker(array $queues = ['default'])
    {
        return App::getInstance()->make('queue.worker')->setQueues($queues);
    }
}

if (!function_exists('waffle_schedule')) {
    function waffle_schedule(): Scheduler
    {
        return new Scheduler;
    }
};

if (!function_exists('waffle_carbon')) {
    function waffle_carbon(): Carbon
    {
        return new Carbon();
    }
}