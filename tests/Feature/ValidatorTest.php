<?php

test('can validate', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'age' => 20,
    ];

    $validator = waffle_validator($data,
        [
            'name' => 'required',
            'email' => 'required|email',
            'age' => 'required|integer|min:21',
        ],
        [
            'age.min' => 'Not old enough', // Custom error message for age.min
        ]
    );

    expect($validator->valid())
        ->toEqual([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ])
        ->and($validator->errors()->messages())->toEqual([
            'age' => [
                0 => 'Not old enough',
            ],
        ]);
});