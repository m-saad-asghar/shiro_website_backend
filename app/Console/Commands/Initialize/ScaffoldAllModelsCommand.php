<?php

namespace App\Console\Commands\Initialize;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ScaffoldAllModelsCommand extends Command
{
    protected $signature = 'scaffold:all-models
    {--r|resource : Also scaffold the API Resource}';

    protected $description = 'Run scaffold:model-schema and scaffold:request-schema for every model in app/Models (excl. BaseModel)';

    public function handle(Filesystem $files)
    {
        $dir = app_path('Models');

        if (! $files->isDirectory($dir)) {
            return $this->error("Directory not found: {$dir}");
        }

        // Scan for all PHP files in app/Models
        $models = collect($files->files($dir))
            ->map->getFilename()                 // e.g. User.php
            ->filter(fn($name) => $name !== 'BaseModel.php' && $name !== 'BaseAuthModel.php' )
            ->map(fn($name) => pathinfo($name, PATHINFO_FILENAME)) // e.g. User
            ->toArray();

        if (empty($models)) {
            return $this->info('No models found in app/Models');
        }

        $this->info('Found models: ' . implode(', ', $models));

        foreach ($models as $model) {
            $class = $model;
            $params = [
                'model' => $class,
            ];

            $this->info("— Scaffolding model: {$class}");

            // 1) scaffold:model-schema
            $this->call('scaffold:model-schema', $params);

            // 2) scaffold:request-schema
            $this->call('scaffold:request-schema', $params);

            // 3) (optional) scaffold:resource-schema
            if ($this->option('resource')) {

                $this->info("— Scaffolding resource for: {$class}");
                $this->call('scaffold:resource-schema',$params);

            }


            $this->info("✔ Done: {$class}\n");
        }

        $this->info('All models have been scaffolded.');
        return 0;
    }
}
