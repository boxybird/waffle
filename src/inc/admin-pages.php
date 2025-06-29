<?php

// Register the admin page
add_action('admin_menu', function (): void {
    add_submenu_page(
        'tools.php',
        'Waffle',
        'Waffle',
        'manage_options',
        'waffle-options.php',
        function (): void {
            require_once __DIR__ . '/admin/templates/options.php';
        }
    );
});
