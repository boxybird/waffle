<?php

namespace BoxyBird\Waffle;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Factory as QueueManager;
use Illuminate\Queue\Worker as QueueWorker;
use Illuminate\Queue\WorkerOptions;

class Worker extends QueueWorker
{
    protected $queues = [];

    protected $options = [];

    protected $current_job;

    protected $current_queue;

    /**
     * @see Illuminate\Queue\WorkerOptions
     */
    protected $default_options = [
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
        callable $resetScope = null
    ) {
        parent::__construct($queue, $events, $handler, $isDownForMaintenance, $resetScope);

        add_filter('cron_schedules', $this->addSchedule(...));
        add_action('waffle_worker_daemon', $this->daemonFactory(...));
    }

    public function setQueues(array $queues = ['default']): static
    {
        $this->queues = $queues;

        return $this;
    }

    public function addSchedule(array $schedules): array
    {
        $schedules['waffle_worker_daemon_schedule'] = [
            'interval' => 60,
            'display' => 'Every Minute',
        ];

        return $schedules;
    }

    public function setOptions(array $options = []): self
    {
        $this->options = $options;

        return $this;
    }

    public function work(): void
    {
        if (wp_next_scheduled('waffle_worker_daemon')) {
            return;
        }

        wp_schedule_event(time(), 'waffle_worker_daemon_schedule', 'waffle_worker_daemon');
    }

    public function daemonFactory(): void
    {
        $this->default_options['timeout'] = $this->calculatorDefaultTimeout();

        $options = array_merge($this->default_options, $this->options);

        // You made it decision. Don't blame me.
        if ($options['timeout'] === 0) {
            set_time_limit(0);
        }

        $worker_options = new WorkerOptions(
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

        try {
            $this->daemon('default', '', $worker_options);
        } catch (Exception $e) {
            $this->handleExceptionDatabaseLogging($e);
        }
    }

    /**
     * Copied to override the parent Illuminate\Queue\Worker::daemon method
     *
     * $_queue not used, but required by parent method
     * Rather, we use $this->queues within the method to determine which queue to process
     */
    public function daemon($connectionName, $_queue, WorkerOptions $options)
    {
        if ($supportsAsyncSignals = $this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }

        $lastRestart = $this->getTimestampOfLastQueueRestart();

        [$startTime, $jobsProcessed] = [hrtime(true) / 1e9, 0];

        while (true) {
            // MY ADDED CODE
            // Assign the current queue to the first queue in the array
            $queue = $this->queues[0] ?? 'default';
            $this->current_queue = $queue;
            // END MY ADDED CODE

            // Before reserving any jobs, we will make sure this queue is not paused and
            // if it is we will just pause this worker for a given amount of time and
            // make sure we do not need to kill this worker process off completely.
            if (!$this->daemonShouldRun($options, $connectionName, $queue)) {
                $status = $this->pauseWorker($options, $lastRestart);

                if (!is_null($status)) {
                    return $this->stop($status);
                }

                continue;
            }

            if ($this->resetScope !== null) {
                ($this->resetScope)();
            }

            // First, we will attempt to get the next job off of the queue. We will also
            // register the timeout handler and reset the alarm for this job so it is
            // not stuck in a frozen state forever. Then, we can fire off this job.
            $job = $this->getNextJob(
                $this->manager->connection($connectionName),
                $queue
            );

            if ($supportsAsyncSignals) {
                $this->registerTimeoutHandler($job, $options);
            }

            // If the daemon should run (not in maintenance mode, etc.), then we can run
            // fire off this job for processing. Otherwise, we will need to sleep the
            // worker so no more jobs are processed until they should be processed.
            if ($job) {
                $jobsProcessed++;

                // MY ADDED CODE
                $this->current_job = $job;
                // END MY ADDED CODE

                $this->runJob($job, $connectionName, $options);

                if ($options->rest > 0) {
                    $this->sleep($options->rest);
                }
            } else {
                // MY ADDED CODE
                // Remove the queue we just processed from the
                // queue list as there are no more jobs to process
                $this->queues = array_slice($this->queues, 1);
                // END MY ADDED CODE

                $this->sleep($options->sleep);
            }

            if ($supportsAsyncSignals) {
                $this->resetTimeoutHandler();
            }

            // Finally, we will check to see if we have exceeded our memory limits or if
            // the queue should restart based on other indications. If so, we'll stop
            // this worker and let whatever is "monitoring" it restart the process.
            $status = $this->stopIfNecessary(
                $options,
                $lastRestart,
                $startTime,
                $jobsProcessed,
                $job
            );

            // MY ADDED CODE "&& empty($this->queues)"
            // In addition to $status logic, stop the
            // daemon if there are no more queues to process
            if (!is_null($status) && empty($this->queues)) {
                return $this->stop($status);
            }
        }

        // MY ADDED CODE
        return null;
        // END MY ADDED CODE
    }

    /**
     *  Copied to override the parent Illuminate\Queue\Worker::runNextJob method
     *
     * Process the next job on the queue.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function runNextJob($connectionName = 'default', $queue = 'default', WorkerOptions $options = null)
    {
        // MY ADDED CODE
        if (!$options instanceof \Illuminate\Queue\WorkerOptions) {
            $this->default_options['timeout'] = $this->calculatorDefaultTimeout();

            $options = array_merge($this->default_options, $this->options);

            $options = new WorkerOptions(
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
        // END MY ADDED CODE

        $job = $this->getNextJob(
            $this->manager->connection($connectionName), $queue
        );

        // If we're able to pull a job off of the stack, we will process it and then return
        // from this method. If there is no job on the queue, we will "sleep" the worker
        // for the specified number of seconds, then keep processing jobs after sleep.
        if ($job) {
            return $this->runJob($job, $connectionName, $options);
        }

        $this->sleep($options->sleep);

        // MY ADDED CODE
        return null;
        // END MY ADDED CODE
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

    protected function handleExceptionDatabaseLogging(\Throwable $e)
    {
        global $wpdb;

        $table_name = $wpdb->prefix.'waffle_queue_logs';

        $this->app->get('db')->table($table_name)->insert([
            'queue' => $this->current_queue,
            'payload' => $this->current_job ? $this->current_job->getRawBody() : null,
            'exception' => $e->getMessage(),
        ]);
    }
}
