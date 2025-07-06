<?php

namespace BoxyBird\Waffle;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Factory as QueueManager;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Worker as QueueWorker;
use Illuminate\Queue\WorkerOptions;

class Worker extends QueueWorker
{
    protected array $options = [];

    /**
     * @see Illuminate\Queue\WorkerOptions
     */
    protected array $default_options = [
        'name' => 'default',
        'backoff' => 0,
        'memory' => 128,
        'timeout' => 60,
        'sleep' => 0,
        'maxTries' => 1,
        'force' => false,
        'stopWhenEmpty' => true,
        'maxJobs' => 500,
        'maxTime' => 60,
        'rest' => 0,
    ];

    public function __construct(
        QueueManager $queue,
        Dispatcher $events,
        ExceptionHandler $handler,
        callable $isDownForMaintenance,
        protected App $app,
        callable $resetScope = null,
        protected array $queues = [],
    ) {
        parent::__construct($queue, $events, $handler, $isDownForMaintenance, $resetScope);

        $this->app->get('events')->listen(JobExceptionOccurred::class, function (JobExceptionOccurred $event): void {
            $this->handleExceptionDatabaseLogging($event);
        });
    }

    public function setOptions(array $options = []): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * OVERRIDES PARENT CLASS
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions|null  $options
     */
    public function runNextJob($connectionName = 'default', $queue = 'default', WorkerOptions $options = null): void
    {
        $worker_options = $this->getWorkerOptions();

        $queues = array_merge($this->queues, [$queue]);

        foreach ($queues as $queue) {
            parent::runNextJob($connectionName, $queue, $worker_options);
        }
    }

    public function work(): void
    {
        $this->app->make(Scheduler::class)->call(function (): void {
            $worker_options = $this->getWorkerOptions();

            $this->daemon('default', implode(',', $this->queues), $worker_options);
        })->everyMinute();
    }

    protected function calculatorDefaultTimeout(): int
    {
        // Attempt to get the calculate timeout to be 80% of
        // the servers max_execution_time, else it's set to 60 seconds
        if (ini_get('max_execution_time') && is_numeric(ini_get('max_execution_time'))) {
            $timeout = intval((int) ini_get('max_execution_time') * 0.80);
        }

        return $timeout ?? 60;
    }

    protected function handleExceptionDatabaseLogging(JobExceptionOccurred $event): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix.'waffle_queue_logs';

        $this->app->get('db')->table($table_name)->insert([
            'queue' => $event->job->getQueue(),
            'payload' => $event->job->getRawBody(),
            'exception' => $event->exception->getMessage(),
        ]);
    }

    protected function getWorkerOptions(): WorkerOptions
    {
        $this->default_options['timeout'] = $this->calculatorDefaultTimeout();

        $options = array_merge($this->default_options, $this->options);

        if ($options['timeout'] === 0) {
            set_time_limit(0);
        }

        return new WorkerOptions(
            $options['name'],
            $options['backoff'],
            $options['memory'],
            $options['timeout'],
            $options['sleep'],
            $options['maxTries'],
            $options['force'],
            $options['stopWhenEmpty'],
            $options['maxJobs'],
            $options['maxTime'],
            $options['rest'],
        );
    }
}
