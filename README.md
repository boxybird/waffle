# Waffle

## Installation

Install the package via composer.

```
composer require boxybird/waffle
```

## Available functions

[waffle_queue](#waffle_queue--waffle_worker)

[waffle_worker](#waffle_queue--waffle_worker)

[waffle_db](#waffle_db)

[waffle_validator](#waffle_validator)

[waffle_encrypter](#waffle_encrypter)

[waffle_http](#waffle_http)

[waffle_cache](#waffle_cache)

[waffle_session](#waffle_session)

[waffle_storage](#waffle_storage)

[waffle_request](#waffle_request)

[waffle_collection](#waffle_collection)

[waffle_str](#waffle_str)

[waffle_arr](#waffle_arr)

[waffle_uri](#waffle_uri)

[waffle_benchmark](#waffle_benchmark)

---

### waffle_queue / waffle_worker
```php
<?php

// Define a job class
class LongRunningJob
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Handle the job. 
     *
     */
    public function fire($job, $data)
    {
        // Simulate long running code
        sleep($data['how_long']);

        // Something may or may not have gone wrong
        $oops = false;

        // If $oops === true release the job back to the queue to try 
        // again based on waffle_worker()->setOptions(['maxTries' => 3 // default is 1])
        // Failed jobs are caught and stored in the database and can be
        // viewed in the admin (/wp-admin/tools.php?page=waffle-options.php)
        if ($oops) {
            return $job->release();
        }

        // Delete the job from the queue
        $job->delete();
    }
}

// Push a job onto the default queue
waffle_queue()->push(LongRunningJob::class, ['how_long' => 5]);

// Push a job onto a custom queue
waffle_queue()->push(LongRunningJob::class, ['how_long' => 5], 'my_custom_queue');

// Run the default queue worker in the background
waffle_worker()->work();

// Run the custom queue worker in the background
waffle_worker(['my_custom_queue'])->work();

// Run multiple queue workers in the background. Array order determines the priority
waffle_worker(['my_custom_queue', 'default'])->work();

// Run a queue worker in the background with overridden global options
waffle_worker()->setOptions([
    'memory'   => 256, // default is 128 (MB)
    'sleep'    => 3, // default is 0 (seconds to sleep after each job)
    'maxTries' => 3, // default is 1 (number of times to try a job before failing)
    'maxJobs'  => 3, // default is 500 (number of jobs to process before stopping)
    'maxTime'  => 90, // default is 60 (number of seconds to process each job before stopping)        
    'timeout'  => 120, // Attempts to default to 80% of the servers max_execution_time, else default is 60 seconds (server timeout/worker timeout)
])->work();

// Notes on setOptions(): 
// Both 'timeout' and 'maxTime' work hand in hand as exceptions are thrown if either exceeds server timeout
// Defined Job class properties will override global setOptions() values

// Notes on workers:
// Workers use WP-Cron to run in the background. The interval is 60 seconds
// Only a single worker can be run at a time. The last declared worker will be the one that runs
waffle_worker()->work(); // This will not run
waffle_worker(['my_custom_queue', 'default'])->work(); // This will run 
```
---

### waffle_db

Reference: https://laravel.com/docs/12.x/queries
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

### waffle_validator

Reference: // https://laravel.com/docs/12.x/validation
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

## waffle_encrypter

Reference: https://laravel.com/docs/12.x/encryption
```php
<?php

// Add to wp-config.php
define('WAFFLE_ENCRYPTER_KEY', 'REPLACE_WITH_SOME_16_CHARACTER_STRING');

// Example usage
$encrypted = waffle_encrypter()->encrypt('Some secret');

$decrypted = waffle_encrypter()->decrypt($encrypted);
```

## waffle_http

Reference: https://laravel.com/docs/12.x/http-client
```php
<?php

// GET
$response = waffle_http()->get('https://rickandmortyapi.com/api/character');

if ($response->failed()) {
    // Handle error
}

$data = $response->json();

// POST
$response = waffle_http()
    ->withHeaders([
        'Bearer' => 'SOME_TOKEN',
    ])
    ->post('https://example.test/create', [
        'name'   => 'John Doe',
        'status' => 'active',
    ]);
```

## waffle_cache

Reference: https://laravel.com/docs/12.x/cache
```php
<?php

// Cache forever, or until the cache is flushed
waffle_cache()->put('foo', 'bar');

// Cache for 60 seconds
waffle_cache()->put('foo', 'bar', 60);

// Get cached value
$foo = waffle_cache()->get('foo');

// Flush the cache
waffle_cache()->flush();

// -----

// Cache the query results for 1 hour. If the hour is up, the query will be run again.
$posts = waffle_cache()->remember('posts', HOUR_IN_SECONDS, function () {
    $query = new WP_Query([
        // SOME EXPENSIVE QUERY ARGS
    ]);

    return $query->get_posts();
});

// Flush the cache on some action
add_action('save_post', function ($post_id) {
    // Flush the entire cache
    waffle_cache()->flush();

    // Flush a specific key
    waffle_cache()->forget('posts');
});
```

## waffle_session

Reference: https://laravel.com/docs/12.x/session
```php
<?php

// Handle logic to determine 'status' of the user
// Store items in the session
waffle_session()->put('status', $active);

// Retrieve an item from the session if it exists
if (waffle_session()->has('status')) {
    echo waffle_session()->get('status');
}

// Handle example form submission...
// Store items in the session for the next request only
waffle_session()->flash('message', 'Thanks for signing up!');

// Retrieve a flash message if it exists
if (waffle_session()->has('message')) {
    echo waffle_session()->get('message');
}
```

## waffle_storage

Reference: https://laravel.com/docs/12.x/filesystem
```php
<?php

// LOCAL
$disk = waffle_storage()->build([
    'driver' => 'local',
    'root'   => wp_upload_dir()['basedir'],
    'url'    => wp_upload_dir()['baseurl'],
]);

// /wp-content/uploads/custom-folder/hello-world.txt
$disk->put('custom-folder/hello-world.txt', 'Hello World');

// https://example.test/wp-content/uploads/custom-folder/hello-world.txt
$url = $disk->url('custom-folder/hello-world.txt');

// -----

// AMAZON S3
$disk = waffle_storage()->build([
    'driver' => 's3',
    'key'    => 'YOUR_KEY',
    'secret' => 'YOUR_SECRET',
    'region' => 'YOUR_REGION',
    'bucket' => 'YOUR_BUCKET',
]);

$disk->put('hello-world.txt', 'Hello World', 'public');

// https://YOUR_BUCKET.s3.amazonaws.com/hello-world.txt
$url = $disk->url('hello-world.txt');
```

## waffle_request

Reference: https://laravel.com/docs/12.x/requests
```php
<?php

// https://example.com?first_name=Jane&last_name=Doe&age=40

$params = waffle_request()->input(); 
// [
//     'first_name' => 'Jane',
//     'last_name' => 'Doe',
//     'age' => 40
// ];

$first_name = waffle_request()->only(['first_name']);
// [
//     'first_name' => 'Jane',
// ];
```

## waffle_collection

Reference: https://laravel.com/docs/12.x/collections
```php
<?php

$posts = waffle_collection(get_posts())
    ->map(function ($item) {
        return [
            'id'    => $item->ID,
            'title' => get_the_title($item->ID),
            'url'   => get_permalink($item->ID),
            'image' => get_the_post_thumbnail_url($item->ID),
        ];
    })
    ->toArray();
```

## waffle_str

Reference: https://laravel.com/docs/12.x/helpers#fluent-strings
```php
<?php

$string = waffle_str('hello world')
    ->replace('world', 'universe')
    ->snake()
    ->upper();

echo $string; // HELLO_UNIVERSE
```

## waffle_arr

Reference: https://laravel.com/docs/12.x/helpers#arrays-and-objects-method-list
```php
<?php

$array = ['products' => ['desk' => ['price' => 100]]];
 
$price = waffle_arr()->get($array, 'products.desk.price');

echo $price; // 100
```

## waffle_uri

Reference: https://laravel.com/docs/12.x/helpers#uri
```php
<?php

$uri = Uri::of('https://example.com')
    ->withScheme('https')
    ->withHost('test.com')
    ->withPort(8000)
    ->withPath('/users')
    ->withQuery(['page' => 2])
    ->withFragment('section-1');

echo $uri; // 'https://test.com:8000/users?page=2#section-1'
```

## waffle_benchmark

Reference: https://laravel.com/docs/12.x/helpers#benchmarking
```php
<?php

waffle_benchmark()->dd([
    fn() => get_posts(),
    fn() => waffle_db()->table('wp_posts')->take(10)->get(),
]);
```