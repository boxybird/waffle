<?php

test('it can run a process and get the result', function (): void {
    $result = waffle_process()::run('echo Hello World');

    expect($result->successful())->toBeTrue()
        ->and($result->failed())->toBeFalse()
        ->and($result->exitCode())->toBe(0)
        ->and($result->output())->toContain('Hello World')
        ->and($result->errorOutput())->toBeEmpty();
});