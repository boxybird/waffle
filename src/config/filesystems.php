<?php

return [
    'default' => 'local',
    'disks'   => [
        'local' => [
            'driver' => 'local',
            'root'   => wp_upload_dir()['basedir'] . '/waffle',
        ],
    ]
];
