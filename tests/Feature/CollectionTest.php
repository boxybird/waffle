<?php

test('collections work', function () {
    $items = waffle_collection(['one', 'two', 'three'])
        ->map(function ($item) {
            return strtoupper($item);
        })
        ->toArray();

    expect($items)->toBe(['ONE', 'TWO', 'THREE']);
});