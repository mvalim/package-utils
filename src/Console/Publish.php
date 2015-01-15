<?php namespace Mvalim\Workbench\Console;

use Illuminate\Console\Command;
use Mvalim\Workbench\Container;
use SebastianBergmann\Exporter\Exception;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Publish extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'workbench:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish resources from a package.';


	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$packageName = $this->argument('package');

		$this->container = app()->make('Mvalim\Workbench\Container');
		$package = $this->container->package($packageName);

		$resource = $this->argument('resource');
		$allPublishers = $resource ? [$package->getPublisher($resource)] : $package->getPublishers();

		foreach($allPublishers as $key => $publisher) {
			$this->comment('Publishing ' . ($resource ?: $key) . ' for package ' . $packageName);
			try {
				$publisher->publish($this->option('force'));
			} catch(\Exception $e) {
				$this->error("\n" . $e->getMessage());
				return;
			}

		}
		$this->info("Resource(s) from $packageName published");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['package', InputArgument::REQUIRED, 'The package name.'],
			['resource', InputArgument::OPTIONAL, 'What to to publish? (optional, if none is given will execute all registered publishers)'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['force', null, InputOption::VALUE_NONE, 'Make a backup of the existing files, and overwrite them.'],
		];
	}

}
