<h3 align="center">Routing</h3>

<div align="center">

[![Status](https://img.shields.io/badge/status-active-success.svg)]()
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)


</div>

---

<p align="center">
    A simple http router for psr7 requests
    <br> 
</p>

## 📝 Table of Contents

- [Prerequisites](#prerequisites)
- [Installing](#installing)
- [Testing](#testing)
- [Coding Style](#coding_style)
- [Getting Started](#getting_started)
- [Usage](#usage)
- [Contributing](#contributing)
- [Authors](#authors)


## Prerequisites <a name = "prerequisites"></a>


- PHP 7.3 +
- Composer 


## Installing <a name = "installing"></a>

The recommended way to install is via Composer:


```
composer require phpatom/routing
```


## Testing Installing <a name = "testing"></a>
 
```
composer test
```

### Coding style <a name = "coding_style"></a>

```
./vendor/bin/phpcs
```

## Getting Started <a name = "getting_started"></a>
### Basic usage 
```php

# create a new router
use Atom\Routing\Route;
use \Atom\Routing\Router;
$router = new Router();

#add routes
$router->add(Route::get("posts","MyController@index","post.index"));
$router->add(Route::get("post/create","MyController@create","post.create"));
$router->add(Route::post("post","MyController@store","post.store"));
$router->add(Route::put("post","MyController@update","post.update"));

$router->add(Route::create(["POST","GET"],"/login","LoginHandler","login"));

# add route group
$router->group("/user",
    function(\Atom\Routing\RouteGroup $group){
        $group->add(Route::get("logout","userController@logout"));
        $group->add(Route::get("dashboard","userController@dashboard"));
    },
    AuthMiddleware::class
);

#dispatch a request
$request = $router->dispatch($request); // it returns a modified request

# get info about the current route

$match = \Atom\Routing\MatchedRoute::of($request)// will be null nothing was found;
echo $match->getRoute()->getHandler(); // anything you set on the route
echo $match->getMethod(); // the method matched
echo $match->getParameters(); // route parameters
echo $match->is("POST","/foo"); //boolean
echo $match->isNamed("myroute"); // boolean
```
### Route parameters
```php
 $router->add(Route::get("/post/{id:\d+}-{slug}", "", "post.single"));
 $router->group("admin", function (RouteGroupContract $group) {
    $group->add(Route::get("/user/{id}", "", "user"));
    $group->add(Route::get("/user", "", "users"));
 });
```

### generate url
```php
$router->add(Route::get("/post/{id:\d+}-{slug}", "", "post.single"));
$router->group("admin", function (RouteGroupContract $group) {
    $group->add(Route::get("/user/{id}", "", "user"));
    $group->add(Route::get("/user", "", "users"));
});
$router->generateUrl("users"); // "admin/user";
$router->generateUrl("user", ["id" => 42]); // "/admin/user/42"
// or 
\Atom\Routing\Router::pathFor("post.single", ["id" => 42, "slug" => "aze-aze"]); //"/post/42-aze-aze"
```
## Contributing <a name = "contributing"></a>
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.


## ✍️ Author <a name = "authors"></a>

- [@dani-gouken](https://github.com/dani-gouken) - Idea & Initial work

