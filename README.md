# Laravel 5 Workbench Helper
Since laravel 5 doesn't ship with some of the old workbench / package functionality
I've decided to write this package and add some common capabilities to the default
service provider, like publishing *configs* and *migrations*

## Usage
To use this helpers the first step is to add this package to the composer 
dependencies.

	composer require mvalim\workbench

After the package was installed you only need to extend `Mvalim\Workbench\Provider`
in your service provider instead of `Illuminate\Support\ServiceProvider` and the
helpers will be available.

First you must define your package name and namespace (for config, and other resources
that need a namespace). When regitering your package just call:
	
```
$this->package('<vendor>/<packageName>', '<namespace>');

ex.: $this->package('mvalim/workbench', 'workbench');
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
php artisan workbench:publish <packageName> <optional:resources>

php artisan workbench:publish mvalim/workbench

// publish only the configs
php artisan workbench:publish mvalim/workbench config 

// backup the existing, and overwrite the resources
php artisan workbench:publish mvalim/workbench --force

```
If you don't provide a resource, all of the registered resources will be published.
The `workbench:publish` command accept the option `--force` that will backup the
existing resources to `storage/workbench` and will override them.


## TODO
- Add other publishers
- Allow people to register custom publishers / helpers