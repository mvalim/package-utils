<?php

namespace spec\Mvalim\Workbench\Providers;

use Mvalim\Workbench\ContainerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mvalim\Workbench\Provider;
use Illuminate\Contracts\Foundation\Application;

class CorrectPackageNameSpec extends ObjectBehavior {

	function let(Application $app, ContainerInterface $container)
	{
		$container->addPackage('mvalim/package')->shouldBeCalled();
		$app->make('Mvalim\Workbench\Container')->willReturn($container);

		$this->beAnInstanceOf('spec\Mvalim\Workbench\Providers\CorrectProviderStub');
		$this->beConstructedWith($app);
	}
}

class CorrectProviderStub extends Provider {

	public function register()
	{
		return $this->package('mvalim/package');
	}
}

class Container {}