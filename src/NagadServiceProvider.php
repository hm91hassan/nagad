<?php

namespace Luova\Nagad;


use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class NagadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/nagad.php' => config_path('nagad.php')
        ], 'nagad');

        AliasLoader::getInstance()->alias('LNagad', 'Luova\Nagad\Traits\LNagad');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/nagad.php', 'nagad');
    }
}
