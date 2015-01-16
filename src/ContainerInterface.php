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

	/**
	 * Return a given package instance
	 *
	 * @param $name
	 * @return Package
	 * @throws PackageNotDefinedException
	 */
	public function package($name);

	/**
	 * Return an array containing all packages instances
	 *
	 * @return array
	 */
	public function getPackages();

	/**
	 * Add the command instance to the container
	 *
	 * @param $command
	 */
	public function setCommand($command);
}