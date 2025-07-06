<?php

use BoxyBird\Waffle\App;
use Illuminate\Queue\Capsule\Manager as Queue;
use Illuminate\Queue\Connectors\DatabaseConnector;

App::getInstance()->singleton('queue', function ($app) {
    global $wpdb;

    $table_name_queue = $wpdb->prefix.'waffle_queue';
    $table_name_logs = $wpdb->prefix.'waffle_queue_logs';
    $cache_group = 'waffle_schema';
    $table_queue_cache_key = 'waffle_queue_table_exists';
    $table_logs_cache_key = 'waffle_queue_logs_table_exists';

    if (wp_cache_get($table_queue_cache_key, $cache_group) !== '1') {
        if (!$app->get('db')->schema()->hasTable($table_name_queue)) {
            $app->get('db')->schema()->create($table_name_queue, function ($table): void {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        wp_cache_set($table_queue_cache_key, '1', $cache_group);
    }

    if (wp_cache_get($table_logs_cache_key, $cache_group) !== '1') {
        if (!$app->get('db')->schema()->hasTable($table_name_logs)) {
            $app->get('db')->schema()->create($table_name_logs, function ($table): void {
                $table->bigIncrements('id');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        wp_cache_set($table_logs_cache_key, '1', $cache_group);
    }

    $queue = new Queue($app);

    $queue->addConnection([
        'driver' => 'database',
        'table' => $table_name_queue,
        'queue' => 'default',
        'connection' => 'default',
        'host' => 'localhost',
    ]);

    $queue->getQueueManager()->addConnector('database', fn(): DatabaseConnector => new DatabaseConnector($app->get('db')->getDatabaseManager()));

    // $queue->setAsGlobal();

    return $queue->getQueueManager();
});