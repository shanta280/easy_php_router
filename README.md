# easy_php_router
Lightweight, Easy, simple, fast PHP Router (PHP Routing System) (Single Class)

Single Class Route
Easy to use Routing system for PHP
Easy to Integrate
Easy to Implement
Easy to get Started
Easy to modify


# How to install
```
    composer require shanta280/easy_php_router

```


# Basic Example
```
<?php

require_once __DIR__.'/../vendor/autoload.php';
// or 
// require_once path_to_file/EasyRouter.php


$r = new Shantanu\EasyRouter();

$r->get("/", function() {
    echo "Home page";
});

$r->get("/about/{name}", function($name="") {
    echo "About {$name}";
});

// good this page will be shown 
// when url does not maych any thing
// good for showing 404
$r->get("/{any}", function($any='') {
    echo $any;
});

$r->get("/contact", function() {
    echo "Contact Page";
});

$r->get("/services", function() {
    echo "Services Page";
});

$r->run();// this line is important, it starts the routing process

//////
*
* Now we will be able to access url like
* http://example.com/index.php/about/shantanu
* http://example.com/index.php/service
* 
* to remove the index.php from the url we can use .htaccess file
* see example file here
* https://gist.github.com/shanta280/9fad00bd320f9c9f14416adf10985cb1
*

```


### Supported Methods

```
$r->get("path", "callback")
$r->post("path", "callback")
$r->put("path", "callback")
$r->delete("path", "callback")
// another special method
$r->route("method", "path", "callback")
```


### Supported Callback methods
```
// Closure method
$r->get("/", function() {
    // do something here
});

// namespace method
// this will call the index function of MainController
$r->get("/", "\Namespace\Controller\MainController::index");

// class method
// this will call the about method of MyClass
$r->get("/about", ["MyClass", "about"]);

// we can also use someting like below
$r->route("GET", "/someting", ["\Namespace\Controller\SomeController", "some_method"]);

```

## More Info
in callback we can pass namespace\class, Just Class, Closure, more if you extend it.

