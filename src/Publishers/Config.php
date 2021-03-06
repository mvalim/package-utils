<?php  namespace Mvalim\PackageUtils\Publishers;

use Mvalim\PackageUtils\Exceptions\FileExistsException;
use Mvalim\PackageUtils\Exceptions\InvalidConfigFileException;
use Mvalim\PackageUtils\Exceptions\PackageConfigMissing;
use Mvalim\PackageUtils\Publisher;

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
		$config = $this->app->make('config');

		try
		{
			$path = $this->getPath() . '/config.php';
			if($this->app->environment() === 'testing') {
				if( ! $this->filesystem->exists($path)) {
					return $config->set([
						$namespace => $this->getOptions()
					]);
				}
			}
			return $config->set([
				$namespace => include($path)
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
				$this->container->command()->line('<fg=yellow>Skipping</fg=yellow> <info>configuration</info>: file already exists in destination.');
				return;
			}
			$this->backup($configFile);
		}

		$this->makeDirectory();
		$this->filesystem->put($configFile, $this->buildFileContents($config));
		$this->container->command()->line('<info>Package configs published to:</info> '.$configFile);
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
			$f = (string)$f;
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