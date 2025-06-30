<?php

test('it can get a value from an array using dot notation', function () {
    $array = ['products' => ['desk' => ['price' => 100]]];

    $price = waffle_arr()->get($array, 'products.desk.price');

    expect($price)->toBe(100);
});