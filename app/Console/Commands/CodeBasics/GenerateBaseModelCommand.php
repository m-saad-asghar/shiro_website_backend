<?php

namespace App\Console\Commands\CodeBasics;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateBaseModelCommand extends Command
{
    protected $signature = 'make:base-model';
    protected $description = 'Generate the BasicRequest in app/Models';

    public function handle(Filesystem $files)
    {
        $directory = app_path('Models');
        if (! $files->isDirectory($directory)) {
            $files->makeDirectory($directory, 0755, true);
            $this->info("Created directory: {$directory}");
        }

        $filePath = $directory . '/BaseModel.php';

        if ($files->exists($filePath)) {
            $this->info("Overwriting existing Controller at: {$filePath}");
        }

        $stub = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use HasFactory,SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $search = [];
    protected $excel = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public function scopeWithFilters($query)
    {
        $request = request();
        return $query->when($request->filled("search"),function ($query) use ($request){

            $useOr = false;
            foreach ($this->search as $search)
            {

                if(!$useOr) {

                    $query->where($search,"like",'%' . $request->search . '%');
                    $useOr = true;

                }
                else
                    $query->orWhere($search,"like",'%' . $request->search . '%');

            }

        })->when($request->filled("status"),function ($query) use ($request) {

            $query->where("status",$request->status);

        });
    }
}
PHP;

        $files->put($filePath, $stub);
        $this->info("Generated BaseModel at: {$filePath}");
        return 0;
    }
}
