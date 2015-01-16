<?php  namespace Mvalim\PackageUtils;

use Illuminate\Support\ServiceProvider;
use Mvalim\PackageUtils\Exceptions\IncorrectPackageNameException;

abstract class Provider extends ServiceProvider {

	/**
	 * @var Package
	 */
	protected $package;

	public function __construct($app)
	{
		parent::__construct($app);

		if( ! $this->app->resolved('Mvalim\PackageUtils\Container'))
		{
			$this->app->singleton('Mvalim\PackageUtils\Container', function () use ($app)
			{
				return new Container($app);
			});
		}
	}

	/**
	 * Get container instance
	 *
	 * @return \Mvalim\PackageUtils\Container
	 */
	protected function container()
	{
		return $this->app->make('Mvalim\PackageUtils\Container');
	}

	protected function package($packageName = null, $namespace = null)
	{
		if( ! $packageName)
		{
			return $this->package;
		}
		if( ! preg_match('/\w*\/\w*/', $packageName))
		{
			throw new IncorrectPackageNameException(
				"A package should follow the <vendor>/<package> naming convention. $packageName is an invalid name."
			);
		}

		return $this->package = $this->container()->addPackage($packageName, $namespace);
	}

	protected function needsConfig($path)
	{
		$publisher = $this->package->configPublisher();
		$publisher->filesToPublish($path);

		$this->registerResources($publisher);

		return $this;
	}

	protected function needsMigration($path)
	{
		$publisher = $this->package->migrationPublisher();
		$publisher->filesToPublish($path, null);

		$this->registerResources($publisher);

		return $this;
	}

	protected function registerResources($publisher)
	{
		if(method_exists($publisher, 'register'))
		{
			$publisher->register();
		}
	}
}