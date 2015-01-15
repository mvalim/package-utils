<?php namespace Mvalim\Workbench;

use Illuminate\Contracts\Foundation\Application;

interface ContainerInterface {
	public function __construct(Application $app);

	/**
	 * Add a package to the container
	 *
	 * @param  string $name
	 * @param string $namespace
	 * @return mixed
	 */
	public function addPackage($name, $namespace = null);
}