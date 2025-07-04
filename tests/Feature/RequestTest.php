<?php

use Illuminate\Http\Request;

beforeEach(function () {
    waffle_app()->forgetInstances();
});

test('it can process and retrieve GET request details', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/test';
    $_GET['name'] = 'Taffi';

    waffle_app()->instance(Request::class, Request::capture());

    expect(waffle_request()->method())->toBe('GET')
        ->and(waffle_request()->path())->toBe('test')
        ->and(waffle_request()->input('name'))->toBe('Taffi');
});

test('it can process and retrieve POST request details', function () {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/test';
    $_POST['name'] = 'Taffi';

    waffle_app()->instance(Request::class, Request::capture());

    expect(waffle_request()->method())->toBe('POST')
        ->and(waffle_request()->path())->toBe('test')
        ->and(waffle_request()->input('name'))->toBe('Taffi');
});

test('it can process and retrieve PUT request details', function () {
    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_SERVER['REQUEST_URI'] = '/test';
    $_POST['name'] = 'Taffi';

    waffle_app()->instance(Request::class, Request::capture());

    expect(waffle_request()->method())->toBe('PUT')
        ->and(waffle_request()->path())->toBe('test')
        ->and(waffle_request()->input('name'))->toBe('Taffi');
});

test('it can process and retrieve PATCH request details', function () {
    $_SERVER['REQUEST_METHOD'] = 'PATCH';
    $_SERVER['REQUEST_URI'] = '/test';
    $_POST['name'] = 'Taffi';

    waffle_app()->instance(Request::class, Request::capture());

    expect(waffle_request()->method())->toBe('PATCH')
        ->and(waffle_request()->path())->toBe('test')
        ->and(waffle_request()->input('name'))->toBe('Taffi');
});

test('it can process and retrieve DELETE request details', function () {
    $_SERVER['REQUEST_METHOD'] = 'DELETE';
    $_SERVER['REQUEST_URI'] = '/test';
    $_POST['name'] = 'Taffi';

    waffle_app()->instance(Request::class, Request::capture());

    expect(waffle_request()->method())->toBe('DELETE')
        ->and(waffle_request()->path())->toBe('test')
        ->and(waffle_request()->input('name'))->toBe('Taffi');
});