<?php

use BoxyBird\Waffle\App;
use BoxyBird\Waffle\Scheduler;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Request;
use Illuminate\Queue\QueueManager;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (!function_exists('waffle_app')) {
    function waffle_app(): App
    {
        return App::getInstance();
    }
}

if (!function_exists('waffle_request')) {
    function waffle_request(): Request
    {
        return App::getInstance()->make('request');
    }
}

if (!function_exists('waffle_config')) {
    function waffle_config(): Repository
    {
        return App::getInstance()->make('config');
    }
}

if (!function_exists('waffle_validator')) {
    function waffle_validator(array $data, array $rules, array $messages = [], array $customAttributes = []): Validator
    {
        $default_messages = require __DIR__.'/../config/validation-messages.php';

        $messages = array_merge($default_messages, $messages);

        return App::getInstance()->get('validation')->make($data, $rules, $messages, $customAttributes);
    }
}

if (!function_exists('waffle_db')) {
    function waffle_db(): Manager
    {
        return App::getInstance()->make('db');
    }
}

if (!function_exists('waffle_session')) {
    function waffle_session(): SessionManager
    {
        return App::getInstance()->make('session');
    }
}

if (!function_exists('waffle_router')) {
    function waffle_router(callable|array $callback = null, bool $exit = true): void
    {
        try {
            $router = App::getInstance()->make('router');

            // Check for a [class, method] array and instantiate the class.
            if (is_array($callback) && isset($callback[0], $callback[1]) && is_string($callback[0])) {
                $class = new $callback[0];
                $method = $callback[1];
                $class->$method($router);
            } elseif (is_callable($callback)) {
                // Otherwise, assume it's a standard callable like a closure.
                call_user_func($callback, $router);
            }

            $response = $router->dispatch(App::getInstance()->make('request'));;

            $response->send();

            if ($exit) {
                exit;
            }
        } catch (NotFoundHttpException|MethodNotAllowedHttpException $e) {
            return;
        }
    }
}

if (!function_exists('waffle_cache')) {
    function waffle_cache(): CacheManager
    {
        $cache_manager = App::getInstance()->make('cache');

        $cache_manager->store();

        return $cache_manager;
    }
}

if (!function_exists('waffle_collection')) {
    function waffle_collection(array $items = []): Collection
    {
        return Collection::make($items);
    }
}

if (!function_exists('waffle_str')) {
    function waffle_str(string $string): Stringable
    {
        return Str::of($string);
    }
}

if (!function_exists('waffle_arr')) {
    function waffle_arr(): Arr
    {
        return new Arr;
    }
}

if (!function_exists('waffle_benchmark')) {
    function waffle_benchmark(): Benchmark
    {
        return new Benchmark;
    }
}

if (!function_exists('waffle_uri')) {
    function waffle_uri(): Uri
    {
        return new Uri;
    }
}

if (!function_exists('waffle_http')) {
    function waffle_http(): Factory
    {
        return new Factory();
    }
}

if (!function_exists('waffle_encrypter')) {
    function waffle_encrypter(): Encrypter
    {
        return App::getInstance()->make('encrypter');
    }
}

if (!function_exists('waffle_storage')) {
    function waffle_storage(): FilesystemManager
    {
        return App::getInstance()->make('storage');
    }
}

if (!function_exists('waffle_queue')) {
    function waffle_queue(): QueueManager
    {
        return App::getInstance()->make('queue');
    }
}

if (!function_exists('waffle_worker')) {
    function waffle_worker(array $queues = ['default'])
    {
        return App::getInstance()->make('queue.worker', $queues);
    }
}

if (!function_exists('waffle_schedule')) {
    function waffle_schedule(): Scheduler
    {
        return App::getInstance()->make(Scheduler::class);
    }
};

if (!function_exists('waffle_carbon')) {
    function waffle_carbon(): Carbon
    {
        return new Carbon;
    }
}