<?php

namespace BoxyBird\Waffle;

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
        'timeout'       => 60, // server timeout/daemon timeout?
        'sleep'         => 0,
        'maxTries'      => 1,
        'force'         => false,
        'stopWhenEmpty' => true,
        'maxJobs'       => 0,
        'maxTime'       => 60, // per job timeout?
        'rest'          => 0,
    ];

    public function __construct(string $queue, App $app)
    {
        $this->app = $app;
        $this->queue = $queue;
        $this->worker = $app->get('queue.worker');

        add_filter('cron_schedules', [$this, 'addSchedule']);
    }

    public function work()
    {
        add_action('waffle_worker_daemon', [$this, 'daemon']);
        
        if (!wp_next_scheduled('waffle_worker_daemon')) {
            wp_schedule_event(time(), 'waffle_worker_daemon', 'waffle_worker_daemon');
        }
    }
    
    public function addSchedule($schedules)
    {
        $schedules['waffle_worker_daemon'] = [
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
        $options = array_merge($this->default_options, $this->options);

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

        // You made it decision. Don't blame me.
        if ($options['timeout'] === 0) {
            set_time_limit(0);
        }

        try {
            $this->worker->daemon('default', $this->queue, $worker_options);
        } catch (MaxAttemptsExceededException $e) {
            $this->handleMaxAttemptsExceededException($e);
        }
    }

    protected function handleMaxAttemptsExceededException(MaxAttemptsExceededException $e)
    {
        $db = $this->app->get('db');

        $db->table('waffle_queue_logs')->insert([
            'queue'      => $this->queue,
            'exception'  => $e->getMessage(),
        ]);
    }
}
