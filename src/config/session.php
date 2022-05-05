<?php

return [
    'lifetime'        => 120,
    'prefix'          => '',
    'path'            => '/',
    'domain'          => $_SERVER['HTTP_HOST'],
    'secure'          => false,
    'httponly'        => true,
    'same_site'       => 'lax',
    'raw'             => false,
    'lottery'         => [2, 100],
    'cookie'          => 'waffle_session',
    'driver'          => 'database',
    'expire_on_close' => false,
    'table'           => 'waffle_sessions',
    'connection'      => 'default',
];
