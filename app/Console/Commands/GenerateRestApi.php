<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRestApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:restapi {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Rest Api Resource {resource name} { module: api, admin}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resource_name = $this->argument('name');
        $module        = $this->argument('module');
        if( $module == 'api' )
            $this->generateRestApiResource($resource_name);
        else
            $this->generateAdminCrudResource($resource_name);

        $this->info('resource generated successfully');
    }

    public function generateRestApiResource($module_name)
    {
        $this->generateModel($module_name);
        $this->generateApiHook($module_name);
        $this->generateApiController($module_name);
        $this->generateApiCollection($module_name);
    }

    public function generateAdminCrudResource($module_name)
    {
        $this->generateModel($module_name);
        $this->generateAdminHook($module_name);
        $this->generateAdminController($module_name);
        $this->generateCrudFile($module_name);
    }

    public function generateModel($module_name)
    {
        $filename  = Str::singular($module_name);
        if( !file_exists( base_path('app/Models/' . $filename . '.php' ) ) ){
            $modelFile = file_get_contents(public_path('stuff/SampleModel.stuff'));
            $wildCards = ['[SampleModel]','[table_name]','[table_primary_key]'];
            $replacer  = [$filename,$module_name,'id'];
            $modelFile = str_replace($wildCards,$replacer,$modelFile);
            file_put_contents(base_path('app/Models/' . $filename . '.php'),$modelFile);
        }
    }

    public function generateApiHook($module_name)
    {
        $filename  = Str::singular($module_name) . 'Hook';
        if( !file_exists( base_path('app/Models/Hooks/Api/' . $filename . '.php' ) ) ){
            $hookFile = file_get_contents(public_path('stuff/api/SampleHook.stuff'));
            $wildCards = ['[SampleHook]'];
            $replacer  = [$filename];
            $hookFile = str_replace($wildCards,$replacer,$hookFile);
            file_put_contents(base_path('app/Models/Hooks/Api/' . $filename . '.php'),$hookFile);
        }
    }

    public function generateAdminHook($module_name)
    {
        $filename  = Str::singular($module_name) . 'Hook';
        if( !file_exists( base_path('app/Models/Hooks/Admin/' . $filename . '.php' ) ) ){
            $hookFile = file_get_contents(public_path('stuff/admin/SampleHook.stuff'));
            $wildCards = ['[SampleHook]'];
            $replacer  = [$filename];
            $hookFile = str_replace($wildCards,$replacer,$hookFile);
            file_put_contents(base_path('app/Models/Hooks/Admin/' . $filename . '.php'),$hookFile);
        }
    }

    public function generateApiController($module_name)
    {
        $model_name      = Str::singular($module_name);
        $controller_name = $module_name . 'Controller.php';
        if( !file_exists( base_path('app/Http/Controllers/Api/' . $controller_name ) ) ){
            $controllerFile  = file_get_contents(public_path('stuff/api/SampleRestController.stuff'));
            $wildCards       = ['[SampleRestController]','[Model]','[Resource]'];
            $replacer        = [$module_name . 'Controller',$model_name,$model_name];
            $controllerFile  = str_replace($wildCards,$replacer,$controllerFile);
            file_put_contents(base_path('app/Http/Controllers/Api/' . $controller_name),$controllerFile);
        }
    }

    public function generateAdminController($module_name)
    {
        $model_name       = Str::singular($module_name);
        $controller_name  = $module_name . 'Controller.php';
        if( !file_exists( base_path('app/Http/Controllers/Admin/' . $controller_name ) ) ){
            $view_folder_name = Str::kebab($module_name);
            $page_title       = Str::title( str_replace('-',' ',$view_folder_name) );
            $controllerFile   = file_get_contents(public_path('stuff/admin/SampleCrudController.stuff'));
            $wildCards        = ['[SampleCrudController]','[Model]','[Page Title]','[folder_name]'];
            $replacer         = [$module_name . 'Controller',$model_name,$page_title,$view_folder_name];
            $controllerFile   = str_replace($wildCards,$replacer,$controllerFile);
            file_put_contents(base_path('app/Http/Controllers/Admin/' . $controller_name),$controllerFile);
        }
    }

    public function generateApiCollection($module_name)
    {
        $model_name      = Str::singular($module_name);
        $collection_name = $model_name . '.php';
        if( !file_exists( base_path('app/Http/Resources/' . $collection_name ) ) ){
            $collectionFile  = file_get_contents(public_path('stuff/api/SampleResource.stuff'));
            $wildCards       = ['[SampleResource]'];
            $replacer        = [$module_name];
            $collectionFile  = str_replace($wildCards,$replacer,$collectionFile);
            file_put_contents(base_path('app/Http/Resources/' . $collection_name),$collectionFile);
        }
    }

    public function generateCrudFile($module_name)
    {
        $view_folder_name = Str::kebab($module_name);
        $dir_path = resource_path('views/admin/' . $view_folder_name);
        if( !is_dir( $dir_path ) ){
            mkdir($dir_path);
            $source_path = public_path('stuff/admin');
            copy($source_path . '/add.blade.php',$dir_path . '/add.blade.php');
            copy($source_path . '/edit.blade.php',$dir_path . '/edit.blade.php');
            copy($source_path . '/index.blade.php',$dir_path . '/index.blade.php');
            copy($source_path . '/detail.blade.php',$dir_path . '/detail.blade.php');
        }
    }
}
