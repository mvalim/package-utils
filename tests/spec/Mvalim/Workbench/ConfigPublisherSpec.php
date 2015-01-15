<?php

namespace spec\Mvalim\Workbench;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Mvalim\Workbench\Publisher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigPublisherSpec extends ObjectBehavior
{
    function let(Application $app) {
        $this->beAnInstanceOf('spec\Mvalim\Workbench\ConfigPublisher');
        $this->beConstructedWith('mvalim/package', $app);
    }
    function it_is_initializable(Application $app)
    {
        $this->shouldHaveType('spec\Mvalim\Workbench\ConfigPublisher');
    }

    function it_should_return_the_correct_path() {
        $this->getPath()->shouldReturn('app/config/mvalim/package');
    }

    function it_should_throw_exception_when_adding_non_existing_path(Application $app, Filesystem $files) {
        $path = './my-config.php';
        $files->exists($path)->willReturn(false);
        $app->make('files')->willReturn($files);

        $this->shouldThrow()->during('filesToPublish',[$path]);

        $files->exists($path)->willReturn(true);
        $files->isDirectory($path)->willReturn(false);

        $this->filesToPublish($path)->shouldReturn($this);
    }

    function it_should_read_the_directory_if_one_was_provided(Application $app, Filesystem $files) {
        $path = './my-path';
        $allFiles = [
            './my-path/file1.php',
            './my-path/file2.php',
        ];

        $files->exists($path)->willReturn(true);
        $files->isDirectory($path)->willReturn(true);

        $files->allFiles($path)->willReturn($allFiles)->shouldBeCalled();

        $app->make('files')->willReturn($files);

        $this->filesToPublish($path);
        $this->files()->shouldReturn($allFiles);
    }

    function it_should_always_return_an_array_when_fetching_the_files(Application $app, Filesystem $files) {
        $path = './my-config.php';
        $files->exists($path)->willReturn(true);
        $files->isDirectory($path)->willReturn(false);

        $app->make('files')->willReturn($files);

        $this->filesToPublish($path);
        $this->files()->shouldReturn([$path]);
    }
}

class ConfigPublisher extends Publisher {
    public function getPath()
    {
        return 'app/config/' . $this->package;
    }

    /**
     * Register the resources if needed
     *
     * @return String
     */
    public function register()
    {
        // TODO: Implement register() method.
    }

    /**
     * Publish all files that needs to be copied
     *
     * @param bool $force
     * @return String
     */
    public function publish($force = false)
    {
        // TODO: Implement publish() method.
    }
}
