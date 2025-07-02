<?php

namespace Tests\Feature;

class QueueTest
{
    public function fire($job, $data)
    {
        $job->delete();
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
    waffle_worker()->runNextJob();

    $job = waffle_db()->table('wp_waffle_queue')->first();

    expect($job)->toBeNull();
});
