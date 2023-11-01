<?php

// Register the admin page
add_action('admin_menu', function () {
    add_submenu_page(
        'tools.php',
        'Waffle',
        'Waffle',
        'manage_options',
        'waffle-options.php',
        function () {
            require_once __DIR__ . '/admin/templates/options.php';
        }
    );
});
