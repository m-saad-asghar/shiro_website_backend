<?php

namespace App\Console\Commands\CodeAfterMigrate;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ScaffoldModelSchemaCommand extends BaseScaffoldCommand
{
    protected $signature = 'scaffold:model-schema
        {model : Fully-qualified Model class e.g. App\\Models\\User}
        {--table=  : Optional schema table name}';

    protected $description = 'Generate or update Model extending BaseModel with fillable and casts';

    public function handle()
    {
        $modelClass = $this->handelModelScope($this->argument('model'));

        if(!$this->ensureModelExists($modelClass))
         return;

        $path = $this->getClassFilePath($modelClass);
        $this->updateModelBaseClass($path, $modelClass);

        $table   = $this->option('table') ?: (new $modelClass)->getTable();
        $columns = $this->getColumns($table);

        [$fillable, $casts] = $this->parseModelAttributes($columns);

        $this->injectModelProperties($path, $modelClass, $fillable, $casts);
        $this->info("Model scaffolding complete: {$path}");
    }
    protected function ensureModelExists(string $class) : bool
    {
        if (!class_exists($class)) {
            Artisan::call('make:model', ['name' => $class]);
            $this->error("The model {$class} not found but now created run the command again to fill it");
            return false;
        }

        return true;
    }

    protected function getClassFilePath(string $class): string
    {
        return (new \ReflectionClass($class))->getFileName();
    }

    protected function updateModelBaseClass(string $path, string $class)
    {
        $content = $this->files->get($path);

        // Case 1: Eloquent models
        if (preg_match('/extends\s+Model\b/', $content)) {
            // Swap the parent class
            $content = preg_replace(
                '/extends\s+Model\b/',
                'extends BaseModel',
                $content
            );

            // Inject import, if missing
            if (! Str::contains($content, 'use App\\Models\\BaseModel;')) {
                $content = preg_replace(
                    '/namespace\s+App\\\Models;/',
                    "namespace App\\Models;\n\nuse App\\Models\\BaseModel;",
                    $content
                );
            }
        }

        // Case 2: Authenticatable-based models (e.g. User)
        if (preg_match('/extends\s+Authenticatable\b/', $content)) {
            // Swap the parent class
            $content = preg_replace(
                '/extends\s+Authenticatable\b/',
                'extends BaseAuthModel',
                $content
            );

            // Inject import, if missing
            if (! Str::contains($content, 'use App\\Models\\BaseAuthModel;')) {
                $content = preg_replace(
                    '/namespace\s+App\\\Models;/',
                    "namespace App\\Models;\n\nuse App\\Models\\BaseAuthModel;",
                    $content
                );
            }
        }

        // Persist changes
        $this->files->put($path, $content);
    }

    protected function parseModelAttributes(array $columns): array
    {
        $fillable = [];
        $casts    = [];
        foreach ($columns as $col) {
            if (in_array($col->COLUMN_NAME, ['id','created_at','updated_at','deleted_at'], true)) {
                continue;
            }
            $fillable[] = $col->COLUMN_NAME;

            switch (strtolower($col->DATA_TYPE)) {
                case 'tinyint':
                    $casts[$col->COLUMN_NAME] = Str::contains($col->COLUMN_TYPE, '(1)')
                        ? 'boolean' : 'integer';
                    break;
                case 'int': case 'smallint': case 'mediumint': case 'bigint':
                $casts[$col->COLUMN_NAME] = 'integer'; break;
                case 'decimal': case 'float': case 'double':
                $casts[$col->COLUMN_NAME] = 'float'; break;
                case 'json':
                    $casts[$col->COLUMN_NAME] = 'array'; break;
                case 'date': case 'datetime': case 'timestamp':
                $casts[$col->COLUMN_NAME] = 'datetime'; break;
            }
        }
        return [$fillable, $casts];
    }

    protected function injectModelProperties(string $path, string $class, array $fillable, array $casts)
    {
        $content = $this->files->get($path);

        $fillableBlock = $this->formatArrayBlock('fillable', $fillable);
        $castsBlock    = $this->formatArrayBlock('casts', $casts);

        // 1) Attempt to replace existing $fillable
        $newContent = preg_replace(
            '/protected\s+\$fillable\s*=\s*\[.*?\];/s',
            $fillableBlock,
            $content,
            -1,
            $countFillable
        );

        // 2) If no replacement happened, inject after the class declaration
        if ($countFillable === 0) {
            $newContent = $this->injectAfterLastTraitUse($newContent, class_basename($class), $fillableBlock);
        }

        // 3) Attempt to replace existing $casts
        $finalContent = preg_replace(
            '/protected\s+\$casts\s*=\s*\[.*?\];/s',
            $castsBlock,
            $newContent,
            -1,
            $countCasts
        );

        // 4) If no replacement happened, inject after the $fillable block
        if ($countCasts === 0) {
            $finalContent = preg_replace(
                '/(protected\s+\$fillable\s*=\s*\[.*?\];)/s',
                "$1\n\n" . trim($castsBlock),
                $finalContent
            );
        }

        // 5) Persist
        $this->files->put($path, $finalContent);
        $this->info("✅ Updated or injected \$fillable & \$casts in: {$path}");
    }

    protected function injectAfterLastTraitUse(string $content, string $className, string $injectionBlock): string
    {
        return preg_replace_callback(
            '/(class\s+' . preg_quote($className) . '[^{]*\{)(.*?)(\n\s*(?:public|protected|private|function|\}))/s',
            function ($matches) use ($injectionBlock) {
                [$fullMatch, $classHead, $classBody, $insertionPoint] = $matches;

                // Look for all `use ...;` lines in the class body
                if (preg_match_all('/^\s*use\s+[^\n;]+;/m', $classBody, $uses, PREG_OFFSET_CAPTURE)) {
                    $lastUse = end($uses[0]);
                    $insertPos = $lastUse[1] + strlen($lastUse[0]);

                    $before = substr($classBody, 0, $insertPos);
                    $after = substr($classBody, $insertPos);

                    $classBody = $before . "\n\n" . trim($injectionBlock) . "\n" . $after;
                } else {
                    // No traits used; inject at the top of the class body
                    $classBody = trim($injectionBlock) . "\n" . $classBody;
                }

                return $classHead . $classBody . $insertionPoint;
            },
            $content
        );
    }


    protected function formatArrayBlock(string $key, array $items): string
    {
        $block = "protected \${$key} = [\n";
        foreach ($items as $k => $v) {
            $line = is_int($k)
                ? "        '{$v}' => '{$v}',\n"
                : "        '{$k}' => '{$v}',\n";
            $block .= $line;
        }
        $block .= "    ];";
        return $block;
    }
}
