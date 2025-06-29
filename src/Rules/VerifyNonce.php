<?php

namespace BoxyBird\Waffle\Rules;

use Illuminate\Validation\Rule;

class VerifyNonce extends Rule
{
    public function passes($attribute, $value, $parameters): bool
    {
        return (bool) wp_verify_nonce($value, $parameters[0] ?? null);
    }

    public function message(): string
    {
        return 'The nonce is invalid.';
    }
}
