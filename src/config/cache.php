<?php

return [
    'cache.default'     => 'file',
    'cache.prefix'      => 'waffle',
    'cache.stores.file' => [
        'driver' => 'file',
        'path'   => WP_CONTENT_DIR . '/waffle-cache',
    ],
];
