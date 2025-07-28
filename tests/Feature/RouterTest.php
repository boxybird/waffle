<?php

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function (): void {
    waffle_app()->forgetInstances();
});

test('it can handle a basic GET request', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/test';

    $request = waffle_app()->instance(Request::class, Request::capture());

    waffle_router(function ($router): void {
        $router->get('/test', fn(): string => 'Hello from GET');
    }, false);

    $response = waffle_app()->make('router')->dispatch($request);

    expect($response->getContent())->toBe('Hello from GET')
        ->and($response->getStatusCode())->toBe(200);
});

test('it can handle a basic POST request', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/test';

    $request = waffle_app()->instance(Request::class, Request::capture());

    waffle_router(function ($router): void {
        $router->post('/test', fn(): string => 'Hello from POST');
    }, false);

    $response = waffle_app()->make('router')->dispatch($request);

    expect($response->getContent())->toBe('Hello from POST')
        ->and($response->getStatusCode())->toBe(200);
});

test('it can handle a basic PUT request', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_SERVER['REQUEST_URI'] = '/test';

    $request = waffle_app()->instance(Request::class, Request::capture());

    waffle_router(function ($router): void {
        $router->put('/test', fn(): string => 'Hello from PUT');
    }, false);

    $response = waffle_app()->make('router')->dispatch($request);

    expect($response->getContent())->toBe('Hello from PUT')
        ->and($response->getStatusCode())->toBe(200);
});

test('it can handle a basic PATCH request', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'PATCH';
    $_SERVER['REQUEST_URI'] = '/test';

    $request = waffle_app()->instance(Request::class, Request::capture());

    waffle_router(function ($router): void {
        $router->patch('/test', fn(): string => 'Hello from PATCH');
    }, false);

    $response = waffle_app()->make('router')->dispatch($request);

    expect($response->getContent())->toBe('Hello from PATCH')
        ->and($response->getStatusCode())->toBe(200);
});

test('it can handle a basic DELETE request', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'DELETE';
    $_SERVER['REQUEST_URI'] = '/test';

    $request = waffle_app()->instance(Request::class, Request::capture());

    waffle_router(function ($router): void {
        $router->delete('/test', fn(): string => 'Hello from DELETE');
    }, false);

    $response = waffle_app()->make('router')->dispatch($request);

    expect($response->getContent())->toBe('Hello from DELETE')
        ->and($response->getStatusCode())->toBe(200);
});

test('it returns 404 for non-existent routes', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/non-existent';

    $request = waffle_app()->instance(Request::class, Request::capture());

    waffle_router(function ($router): void {
        $router->get('/test', fn(): string => 'Hello from GET');
    }, false);

    try {
        waffle_app()->make('router')->dispatch($request);

        // False positive assertion. We want to hit the "catch" block.
        expect(true)->toBeFalse();
    } catch (NotFoundHttpException $e) {
        expect($e->getMessage())->toBe('The route non-existent could not be found.');
    }
});

test('it gracefully handles a NotFoundHttpException', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/non-existent-route';

    waffle_app()->instance(Request::class, Request::capture());

    // The waffle_router should catch the NotFoundHttpException and simply return.
    // No exception should be thrown from this call.
    waffle_router(function ($router): void {
        $router->get('/test', fn(): string => 'Hello from GET');
    }, false);

    // If we reach here, no exception was thrown, so the test passes.
    expect(true)->toBeTrue();
});

test('it re-throws exceptions other than NotFoundHttpException', function (): void {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/error';

    waffle_app()->instance(Request::class, Request::capture());

    waffle_router(function ($router): void {
        $router->get('/error', function (): void {
            throw new Exception('Something went wrong');
        });
    }, false);
})->throws(Exception::class, 'Something went wrong');