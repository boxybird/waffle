# Waffle

## Installation

Option 1: Install the package via composer. (**Recommended**)

```
composer require boxybird/waffle
```

Option 2: Clone or download as a plugin and run `composer install` before activating in WordPress Admin.

## Available functions

```php
waffle_db();
// https://laravel.com/docs/8.x/queries

waffle_validator();
// https://laravel.com/docs/8.x/validation

waffle_request();
// https://laravel.com/docs/8.x/requests

waffle_http();
// https://laravel.com/docs/8.x/http-client

waffle_session();
// https://laravel.com/docs/8.x/session

waffle_cache();
// https://laravel.com/docs/8.x/cache

waffle_collection();
// https://laravel.com/docs/8.x/collections

waffle_str();
// https://laravel.com/docs/8.x/helpers#fluent-strings

waffle_encrypter();
// https://laravel.com/docs/8.x/encryption
```

## Usage examples

```php
<?php

// Create a custom table in the database if it doesn't exist
if (!waffle_db()->schema()->hasTable('waffle_custom_table')) {
    waffle_db()->schema()->create('waffle_custom_table', function ($table) use ($wpdb) {
        $table->increments('id');
        $table->bigInteger('user_id')->unsigned();
        $table->string('extra_user_content');
        $table->timestamp('created_at')->default(waffle_db()::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')->default(waffle_db()::raw('CURRENT_TIMESTAMP'));
        $table->foreign('user_id')->references('ID')->on('wp_users');
    });
}

// Insert data
waffle_db()->table('waffle_custom_table')->insert([
    'user_id' => get_current_user_id(),
    'extra_user_content' => 'Some extra content about the user.',
]);

// Query new table and join with user table
$query = waffle_db()
    ->table('waffle_custom_table')
    ->join('wp_users', 'wp_users.ID', '=', 'waffle_custom_table.user_id')
    ->select('wp_users.*', 'waffle_custom_table.extra_user_content')
    ->where('waffle_custom_table.user_id', get_current_user_id())
    ->get();
```

```php
<?php

$data = [
    'name'  => 'John Doe',
    'email' => 'john@doe.com',
    'age'   => 20,
];

$validator = waffle_validator($data,
    [
        'name'  => 'required',
        'email' => 'required|email',
        'age'   => 'required|integer|min:21',
    ],
    [
        'age.min' => 'Not old enough', // Custom error message for age.min
    ]
);
```

```php
<?php

// Add to wp-config.php
define('WAFFLE_ENCRYPTER_KEY', 'REPLACE_WITH_SOME_16_CHARACTER_STRING');

// Example usage
$encrypted = waffle_encrypter()->encrypt('Some secret');

$decrypted = waffle_encrypter()->decrypt($encrypted);
```
