<?php

require '../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');

$config = ['controllers_namespace' => '\\App\\Controller'];
$router = new \PHPRouter\Router($config);

/**
 * Home
 */
$router->get('/',function() use ($router){
    echo '<h1>Home</h1>';
    ?>
    <a href="<?= $router->url('namedroute',['id'=> 10]); ?>">Named route + parameter</a>
    <a href="./test">Test</a>
    <a href="./test/10">Test 10</a>
    <?php
});

/**
 * With function
 */
$router->get('/test',function(){
    echo 'Hello!';
});

/**
 * With parameter
 */
$router->get('/test/:id',function($id){
    echo "Received : $id";
});

/**
 * With parameter regex : TODO
 */
//$router->get('/regex/:message',function($message){
//    echo "Received : $message";
//})->with('message','[a-zA-Z0-9]+');


/**
 * Named routed
 */
$router->get('/mynamedroute/:id',function($id){
    echo "Named route receive $id";
},'namedroute');



/**
 * GET http://localhost/phprouter/example/articles
 */
$router->get('/articles','Article@getAll');


/**
 * GET http://localhost/phprouter/example/articles/1
 */
$router->get('/articles/:id','Article@getOne');


/**
 * POST http://localhost/phprouter/example/articles
 * Content-Type:application/json
 * {
 * "title":"New Title",
 * "content":"Lorem ipsum ..."
 * }
 *
 * Or
 *
 * POST http://localhost/phprouter/example/articles
 * Content-Type:application/x-www-form-urlencoded
 * ... form
 */
$router->post('/articles','Article@add');


/**
 * PUT http://localhost/phprouter/example/articles/2
 * Content-Type:application/json
 * {
 * "id":2,
 * "title":"Update Title",
 * "content":"Lorem ipsum ..."
 * }
 *
 * Or
 *
 * PUT http://localhost/phprouter/example/articles/2
 * Content-Type:application/x-www-form-urlencoded
 * ... form
 */
$router->put('/articles/:id','Article@update');

/**
 * DELETE http://localhost/phprouter/example/articles/1
 */
$router->delete('/articles/:id','Article@delete');


/**
 * Router without namespace config + controllers
 */
//$router->get('/test/controller','\\App\\Controller\\ArticleController@getAll');

$success = $router->tryCall();
if(!$success){
    header("HTTP/1.0 404 Not Found");
    exit;
}