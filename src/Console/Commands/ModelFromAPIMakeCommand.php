<?php

namespace Madeweb\Eloquent\API\Console\Commands;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ModelFromAPIMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:model-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model from api class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     * @throws Exception
     */
    protected function getStub()
    {
        if($this->hasOption('auth')){
            $stub = '/stubs/model.api-auth.stub';
        }elseif ($this->option('api')) {
            $stub = '/stubs/model.api.stub';
        }else{
            throw new Exception('Is required define a name of api class');
        }
        return __DIR__.$stub;
    }


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Models';
    }

    protected function qualifyClass($name)
    {
        $name = ucwords($name);
        $name = ltrim($name, '\\/');
        $rootNamespace = $this->rootNamespace();
        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }
        $name = str_replace('/', '\\', $name);
        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }


    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');
            return false;
        }
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type.' created successfully.');
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base service import if we are already in base namespace.
     *
     * @param  string $name
     * @return string
     * @throws FileNotFoundException
     */
    protected function buildClass($name)
    {
        $replace = [];
        if ($this->option('api')) {
            $replace = $this->buildAPIReplacements($replace);
        }
        if ($this->hasOption('auth')) {
            $replace = $this->buildAuthReplacements($replace);
        }
        $replace['DummyModelClass'] = class_basename($name);
        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * @param array $replace
     * @return array
     */
    protected function buildAuthReplacements(array $replace)
    {
        return array_merge($replace, [
            'DummyFullTraitAuth' => 'Madeweb\Eloquent\API\Models\Traits\AuthenticatableTrait',
            'DummyTraitAuthClass' => 'AuthenticatableTrait',
        ]);
    }

    /**
     * @param array $replace
     * @return array
     */
    protected function buildAPIReplacements(array $replace)
    {
        $apiClass = $this->parseModel($this->option('api'));
        return array_merge($replace, [
            'DummyFullAPIClass' => $apiClass,
            'DummyAPIClass' => class_basename($apiClass),
            'DummyAPIVariable' => lcfirst(class_basename($apiClass)),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }
        $model = trim(str_replace('/', '\\', $model), '\\');
        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.$model;
        }
        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['api', 'a', InputOption::VALUE_REQUIRED, 'Generate a resource model for the given api.'],
            ['auth', 'l', InputOption::VALUE_OPTIONAL, 'Add authentication for passport']
        ];
    }
}
