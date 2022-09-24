<?php

use BoxyBird\Waffle\App;
use BoxyBird\Waffle\DatabaseConnector;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Queue\Capsule\Manager as Queue;

App::getInstance()->singleton('queue', function ($app) {
    // Create the queue table if it doesn't exist.
    if (!get_option('waffle_queue_table_exists')) {
        update_option('waffle_queue_table_exists', true, true);

        // Double check if queue table doesn't
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

    // Create the queue logs table if it doesn't exist.
    if (!get_option('waffle_queue_logs_table_exists')) {
        update_option('waffle_queue_logs_table_exists', true, true);

        // Double check if queue logs table doesn't
        // exist as transient may be manually deleted.
        if (!$app->get('db')->schema()->hasTable('waffle_queue_logs')) {
            $app->get('db')->schema()->create('waffle_queue_logs', function ($table) {
                $table->bigIncrements('id');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }
        
        update_option('waffle_queue_logs_table_exists', true, true);
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
