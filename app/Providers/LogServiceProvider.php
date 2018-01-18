<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class LogServiceProvider extends ServiceProvider {
    /**
     * Configure logging on boot.
     *
     * @return void
     */
    public function boot() {
    }

    /**
     * Register the log service.
     *
     * @return void
     */
    public function register() {
    }
}