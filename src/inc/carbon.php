<?php

add_action('after_setup_theme', function () {
    \Carbon_Fields\Carbon_Fields::boot();
});

add_action('carbon_fields_register_fields', function () {
    require_once __DIR__ . '/admin/carbon.php';
});
