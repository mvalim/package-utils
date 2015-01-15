<?php

namespace spec\Mvalim\Workbench;

use Illuminate\Contracts\Foundation\Application;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContainerSpec extends ObjectBehavior
{
    function it_is_initializable(Application $app)
    {
        $this->beConstructedWith($app);
        $this->shouldHaveType('Mvalim\Workbench\Container');
    }
}
