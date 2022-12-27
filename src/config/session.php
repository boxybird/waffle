<?php

$defaults = [
    'lifetime'        => 120,
    'prefix'          => '',
    'path'            => '/',
    'domain'          => $_SERVER['HTTP_HOST'] ?? null,
    'secure'          => false,
    'httponly'        => true,
    'same_site'       => 'lax',
    'raw'             => false,
    'lottery'         => [2, 100],
    'cookie'          => 'waffle_session',
    'expire_on_close' => false,
    'connection'      => 'default',
];

return array_merge(apply_filters('waffle/session_config', $defaults), [
    'driver' => 'database',
    'table'  => 'waffle_sessions',
]);
