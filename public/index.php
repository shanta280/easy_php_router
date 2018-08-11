<?php

require_once __DIR__.'/../vendor/autoload.php';

use Shantanu\EasyRouter\EasyRouter;

EasyRouter::start()
    ->get('/', function() {
    
        echo "Hello Home Page";
    })
    ->run();
