<?php

return [
    'default' => 'database',
    'ttl'     => 3600,
    'stores'  => [
        'database' => [
            'driver' => 'database',
            'table'  => 'waffle_cache',
        ],
    ],
];
