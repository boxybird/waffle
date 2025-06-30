<?php

namespace Tests\Feature;

test('it can push a job to the queue', function () {
    class JobTest
    {
        public function fire($job, $data)
        {
            $job->delete();
        }
    }

    waffle_queue()->push(JobTest::class, ['foo' => 'bar']);

    $job = waffle_db()->table('waffle_queue')
        ->where('payload', 'LIKE', '%"data":{"foo":"bar"}%')
        ->first();

    expect($job)->not()->toBeNull();

    // Cleanup
    waffle_db()->table('waffle_queue')->truncate();
});