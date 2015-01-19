# Laravel 5 Package Utils
Since laravel 5 doesn't ship with some of the workbench/package functionalities
I've decided to write this package and add some common capabilities to the default
service provider, like publishing *configs* and *migrations*

This package intends to follow the new directions defined by Taylor Otwell, but
if you need the old functionaties back you can try:

**Configuration:** https://github.com/orchestral/config

## Usage
To use this helpers the first step is to add this package to the composer 
dependencies.

	composer require mvalim/package-utils

After the package was installed you only need to extend `Mvalim\PackageUtils\Provider`
in your service provider instead of `Illuminate\Support\ServiceProvider` and the
helpers will be available.

Once you have installed this package you can register as many packages as you
want without worrying about performance. The publishers will only register your
resources at runtime, no additional checks (like if files exist) will be made.
If the resources are not published yet an exception can be thrown.

First you must define your package name and namespace (for config, and other resources
that need a namespace). When regitering your package just call:
	
```
$this->package('<vendor>/<packageName>', '<namespace>');

ex.: $this->package('mvalim/package', 'my-package');
```
**Warning:** *If no namespace is provided, the `vendor.packageName` will be used as the namespace*

## Methods available
When you extend the `Provider` class the below methods will be available:

#### needsConfig($path)
Add configuration files to be published.

**$path**: can be a single file or a directory with multiple files that will be 
merged in a single `config/<vendor>/<package>/config.php` when published.

**Warging:** Like the default laravel config files, all these files must return
an array.

#### needsMigration($path)
Add migration files to be published.
**$path**: must be a directory containing all the migrations that needs to be
published to the migrations path.

**Warning:** This helper will **only copy** the files to the migrations path.


## Publishing your resources
After you've defined your package resources you can publish them using the folowing 
command:
```	
php artisan package:publish <packageName> <optional:resources>

php artisan package:publish mvalim/package

// publish only the configs
php artisan package:publish mvalim/package config

// backup the existing, and overwrite the resources
php artisan package:publish mvalim/package --force
```

If you don't provide a resource, all of the registered resources will be published.
The `package:publish` command accept the option `--force` that will backup the
existing resources to `storage/packages` and will override them.


## Service provider example
```
<?php  namespace Acme;

use Mvalim\PackageUtils\Provider;

class AcmeServiceProvider extends Provider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('acme/awesome-package');
		$this->needsConfig(__DIR__ . '/resources/configs');
		$this->needsMigration(__DIR__ . '/resources/migrations');
	}
}

// in the console
php package:publish acme/awesome-package


// Done, all the configs and migrations are now published! You can access the
// configurations, and all your migrations are now available

Config::get('acme.awesome-package.my-option');
Artisan::call('migrate');
```

## TODO
- Write missing tests !!
- Add other publishers
- Allow people to register custom publishers / helpers