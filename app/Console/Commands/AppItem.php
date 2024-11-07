<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Str;

class AppItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:item {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Item files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class = $this->argument('name');
        if (! preg_match('/\\\\/', $class)) {
            if (! $this->confirm('Did you remember to use a double backslash (\\\\)?', true)) {
                return self::FAILURE;
            }
        }
        // class name
        $classParts = explode('\\', $class);
        $className = class_basename($class);
        $classPath = implode('/', $classParts);

        // migration
        $table = Str::snake(Str::pluralStudly($className));
        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);

        $modelNamespace = $this->getNamespace('Models', $classParts);

        // model
        $this->generate(
            base_path('stubs/ItemModel.stub'),
            app_path("Models/$classPath.php"),
            [
                'DummyClass' => $className,
                'DummyNamespace' => $modelNamespace,
            ],
        );

        // service
        $this->generate(
            base_path('stubs/ItemService.stub'),
            app_path("Services/{$classPath}Service.php"),
            [
                'DummyClass' => $className.'Service',
                'DummyNamespace' => $this->getNamespace('Services', $classParts),
            ],
        );

        // controller
        $controllerClass = $className.'Controller';
        $controllerNamespace = $this->getNamespace('Http\\Controllers', $classParts);
        $this->generate(
            base_path('stubs/ItemController.stub'),
            app_path("Http/Controllers/{$classPath}Controller.php"),
            [
                'DummyClass' => $controllerClass,
                'DummyNamespace' => $controllerNamespace,
            ],
        );

        // resource
        $this->generate(
            base_path('stubs/ItemResource.stub'),
            app_path("Http/Resources/{$classPath}Resource.php"),
            [
                'DummyClass' => $className.'Resource',
                'DummyNamespace' => $this->getNamespace('Http\\Resources', $classParts),
            ],
        );

        // store update request
        $this->generate(
            base_path('stubs/ItemStoreUpdateRequest.stub'),
            app_path("Http/Requests/{$classPath}StoreUpdateRequest.php"),
            [
                'DummyClass' => $className.'StoreUpdateRequest',
                'DummyNamespace' => $this->getNamespace('Http\\Requests', $classParts),
            ],
        );

        // factory
        $factoryParts = array_slice($classParts, 0, -1);
        $factoryPath = implode('/', $factoryParts);
        $this->mkdir(base_path("database/factories/$factoryPath"));
        $this->generate(
            base_path('stubs/ItemFactory.stub'),
            base_path("database/factories/{$classPath}Factory.php"),
            [
                'DummyModelClass' => $className,
                'DummyModelNamespace' => $modelNamespace,
                'DummyClass' => $className.'Factory',
                'DummyNamespace' => $this->getNamespace('Database\\Factories', $classParts, false, false),
            ],
        );

        // test
        $testParts = array_slice($classParts, 0, -1);
        $testPath = implode('/', $testParts);
        $this->mkdir(base_path("tests/Feature/$testPath"));
        $this->generate(
            base_path('stubs/ItemTest.stub'),
            base_path("tests/Feature/{$classPath}Test.php"),
            [
                'DummyModelClass' => $className,
                'DummyModelNamespace' => $modelNamespace,
                'DummyClass' => $className.'Test',
                'DummyNamespace' => $this->getNamespace('Tests\\Feature', $classParts, false, false),
                'dummy-route' => $table,
            ],
        );

        // routes
        $routeParts = array_slice($classParts, 0, -1);
        $routePath = implode('/', array_map(fn ($item) => Str::kebab($item), $routeParts));
        $this->mkdir(base_path("routes/api/$routePath"));
        $this->generate(
            base_path('stubs/routes.stub'),
            base_path("routes/api/$routePath/$table.php"),
            [
                'dummy-route' => $table,
                'dummy permission' => "manage $table",
                'DummyControllerClass' => $controllerClass,
                'DummyControllerNamespace' => $controllerNamespace,
            ],
        );
        $routesContent = file_get_contents('routes/api.php');
        if (! preg_match("/$routePath\/$table/", $routesContent)) {
            $routesContent .= "require __DIR__ . '/api/$routePath/$table.php';\n";
        }
        file_put_contents(base_path('routes/api.php'), $routesContent);
    }

    public function generate(string $stubPath, string $filePath, array $replacements)
    {
        $stubContent = file_get_contents($stubPath);
        $fileContent = str_replace(array_keys($replacements), $replacements, $stubContent);
        if (! file_exists($filePath)) {
            file_put_contents($filePath, $fileContent);
        }
    }

    public function mkdir($path)
    {
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    public function getNamespace(string $prefix, array $classParts, $mkdir = true, $app = true): string
    {
        $namespace = ($app ? 'App\\' : '').$prefix;
        $namespaceParts = array_slice($classParts, 0, -1);
        if (count($namespaceParts)) {
            $namespace .= '\\'.implode('\\', $namespaceParts);
        }
        $path = implode('/', $namespaceParts);
        $prefixParts = explode('\\', $prefix);
        $prefixPath = implode('/', $prefixParts);
        if ($mkdir) {
            $this->mkdir(app_path("$prefixPath/$path"));
        }

        return $namespace;
    }
}
