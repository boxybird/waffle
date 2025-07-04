<?php

test('it can encrypt and decrypt a string', function () {
    $encrypted = waffle_encrypter()->encrypt('Some secret');
    $decrypted = waffle_encrypter()->decrypt($encrypted);

    expect($decrypted)->toBe('Some secret');
});