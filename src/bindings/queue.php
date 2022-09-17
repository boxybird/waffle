<?php

use BoxyBird\Waffle\App;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Queue\Capsule\Manager as Queue;
use Illuminate\Queue\Connectors\DatabaseConnector;

App::getInstance()->singleton('queue', function ($app) {
    // Create the cache table if it doesn't exist.
    if (!get_option('waffle_queue_table_exists')) {
        update_option('waffle_queue_table_exists', true, true);

        // Double check if cache table doesn't
        // exist as transient may be manually deleted.
        if (!$app->get('db')->schema()->hasTable('waffle_queue')) {
            $app->get('db')->schema()->create('waffle_queue', function ($table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });

            update_option('waffle_queue_table_exists', true, true);
        }
    }

    $queue = new Queue($app);

    $queue->addConnection([
        'driver'     => 'database',
        'table'      => 'waffle_queue',
        'queue'      => 'default',
        'connection' => 'default',
        'host'       => 'localhost',
    ]);

    $queue->addConnector('database', function () use ($app) {
        return new DatabaseConnector($app->get('db'));
    });

    // $queue->setAsGlobal();

    $queue_manager = $queue->getQueueManager();

    $resolver = new ConnectionResolver([
        'default' => $app->get('db')->getConnection(),
    ]);

    $queue_manager->addConnector('database', function () use ($resolver) {
        return new DatabaseConnector($resolver);
    });

    return $queue_manager;
});
