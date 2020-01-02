<?php

namespace Madeweb\Eloquent\API;

use Illuminate\Support\ServiceProvider;
use Madeweb\Eloquent\API\Console\Commands\ModelFromAPIMakeCommand;

class EloquentAPIServiceProvider extends ServiceProvider
{
    protected $commands = [
        ModelFromAPIMakeCommand::class
    ];

    public function boot()
    {
        $this->commands($this->commands);
    }

    public function register()
    {

    }
}
