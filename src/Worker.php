<?php

namespace BoxyBird\Waffle;

use Exception;
use BoxyBird\Waffle\App;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Queue\MaxAttemptsExceededException;

class Worker
{
    protected $app;

    protected $queue;

    protected $worker;

    protected $options = [];

    protected $default_options = [
        'name'          => 'default',
        'backoff'       => 0,
        'memory'        => 128,
        'sleep'         => 0,
        'maxTries'      => 1,
        'force'         => false,
        'stopWhenEmpty' => true,
        'maxJobs'       => 0,
        'rest'          => 0,
        'maxTime'       => 60, // per job timeout
        // 'timeout'    => Default valued handled by method $this->calculatorDefaultTimeout()
    ];

    public function __construct(string $queue, App $app)
    {
        $this->app = $app;
        $this->queue = $queue;
        $this->worker = $app->get('queue.worker');

        add_filter('cron_schedules', [$this, 'addSchedule']);
        add_action('waffle_worker_daemon', [$this, 'daemon']);
    }

    public function work()
    {
        if (wp_next_scheduled('waffle_worker_daemon')) {
            return;
        }

        wp_schedule_event(time(), 'waffle_worker_daemon_schedule', 'waffle_worker_daemon');
    }
    
    public function addSchedule($schedules)
    {
        $schedules['waffle_worker_daemon_schedule'] = [
            'interval' => 60,
            'display'  => 'Every Minute',
        ];

        return $schedules;
    }

    public function setOptions(array $options = []): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @see Illuminate\Queue\Worker::daemon
     */
    public function daemon(): void
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
            $this->worker->daemon('default', $this->queue, $worker_options);
        } catch (MaxAttemptsExceededException $e) {
            $this->handleMaxAttemptsExceededException($e);
        } catch (Exception $e) {
            throw $e;
        }
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

    protected function handleMaxAttemptsExceededException(MaxAttemptsExceededException $e)
    {
        $this->app->get('db')->table('waffle_queue_logs')->insert([
            'queue'      => $this->queue,
            'exception'  => $e->getMessage(),
        ]);
    }
}
