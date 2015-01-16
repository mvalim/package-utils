<?php

namespace spec\Mvalim\PackageUtils\Providers;

use Mvalim\PackageUtils\ContainerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mvalim\PackageUtils\Provider;
use Illuminate\Contracts\Foundation\Application;

class CorrectPackageNameSpec extends ObjectBehavior {

	function let(Application $app, ContainerInterface $container)
	{
		$container->addPackage('mvalim/package')->shouldBeCalled();
		$app->make('Mvalim\PackageUtils\Container')->willReturn($container);

		$this->beAnInstanceOf('spec\Mvalim\PackageUtils\Providers\CorrectProviderStub');
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