<?php

use BoxyBird\Waffle\App;
use Illuminate\Validation\Factory;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;

/**
 * Bind validation instance to container
 */
App::getInstance()->singleton('validation', function ($app): Factory {
    $loader = new FileLoader($app->get('files'), 'lang');
    $translator = new Translator($loader, 'en');
    $presence = new DatabasePresenceVerifier($app->get('db')->getDatabaseManager());
    $validation = new Factory($translator, $app);

    $validation->setPresenceVerifier($presence);

    // Add custom validation rules
    $rules = apply_filters('waffle/validation-rules', $app->config->get('validation-rules'));

    foreach ($rules as $key => $rule_class) {
        $rule_class = new $rule_class();

        $validation->extend($key, function ($attribute, $value, $parameters, $validator) use ($rule_class) {
            return $rule_class->passes($attribute, $value, $parameters, $validator);
        }, $rule_class->message());
    }

    return $validation;
});
