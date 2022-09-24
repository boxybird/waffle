<?php

namespace BoxyBird\Waffle;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Queue\DatabaseQueue as IlluminateDatabaseQueue;

class DatabaseQueue extends IlluminateDatabaseQueue
{
    /**
    * Create a typical, string based queue payload array.
    *
    * @param  string  $job
    * @param  string  $queue
    * @param  mixed  $data
    * @return array
    */
    protected function createStringPayload($job, $queue, $data)
    {
        $properties = $this->extractPropertiesFromClassString($job);

        return $this->withCreatePayloadHooks($queue, [
            'uuid'          => (string) Str::uuid(),
            'displayName'   => is_string($job) ? explode('@', $job)[0] : null,
            'job'           => $job,
            'maxTries'      => $properties['tries'] ?? null,
            'maxExceptions' => $properties['maxExceptions'] ?? null,
            'failOnTimeout' => $properties['failOnTimeout'] ?? false,
            'backoff'       => $properties['backoff'] ?? null,
            'timeout'       => $properties['timeout'] ?? null,
            'data'          => $data,
        ]);
    }

    protected function extractPropertiesFromClassString(string $class): array
    {
        $properties = [];

        if (class_exists($class)) {
            $reflection = new ReflectionClass($class);

            $properties = $reflection->getDefaultProperties();
        }

        return $properties;
    }
}
