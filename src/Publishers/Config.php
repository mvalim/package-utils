<?php  namespace Mvalim\Workbench\Publishers;

use Mvalim\Workbench\Exceptions\FileExistsException;
use Mvalim\Workbench\Exceptions\InvalidConfigFileException;
use Mvalim\Workbench\Exceptions\PackageConfigMissing;
use Mvalim\Workbench\Publisher;

class Config extends Publisher {

	protected $name = 'config';

	/**
	 * Return the path where the resources should be published
	 *
	 * @return String
	 */
	public function getPath()
	{
		return $this->app->make('path.config') . '/' . trim($this->package->getName(), '/\\');
	}

	/**
	 * Register the resources if needed
	 *
	 * @return String
	 */
	public function register()
	{
		$namespace = $this->package->getNamespace();

		try
		{
			return $this->app->make('config')->set([
				$namespace => include($this->getPath() . '/config.php')
			]);
		} catch(\Exception $e) {
			$msg = "You need to publish the configs for the package {$this->package->getName()}";
			$this->error(new PackageConfigMissing($msg));
		}
	}

	public function publish($force = false)
	{
		$configFile = $this->getPath() . '/config.php';
		$config = $this->getOptions();
		if( ! $config)
		{
			return;
		}
		if($this->filesystem->exists($configFile))
		{
			if( ! $force)
			{
				throw new FileExistsException('The config file was already published');
			}
			$this->backup($configFile);
		}

		$this->makeDirectory();
		$this->filesystem->put($configFile, $this->buildFileContents($config));
	}

	public function getOptions()
	{
		$files = $this->files();
		if( ! count($files))
		{
			return false;
		}
		foreach($files as $f)
		{
			$opts[$f] = include($f);
			if( ! is_array($opts[$f]))
			{
				throw new InvalidConfigFileException("All configuration files must return an array");
			}
		}

		$configArray = [];
		foreach($opts as $arr)
		{
			$configArray = array_merge($configArray, $arr);
		}

		return $configArray;
	}

	protected function buildFileContents(array $data)
	{
		return "<?php \nreturn " . var_export($data, true) . ";";
	}
}