<?php

namespace Luova\Nagad;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NagadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/nagad.php' => config_path('nagad.php')
        ], 'nagad');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/nagad.php', 'nagad');
    }
}
