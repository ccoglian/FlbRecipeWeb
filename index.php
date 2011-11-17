<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/inc/init.php');

/**
 * Step 1: Require the Slim PHP 5 Framework
 *
 * If using the default file layout, the `Slim/` directory
 * will already be on your include path. If you move the `Slim/`
 * directory elsewhere, ensure that it is added to your include path
 * or update this file path as needed.
 */
require 'inc/Slim/Slim.php';

/**
 * Step 2: Instantiate the Slim application
 *
 * Here we instantiate the Slim application with its default settings.
 * However, we could also pass a key-value array of settings.
 * Refer to the online documentation for available settings.
 */
$app = new Slim(array(
    'debug' => true,
    'log.enable' => true,
    'log.path' => 'inc/Slim/logs',
    'log.level' => 3
));

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function. If you are using PHP < 5.3, the
 * second argument should be any variable that returns `true` for
 * `is_callable()`. An example GET route for PHP < 5.3 is:
 *
 * $app = new Slim();
 * $app->get('/hello/:name', 'myFunction');
 * function myFunction($name) { echo "Hello, $name"; }
 *
 * The routes below work with PHP >= 5.3.
 */

//GET routes
$app->get('/', function () {
    echo "Home!";
});

$app->get('/recipe/:id', function ($id) use($app) {
    $app->response()->header('Content-Type', 'application/json; charset=utf-8');
    $app->response()->header('Access-Control-Allow-Origin', '*');
    $app->render('raw.php', array(
        'view' => 'raw',
        'obj' => new Recipe($id)
    ));
});

//POST route
//$app->post('/person', function () {
    //Create new Person
//});

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This is responsible for executing
 * the Slim application using the settings and routes defined above.
 */
$app->run();