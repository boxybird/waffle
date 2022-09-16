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

                // Create the cache table if it doesn't exist.
                if (!get_option('waffle_queue_logs_table_exists')) {
                    update_option('waffle_queue_logs_table_exists', true, true);

                    // Double check if cache table doesn't
                    // exist as transient may be manually deleted.
                    if (!$app->get('db')->schema()->hasTable('waffle_queue_logs')) {
                        $app->get('db')->schema()->create('waffle_queue_logs', function ($table) {
                            $table->bigIncrements('id');
                            $table->text('queue');
                            $table->longText('exception');
                            $table->timestamp('failed_at')->useCurrent();
                        });
                    }
                }

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
