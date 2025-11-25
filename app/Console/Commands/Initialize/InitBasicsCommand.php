<?php

namespace App\Console\Commands\Initialize;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitBasicsCommand extends Command
{
    protected $signature = 'init:structure ';

    protected $description = 'This command will initialize the basics in the structure';

    protected $commands = [

        "make:base-model",
        "make:base-auth-model",

        "make:default-controller",
        "make:father-crud-controller",

        "make:basic-resource",
        "make:basic-request",

        "make:model-columns-service",
        "make:basic-crud-service",

    ];
    public function handle()
    {
        foreach($this->commands as $command)
         $this->call($command);
    }
}
