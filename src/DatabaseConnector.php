<?php

namespace BoxyBird\Waffle;

use BoxyBird\Waffle\DatabaseQueue;
use Illuminate\Queue\Connectors\DatabaseConnector as IlluminateDatabaseConnector;

class DatabaseConnector extends IlluminateDatabaseConnector
{
    public function connect(array $config)
    {
        return new DatabaseQueue(
            $this->connections->connection($config['connection'] ?? null),
            $config['table'],
            $config['queue'],
            $config['retry_after'] ?? 60,
            $config['after_commit'] ?? null
        );
    }
}
