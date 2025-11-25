<?php

namespace App\Console\Commands\CodeBasics;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateFatherCrudControllerCommand extends Command
{
    protected $signature = 'make:father-crud-controller';
    protected $description = 'Generate the FatherCrudController in app/Http/Controllers';

    public function handle(Filesystem $files)
    {
        $directory = app_path('Http/Controllers');
        if (! $files->isDirectory($directory)) {
            $files->makeDirectory($directory, 0755, true);
            $this->info("Created directory: {$directory}");
        }

        $filePath = $directory . '/FatherCrudController.php';
        if ($files->exists($filePath)) {
            $this->info("Overwriting existing Controller at: {$filePath}");
        }

        $stub = <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Priest\PriestRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Traits\GeneralTrait;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class FatherCrudController extends Controller
{
    protected $key;
    protected $service;

    protected $createRequest;
    protected $updateRequest;

    public function __construct(){

        $this->setVariables();

    }

    protected abstract function setVariables() : void;

    public function allPaginated(Request $request){

        try{

            return $this->apiResponse(
                $this->service::allPaginated($request)
            );

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }

    }

    public function pureAll(Request $request)
    {
        try{

            $data[Str::plural($this->key)] = $this->service::pureAll($request);
            return $this->apiResponse(
                $data
            );

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }
    }

    public function all(Request $request){

        try{

            $data[Str::plural($this->key)] = $this->service::all($request);
            return $this->apiResponse(
                $data
            );

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }

    }

    public function show(Request $request){

        try{

            $data[$this->key] = $this->service::show($request);
            return $this->apiResponse($data);

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }

    }


    public function store() {

        try{

            $request = app($this->createRequest);
            $data[$this->key] = $this->service::create($request);

            return $this->apiResponse($data);

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }

    }

    public function update() {

        try{

            $request = app($this->updateRequest);
            $data[$this->key] = $this->service::update($request);

            return $this->apiResponse($data);

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }

    }

    public function activate(Request $request) {

        try{

            $data["message"] = ($this->service::activate($request))? "activation done"
                : "there was an error with activate data";
            return $this->apiResponse($data);

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }

    }

    public function deactivate(Request $request) {

        try{

            $data["message"] = ($this->service::deactivate($request))? "deactivation done"
                : "there was an error with deactivate data";
            return $this->apiResponse($data);

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }

    }

    public function destroy(Request $request)
    {
        try{

            $data["message"] = ($this->service::delete($request))? "deletion done"
                : "there was an error with delete data";
            return $this->apiResponse($data);

        }catch(\Exception $e)
        {
            return $this->handleException($e);
        }
    }

    public function exportExcel(Request $request)
    {
        try {

            $data["file"] = $this->service::exportExcel($request);
            return $this->apiResponse($data);

        }catch (\Exception $e)
        {
            return $this->handleException($e);
        }
    }

    public function importExcel(Request $request)
    {
        try {

            $data["message"] = ($this->service::importExcel($request))? "import done"
                : "there was an error with import data";
            return $this->apiResponse($data);

        }catch (\Exception $e)
        {
            return $this->handleException($e);
        }
    }

}
PHP;

        $files->put($filePath, $stub);
        $this->info("Generated FatherCrudController at: {$filePath}");
        return 0;
    }
}
