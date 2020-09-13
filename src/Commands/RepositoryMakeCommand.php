<?php


namespace ElegantMedia\SimpleRepository\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends \Illuminate\Console\GeneratorCommand
{


	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:repository';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new repository class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Repository';

	/**
	 * @inheritDoc
	 */
	protected function getStub()
	{
		return __DIR__.DIRECTORY_SEPARATOR."stubs".DIRECTORY_SEPARATOR.'Repository.php.stub';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		$dir = $this->option('dir');

		if ($this->option('group')) {
			return $rootNamespace."\\{$dir}\\".$this->getEntityPlural();
		}

		return $rootNamespace."\\{$dir}";
	}

	protected function getPath($name)
	{
		$path = [$this->option('dir')];

		if ($this->option('group')) {
			$path[] = $this->getEntityPlural();
		}

		$path[] = $this->getEntityPlural() . 'Repository.php';
		$path = app_path(implode(DIRECTORY_SEPARATOR, $path));

		return str_replace('\\', DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * Replace the class name for the given stub.
	 *
	 * @param  string  $stub
	 * @param  string  $name
	 * @return string
	 */
	protected function replaceClass($stub, $name)
	{
		$name = Str::pluralStudly($name);

		if (stripos($name, 'repo') === false) {
			$name .= 'Repository';
		}

		return parent::replaceClass($stub, $name);
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
		$stub = $this->files->get($this->getStub());

		$stub = $this->replaceModelClass($stub);

		return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
	}

	protected function replaceModelClass($stub)
	{
		if ($this->option('model')) {
			$replace = $this->option('model');
		} else {
			$replace = Str::studly(Str::singular($this->getNameInput()));
		}

		return str_replace(['ModelClass'], $replace, $stub);
	}

	protected function getEntityPlural()
	{
		return Str::pluralStudly($this->getNameInput());
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['force', null, InputOption::VALUE_NONE, 'Create the class even if it already exists'],
			['group', null, InputOption::VALUE_NONE, 'Create the repository in an autogenerated folder'],
			['dir', null, InputOption::VALUE_REQUIRED, 'Directory to create the models.', 'Models'],
			['model', null, InputOption::VALUE_OPTIONAL, 'Related model class'],
		];
	}
}
