<?php

namespace BoxyBird\Waffle;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;

class App extends Container implements Application
{
    /**
     * The base path for the application.
     */
    protected string $basePath;

    /**
     * The current locale.
     */
    protected string $locale = 'en';

    /**
     * Indicates if the application has been bootstrapped before.
     */
    protected bool $hasBeenBootstrapped = false;

    /**
     * @see \Illuminate\Contracts\Foundation\Application::isDownForMaintenance()
     */
    public function isDownForMaintenance(): bool
    {
        return false;
    }

    /**
     * @param  string|string[]  $environments
     *
     * @see \Illuminate\Contracts\Foundation\Application::environment()
     */
    public function environment(...$environments): string|bool
    {
        if ($environments === []) {
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

    public function version(): string
    {
        return '1.0.0';
    }

    public function basePath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function bootstrapPath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function configPath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function databasePath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function langPath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function publicPath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function resourcePath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function storagePath($path = ''): string
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
        return '';
    }

    public function runningInConsole(): bool
    {
        return in_array(php_sapi_name(), ['cli', 'phpdbg']);
    }

    public function runningUnitTests(): bool
    {
        return defined('PHPUNIT_RUNNING');
    }

    public function hasDebugModeEnabled(): bool
    {
        return false;
    }

    public function maintenanceMode(): null
    {
        return null;
    }

    public function registerConfiguredProviders(): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }

    public function register($provider, $force = false): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT

        // if (is_string($provider)) {
        // $provider = $this->resolveProvider($provider);
        // }

        // $provider->register();

        // return $provider;
    }

    public function registerDeferredProvider($provider, $service = null): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }

    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    public function boot(): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }

    public function booting($callback): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }

    public function booted($callback): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }

    public function bootstrapWith(array $bootstrappers): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT

        // $this->hasBeenBootstrapped = true;
        // foreach ($bootstrappers as $bootstrapper) {
        // $this->make($bootstrapper)->bootstrap($this);
        // }
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getProviders($provider): array
    {
        return [];
    }

    public function hasBeenBootstrapped(): bool
    {
        return $this->hasBeenBootstrapped;
    }

    public function loadDeferredProviders(): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }

    public function setLocale($locale): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT

        // $this->locale = $locale;
    }

    public function shouldSkipMiddleware(): bool
    {
        return false;
    }

    public function terminating($callback): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }

    public function terminate(): void
    {
        // STUB ONLY AS WAFFLE PACKAGE DOES NOT CURRENT USE IT
    }
}