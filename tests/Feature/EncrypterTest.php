<?php

test('can encrypt and then decrypt', function () {
    $encrypted = waffle_encrypter()->encrypt('Some secret');
    $decrypted = waffle_encrypter()->decrypt($encrypted);

    expect($decrypted)->toBe('Some secret');
});
