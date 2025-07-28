<?php

test('it creates and verifies a file with local disk driver', function (): void {
    $base_root = wp_upload_dir()['basedir'];
    $base_url = wp_upload_dir()['baseurl'];

    $disk = waffle_storage()->build([
        'driver' => 'local',
        'root' => $base_root,
        'url' => $base_url,
    ]);

    $disk->put('custom-folder/hello-world.txt', 'Hello World');

    $url = $disk->url('custom-folder/hello-world.txt');

    expect($url)->toBe($base_url.'/custom-folder/hello-world.txt')
        ->and($disk->exists('custom-folder/hello-world.txt'))->toBeTrue();
});