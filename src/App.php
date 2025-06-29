<?php

namespace BoxyBird\Waffle;

use Illuminate\Container\Container;

class App extends Container
{
    /**
     * @see \Illuminate\Contracts\Foundation\Application::isDownForMaintenance()
     */
    public function isDownForMaintenance(): bool
    {
        return false;
    }

    /**
     * @param string|string[] $environments
     *
     * @see \Illuminate\Contracts\Foundation\Application::environment()
     */
    public function environment(...$environments): string|bool
    {
        if (empty($environments)) {
            return 'waffle';
        }

        return in_array(
            'waffle',
            is_array($environments[0]) ? $environments[0] : $environments
        );
    }

    /**
     * @see \Illuminate\Contracts\Foundation\Application::getNamespace()
     */
    public function getNamespace(): string
    {
        return 'BoxyBird\\Waffle\\';
    }
}
