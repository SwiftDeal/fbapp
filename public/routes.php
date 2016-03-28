<?php

// define routes

$routes = array(
    array(
        "pattern" => "index",
        "controller" => "home",
        "action" => "index"
    ),
    array(
        "pattern" => "profile",
        "controller" => "home",
        "action" => "profile"
    ),
    array(
        "pattern" => "privacy",
        "controller" => "home",
        "action" => "privacy"
    ),
    array(
        "pattern" => "undefined",
        "controller" => "home",
        "action" => "index"
    ),
    array(
        "pattern" => "home",
        "controller" => "home",
        "action" => "index"
    )
);

// add defined routes
foreach ($routes as $route) {
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals
unset($routes);
