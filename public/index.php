<?php

require_once __DIR__.'/../vendor/autoload.php';

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

$r->run();