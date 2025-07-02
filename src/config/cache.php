<?php

global $wpdb;

$table_name = $wpdb->prefix.'waffle_cache';

return [
    'default' => 'database',
    'ttl' => 3600,
    'stores' => [
        'database' => [
            'driver' => 'database',
            'table' => $table_name,
        ],
    ],
];
