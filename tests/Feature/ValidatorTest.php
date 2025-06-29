<?php

test('can validate', function () {
    $nonce = wp_create_nonce('secret');

    $data = [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'age' => 20,
        'nonce' => $nonce,
    ];

    $validator = waffle_validator($data,
        [
            'name' => 'required',
            'email' => 'required|email',
            'age' => 'required|integer|min:21',
            'nonce' => 'required|verify_nonce:secret',
        ],
        [
            'age.min' => 'Not old enough', // Custom error message for age.min
        ]
    );

    expect($validator->valid())
        ->toEqual([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'nonce' => $nonce,
        ])
        ->and($validator->errors()->messages())->toEqual([
            'age' => [
                0 => 'Not old enough',
            ],
        ]);
});