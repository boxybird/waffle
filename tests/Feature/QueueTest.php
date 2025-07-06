<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Queue\WorkerOptions;
use ReflectionMethod;

class QueueTest
{
    public function fire($job, $data)
    {
        $job->delete();
    }
}

class FailingJobTest
{
    public function fire($job, $data)
    {
        throw new Exception('This job is meant to fail.');
    }
}

test('it can push a job to the queue', function () {
    waffle_queue()->push(QueueTest::class, ['foo' => 'bar']);

    $job = waffle_db()->table('wp_waffle_queue')
        ->where('payload', 'LIKE', '%"data":{"foo":"bar"}%')
        ->first();

    expect($job)->not()->toBeNull();
});

test('it can process the next job on the queue', function () {
    waffle_queue()->push(QueueTest::class, ['foo' => 'bar']);

    waffle_worker()->runNextJob();

    $job = waffle_db()->table('wp_waffle_queue')->first();

    expect($job)->toBeNull();
});

test('it logs failed jobs to the database', function () {
    waffle_queue()->push(FailingJobTest::class, ['foo' => 'baz']);

    try {
        waffle_worker()->runNextJob();
    } catch (Exception $e) {
        // We expect this exception, so we catch it and continue.
    }


    $log = waffle_db()->table('wp_waffle_queue_logs')
        ->where('exception', 'LIKE', '%This job is meant to fail.%')
        ->first();

    expect($log)->not()->toBeNull()
        ->and($log->payload)->toContain('FailingJobTest');

    // Clean up the database
    waffle_db()->table('wp_waffle_queue_logs')->truncate();
});

test('it respects custom worker options', function () {
    $worker = waffle_worker()->setOptions(['timeout' => 120, 'maxTries' => 3]);

    $get_worker_options = new ReflectionMethod($worker, 'getWorkerOptions');

    /** @var WorkerOptions $worker_options */
    $worker_options = $get_worker_options->invoke($worker);

    expect($worker_options->timeout)->toBe(120)
        ->and($worker_options->maxTries)->toBe(3);
});

test('it calculates default timeout from ini settings', function () {
    $worker = waffle_worker();

    $calculator_default_timeout = new ReflectionMethod($worker, 'calculatorDefaultTimeout');

    ini_set('max_execution_time', '100');
    $timeout = $calculator_default_timeout->invoke($worker);
    expect($timeout)->toBe(80);

    ini_set('max_execution_time', '0');
    $timeout = $calculator_default_timeout->invoke($worker);
    expect($timeout)->toBe(60);

    ini_set('max_execution_time', 'invalid');
    $timeout = $calculator_default_timeout->invoke($worker);
    expect($timeout)->toBe(60);
});