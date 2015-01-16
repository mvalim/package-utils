<?php namespace Mvalim\Workbench;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;

abstract class Publisher {

	/**
	 * Package instance
	 *
	 * @var Package
	 */
	protected $package;

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var  array
	 */
	protected $files;

	public function __construct($package, Application $app)
	{
		$this->package = $package;

		$this->app = $app;

		$this->filesystem = $this->app->make('files');

		$this->container = $this->app->make('Mvalim\Workbench\Container');
	}

	/**
	 * Return the path where the resources should be published
	 *
	 * @return String
	 */
	abstract public function getPath();

	/**
	 * Register the resources if needed
	 *
	 * @return String
	 */
	abstract public function register();

	/**
	 * Publish all files that needs to be copied
	 *
	 * @param bool $force
	 * @return String
	 */
	abstract public function publish($force = false);

	/**
	 * Return a instance of
	 *
	 * @return Filesystem
	 */
	protected function filesystem()
	{
		return $this->filesystem;
	}

	/**
	 * Create destination path if it does not exist
	 *
	 * @return $this
	 */
	protected function makeDirectory()
	{
		$path = $this->getPath();

		if( ! $this->filesystem()->isDirectory($path))
		{
			$this->filesystem()->makeDirectory($path, 0755, true);
		}

		return $this;
	}

	/**
	 * Define what files needs to be published
	 *
	 * @param  string $path
	 * @return self
	 */
	public function filesToPublish($path)
	{
		$fs = $this->filesystem();
		if( ! $fs->exists($path) && ! $fs->isDirectory($path))
		{
			throw new \InvalidArgumentException("$path set to be published in does not exist.");
		}
		if($fs->isDirectory($path))
		{
			$this->files = $fs->allFiles($path);
		} else
		{
			$this->files = [$path];
		}

		return $this;
	}

	public function files()
	{
		return $this->files;
	}

	protected function copyFilesToDestination($force = false)
	{
		$filesPublished = 0;
		$files = $this->files();
		foreach($files as $f)
		{
			$filename = basename($f);
			$destination = $this->destination($f);

			if(is_file($destination))
			{
				if(!$force) {
					$this->container->command()->line("<fg=yellow>Skipping</fg=yellow> <info>{$filename}</info>: file already exists in destination");
					continue;
				}

				$this->backup($destination);
			}
			$this->filesystem->copy($f, $destination);
			$filesPublished++;
		}
		return $filesPublished;
	}

	protected function destination($file)
	{
		return $this->getPath() . '/' . basename($file);
	}

	protected function backup($file)
	{
		$backupPath = $this->app->make('path.storage') . '/workbench/backup';
		if( ! $this->filesystem->isDirectory($backupPath))
		{
			$this->filesystem->makeDirectory($backupPath);
		}
		$newFile = $backupPath . '/' . date('Ymd-h-i') . '_' . basename($file);
		$this->filesystem->move($file, $newFile);
	}

	protected function error(\Exception $e) {
		global $argv;
		if($this->app->runningInConsole() && in_array('workbench:publish', $argv))
		{
			$command = $this->container->command();
			if($command)
			{
				$command->error($e->getMessage());
			}
			return;
		}
		throw $e;
	}
}