<?php

use BoxyBird\Waffle\App;
use BoxyBird\Waffle\DatabaseConnector;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Queue\Capsule\Manager as Queue;

App::getInstance()->singleton('queue', function ($app) {
    global $wpdb;

    $table_name_queue = $wpdb->prefix.'waffle_queue';
    $table_name_logs = $wpdb->prefix.'waffle_queue_logs';

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

    if (!$app->get('db')->schema()->hasTable($table_name_logs)) {
        $app->get('db')->schema()->create($table_name_logs, function ($table): void {
            $table->bigIncrements('id');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    $queue = new Queue($app);

    $queue->addConnection([
        'driver' => 'database',
        'table' => $table_name_queue,
        'queue' => 'default',
        'connection' => 'default',
        'host' => 'localhost',
    ]);

    $queue->addConnector('database', fn(): DatabaseConnector => new DatabaseConnector($app->get('db')));

    // $queue->setAsGlobal();

    $queue_manager = $queue->getQueueManager();

    $resolver = new ConnectionResolver([
        'default' => $app->get('db')->getConnection(),
    ]);

    $queue_manager->addConnector('database', fn(): DatabaseConnector => new DatabaseConnector($resolver));

    return $queue_manager;
});
