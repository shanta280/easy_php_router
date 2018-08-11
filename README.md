# easy_php_router
Easy, simple, fast PHP Router (PHP Routing System) (Single Class)

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



# Supported Methods 
```
// dynamic url with {someting}

$app->get('/', '')
$app->post('/', '')
$app->put('/page/{slug}/update', function($slug) {
    // do someting with slug
});
$app->delete('/user/{id}', '')
$app->route('GET/POST/PUT/DELETE', '/', '')

to configure 404 (Page Not Found)

$app->get("*", function() {
    // page not found
});

```


### How To Use Example 1 (Method Chaining)

```
<?php

require_once __DIR__.'/../vendor/autoload.php';

use Shantanu\EasyRouter\EasyRouter;

EasyRouter::start()
    ->get('/', function() {
    
        echo "Hello Home Page";
    })
    ->get('/about', function() {
        echo "About EasyPhpRouter";
    })
    ->run();

```


### How To Use Example 2
```
<?php

require_once __DIR__.'/../vendor/autoload.php';

use Shantanu\EasyRouter\EasyRouter;

$app = new EasyRouter();

// Set Your Routes the way you want

// 

$app->get('/', function() {
    echo "Hello Home Page";
});
$app->post("/login", ["ClassName", "loginFunction"]);

// dynamic route 1
$app->get('/about/{username}', "\Namespace\Controller\AboutController::about");
// dynamic route 2
$app->get('/page/{someting}/anyting', "\Namespace\Controller\AboutController::about");

$app->route("GET", "/someting", ["\Namespace\Controller\SomeController", "some_method"]);

$app->run();// this line is important

```

## More Info
in callback we can pass namespace\class, Just Class, Closure, more if you extend it.

