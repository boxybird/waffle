<?php

use BoxyBird\Waffle\App;
use Carbon_Fields\Field;
use Carbon_Fields\Container;

/**
 * This is an experimental feature. // TODO
 */
Container::make('theme_options', __('Waffle'))
    ->set_page_file('waffle-options.php')
    ->set_page_parent('tools.php')
    ->add_tab('Queue Logs', [
        Field::make('html', 'waffle_queue_logs_tab')
            ->set_html(function () {
                $app = App::getInstance();

                if (isset($_POST['waffle_queue_logs_delete'])
                    && wp_verify_nonce($_POST['_wpnonce'], 'waffle_queue_logs_delete')) {
                    $app->get('db')->table('waffle_queue_logs')->truncate();
                }

                $latest = 30;

                $logs_table = $app->get('db')->table('waffle_queue_logs');

                $logs_count = $logs_table->count();
                
                $logs = $logs_table->orderBy('id', 'desc')->take($latest)->get();

                ob_start();
                require_once __DIR__ . '/templates/queue-logs.php';
                
                return ob_get_clean();
            }),
    ])
    ->add_tab('Cache', [
        Field::make('html', 'waffle_cache_tab')
            ->set_html(function () {
                if (isset($_POST['waffle_cache_flush'])
                    && wp_verify_nonce($_POST['_wpnonce'], 'waffle_cache_flush')) {
                    waffle_cache()->flush();
                }

                ob_start();
                require_once __DIR__ . '/templates/cache.php';
                
                return ob_get_clean();
            }),
    ])
;
