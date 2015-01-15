<?php  namespace Mvalim\Workbench\Publishers;

use Mvalim\Workbench\Publisher;

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
		$this->copyFilesToDestination();
	}
}