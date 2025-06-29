<?php

namespace BoxyBird\Waffle;

class Scheduler
{
    protected static array $jobs = [];

    protected static bool $schedules_registered = false;

    protected string $hook;

    public function __construct()
    {
        if (!self::$schedules_registered) {
            add_filter('cron_schedules', [__CLASS__, 'registerCustomSchedules']);

            self::$schedules_registered = true;
        }
    }

    public static function registerCustomSchedules($schedules): array
    {
        if (!isset($schedules['every_minute'])) {
            $schedules['every_minute'] = [
                'interval' => 60,
                'display' => 'Every Minute',
            ];
        }

        if (!isset($schedules['every_five'])) {
            $schedules['every_five'] = [
                'interval' => 300,
                'display' => 'Every 5 Minutes',
            ];
        }

        if (!isset($schedules['every_fifteen'])) {
            $schedules['every_fifteen'] = [
                'interval' => 900,
                'display' => 'Every 15 Minutes',
            ];
        }

        if (!isset($schedules['every_thirty'])) {
            $schedules['every_thirty'] = [
                'interval' => 1800,
                'display' => 'Every 30 Minutes',
            ];
        }

        return $schedules;
    }

    public function call(callable $callback): self
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        if (!isset($backtrace[0]['file'], $backtrace[0]['line'])) {
            throw new \Exception('Could not determine a unique ID for the scheduled job.');
        }

        $caller = $backtrace[0];

        $this->hook = 'waffle_schedule_'.md5($caller['file'].':'.$caller['line']);

        self::$jobs[$this->hook] = $callback;

        add_action($this->hook, [__CLASS__, 'runJob']);

        return $this;
    }

    /**
     * Run the scheduled task immediately.
     * This bypasses the WordPress cron system and executes the code now.
     * Intended for testing.
     */
    public function now(): void
    {
        if (isset(self::$jobs[$this->hook]) && is_callable(self::$jobs[$this->hook])) {
            // Directly invoke the stored callable
            call_user_func(self::$jobs[$this->hook]);
        }
    }

    public function everyMinute(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'every_minute', $this->hook);
        }
    }

    public function everyFiveMinutes(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'every_five', $this->hook);
        }
    }

    public function everyFifteenMinutes(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'every_fifteen', $this->hook);
        }
    }

    public function everyThirtyMinutes(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'every_thirty', $this->hook);
        }
    }

    public function hourly(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'hourly', $this->hook);
        }
    }

    public function twiceDaily(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'twiceDaily', $this->hook);
        }
    }

    public function daily(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'daily', $this->hook);
        }
    }

    public function weekly(): void
    {
        if (!wp_next_scheduled($this->hook)) {
            wp_schedule_event(time(), 'weekly', $this->hook);
        }
    }

    public static function runJob(...$args): void
    {
        $hook = current_filter();

        if (isset(self::$jobs[$hook]) && is_callable(self::$jobs[$hook])) {
            call_user_func_array(self::$jobs[$hook], $args);
        }
    }
}