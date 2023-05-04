<?php

namespace App\Core\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeException extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:exception {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new exception';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/exception.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Exceptions';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $content = parent::buildClass($name);

        return $this->replacePrefix($name, $content);
    }

    /**
     * Replace the prefix from content
     *
     * @param string $name
     * @param string $content
     * @return string
     */
    protected function replacePrefix($name, $content)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        $prefix = Str::snake(str_replace(['Exception', 'exception'], '', $class));

        return str_replace('DummyPrefix', $prefix, $content);
    }
}
