# BanHammer
BanHammer is package for [Laravel 5](http://laravel.com).


## Installation
Add the following line to the require block of your `composer.json` file:
```
"kikfinder/banhammer": "dev-master"
```

For now its also required to add this repository to the repositories block of your `composer.json` file:
```
{
  "type": "vcs",
  "url": "https://github.com/EvanDarwin/BanHammer"
}
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once BanHammer is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'KikFinder\BanHammer\ServiceProvider'`

You can register the BanHammer facade in the `aliases` key of your `config/app.php` file if you like.

* `'BanHammer' => 'KikFinder\BanHammer\Facades\BanHammer'`

We also have to rigister the route middleware, open up `app/Http/Kernel.php` and add the following to the `routeMiddleware` array.

* `'hammer' => 'KikFinder\BanHammer\HTTP\BanHammerMiddleware'`


## Configuration

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This package will publish a config file `hammer.php` and a migration file `2015_02_22_170803_BanHammer_Create_Tables.php`. Execute the following commando to run the migration.

```bash
$ php artisan migrate
```

#### Options

`endpoint` - The application endpoint for BanHammer to connect to in order to download ban listings.

`secret` - The shared secret used to authenticate with BanHammer


## Usage

##### Route Middleware

Add the following to you `app/Http/routes.php` file in order to register your route. You should use the middleware as defined in app/Http/Kernel.php` and provide the field name for the username as argument `hammer`.

```php
Route::post('shoutouts', [
  'as'          => 'shoutouts.create', 
  'uses'        => 'ShoutoutController@createShoutout', 
  'middleware'  => 'hammer', 
  'hammer'      => 'username'
]);
```

When the visitor is banned by his username or IP-address the middleware will throw the exception `\KikFinder\BanHammer\BannedUserException`. 

You should handle this exception in `app/Exceptions/Handler.php`, for example;

```php
public function render($request, Exception $e)
{
	if ($e instanceof \KikFinder\BanHammer\BannedUserException)
		die('You are a bad spammer!');

	return parent::render($request, $e);
}
```


##### Facade
The following methods are provided to ban a certain username or IP-address. These methods will return an instace of `KikFinder\BanHammer\Ban`.
```php
use KikFinder\BanHammer\Facades\BanHammer;

BanHammer::ban('bad-username', '127.0.0.1');
```

```php
use KikFinder\BanHammer\Facades\BanHammer;

BanHammer::banUsername('bad-username');
```

```php
use KikFinder\BanHammer\Facades\BanHammer;

BanHammer::banIpAddress('127.0.0.1');
```

Its also possible to check if an certain username or IP-address is banned. The following method wil return a `boolean` value. 
```php
use KikFinder\BanHammer\Facades\BanHammer;

BanHammer::isBanned('bad-username', '127.0.0.1');
```
