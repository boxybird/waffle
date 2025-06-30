<?php

test('it can get a successful response from a given url', function () {
    $url = get_bloginfo('url');

    $response = waffle_http()->get($url);

    expect($response->getStatusCode())->toBe(200);
});