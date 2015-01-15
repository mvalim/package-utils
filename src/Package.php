<?php  namespace Mvalim\Workbench; 

use Mvalim\Workbench\Exceptions\PublisherNotRegisteredException;

class Package {

	/**
	 * @var  string
	 */
	protected $name;

	/**
	 * Namespace to be used when we need to register package resources like
	 * configuration.
	 *
	 * vendor/awesome-package => awesome-package.opt
	 *
	 * $namespace = 'my-package' => my-package.opt
	 *
	 * @var  string
	 */
	protected $namespace;

	/**
	 * Publishers instances
	 *
	 * @var array
	 */
	protected $publishers = [];

	/**
	 * Custom publishers instances
	 *
	 * @var array
	 */
	protected $customPublishers = [];

	function __construct($name, $namespace = null)
	{
		$this->name = $name;
		$this->namespace = $namespace;

		$this->startPublishers();
	}


	/**
	 * Start the default publisher
	 */
	protected function startPublishers() {
		$this->publishers['config'] = $this->configPublisher();
		$this->publishers['migration'] = $this->migrationPublisher();
	}

	/**
	 * @return Publishers\Config
	 */
	public function configPublisher() {
		if(!isset($this->publishers['config'])) {
			$this->publishers['config'] = app()->make('Mvalim\Workbench\Publishers\Config', [
				'package' => $this
			]);
		}
		return $this->publishers['config'];
	}

	/**
	 * @return Publishers\Migration
	 */
	public function migrationPublisher() {
		if(!isset($this->publishers['migration'])) {
			$this->publishers['migration'] = app()->make('Mvalim\Workbench\Publishers\Migration', [
				'package' => $this
			]);
		}
		return $this->publishers['migration'];
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace ?: basename($this->getName());
	}

	/**
	 * @param $publisher
	 * @return Publisher
	 * @throws PublisherNotRegisteredException
	 */
	public function getPublisher($publisher) {
		if( ! isset($this->publishers[$publisher]))
		{
			throw new PublisherNotRegisteredException("No publisher for $publisher was registered from package {$this->getName()}");
		}
		return $this->publishers[$publisher];
	}

	public function getPublishers() {
		return $this->publishers();
	}
}