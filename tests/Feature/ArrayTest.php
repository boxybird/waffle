<?php

test('arr works', function () {
    $array = ['products' => ['desk' => ['price' => 100]]];

    $price = waffle_arr()->get($array, 'products.desk.price');

    expect($price)->toBe(100);
});