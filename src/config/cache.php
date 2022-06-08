<?php

return [
    'default' => 'file',
    'ttl'     => 3600,
    'prefix'  => 'waffle',
    'stores'  => [
        'file' => [
            'driver' => 'file',
            'path'   => WP_CONTENT_DIR . '/waffle-cache',
        ],
    ],
];
