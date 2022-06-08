<?php

use Illuminate\Validation\Factory;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;

/**
 * Bind validation instance to container
 */
$waffle_app->singleton('validation', function ($waffle_app) {
    $loader = new FileLoader($waffle_app->get('files'), 'lang');
    $translator = new Translator($loader, 'en');
    $presence = new DatabasePresenceVerifier($waffle_app->get('db')->getDatabaseManager());
    $validation = new Factory($translator, $waffle_app);

    $validation->setPresenceVerifier($presence);

    // Add custom validation rules
    $rules = apply_filters('waffle/validation-rules', $waffle_app->config->get('validation-rules'));

    foreach ($rules as $key => $rule_class) {
        $rule_class = new $rule_class();

        $validation->extend($key, function ($attribute, $value, $parameters, $validator) use ($rule_class) {
            return $rule_class->passes($attribute, $value, $parameters, $validator);
        }, $rule_class->message());
    }

    return $validation;
});
