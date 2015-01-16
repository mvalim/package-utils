<?php namespace Mvalim\Workbench;

use Illuminate\Contracts\Foundation\Application;
use Mvalim\Workbench\Console\Publish;
use Mvalim\Workbench\Exceptions\PackageNotDefinedException;

class Container implements ContainerInterface {

	protected $packages = [];

	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * @var Publish
	 */
	protected $command;

	public function __construct(Application $app)
	{
		$this->app = $app;

		$this->app->bindShared('workbench.publish', function ()
		{
			return new Publish();
		});

		$commands = [
			'workbench.publish'
		];

		$events = $this->app['events'];
		$events->listen('artisan.start', function ($artisan) use ($commands)
		{
			$artisan->resolveCommands($commands);
		});
	}

	/**
	 * Add a package instance to the container
	 * @param string $name
	 * @param string $namespace
	 * @return Package
	 */
	public function addPackage($name, $namespace = null)
	{
		return $this->packages[ $name ] = new Package($name, $namespace);
	}

	/**
	 * Return a given package instance
	 *
	 * @param $name
	 * @return Package
	 * @throws PackageNotDefinedException
	 */
	public function package($name)
	{
		if( ! isset($this->packages[ $name ]))
		{
			throw new PackageNotDefinedException("$name is not defined");
		}
		return $this->packages[ $name ];
	}

	/**
	 * Return an array containing all packages instances
	 *
	 * @return array
	 */
	public function getPackages()
	{
		return $this->packages;
	}

	/**
	 * Add the command instance to the container
	 *
	 * @param $command
	 */
	public function setCommand($command)
	{
		$this->command = $command;
	}

	/**
	 * Get the command instance
	 *
	 * @return Publish
	 */
	public function command()
	{
		return $this->command;
	}

}