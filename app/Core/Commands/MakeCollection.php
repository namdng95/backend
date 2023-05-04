<?php

namespace App\Core\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeCollection extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:collection {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new resource\'s collection';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/collection.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Resources';
    }
}
