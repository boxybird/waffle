<?php

test('it can store retrieve and delete a file from storage', function () {
    $disk = waffle_storage()->build([
        'driver' => 'local',
        'root' => __DIR__.'/../storage',
        'url' => 'https://test.com/storage',
    ]);

    $path = 'custom-folder/hello-world.txt';
    $contents = 'Hello World';

    $store = $disk->put($path, $contents);

    expect($store)->toBeTrue();

    $retrieved = $disk->get($path);

    expect($retrieved)->toBe('Hello World');

    $url = $disk->url($path);

    expect($url)->toBe('https://test.com/storage/custom-folder/hello-world.txt');

    $deleted = $disk->delete($path);

    expect($deleted)->toBeTrue();
});