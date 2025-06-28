<?php

test('can get a response', function () {
    $url = get_bloginfo('url');

    $response = waffle_http()->get($url);

    expect($response->getStatusCode())->toBe(200);
});