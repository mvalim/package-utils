<?php

namespace spec\Mvalim\PackageUtils\Providers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mvalim\PackageUtils\Provider;
use Illuminate\Contracts\Foundation\Application;

class WrongPackageNameSpec extends ObjectBehavior
{
    function let(Application $app) {
        $this->beAnInstanceOf('spec\Mvalim\PackageUtils\Providers\ProviderStub');
        $this->beConstructedWith($app);
    }
    function it_is_initializable()
    {
        $this->beAnInstanceOf('spec\Mvalim\PackageUtils\Providers\ProviderStub');
    }

    function it_should_throw_exception_with_wrong_package_name() {
        $this->shouldThrow('Mvalim\PackageUtils\Exceptions\IncorrectPackageNameException')
            ->duringRegister();
    }
}

class ProviderStub extends Provider {

    public function register()
    {
        $this->package('wrong_package');
    }
}