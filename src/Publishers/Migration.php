<?php  namespace Mvalim\PackageUtils\Publishers;

use Mvalim\PackageUtils\Publisher;

class Migration extends Publisher {

	/**
	 * Return the path where the resources should be published
	 *
	 * @return String
	 */
	public function getPath()
	{
		return app()->make('path.database') . '/migrations';
	}

	public function register() { }

	public function publish($force = false)
	{
		if($this->copyFilesToDestination($force))
		{
			$this->container->command()->line('<info>Package migrations published to:</info> '.$this->getPath());
		}
	}
}