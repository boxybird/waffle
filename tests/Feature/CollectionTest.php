<?php

test('it can map over a collection and convert it to an array', function (): void {
    $items = waffle_collection(['one', 'two', 'three'])
        ->map(fn($item) => strtoupper((string) $item))
        ->toArray();

    expect($items)->toBe(['ONE', 'TWO', 'THREE']);
});