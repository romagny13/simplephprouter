<?php

require 'vendor/autoload.php';

$config = ['controllers_namespace' => '\\App\\Controller'];
$router = new \App\Router\Router($config);

/**
 * Enregistrement des routes
*/

$router->get('/',function() use ($router){
    echo '<h1>Page Accueil</h1>';

//    echo $_SERVER['REQUEST_SCHEME']. '://'. $_SERVER['HTTP_HOST']. '/' . $_SERVER['REQUEST_URI'];
//    var_dump($_SERVER);
    ?>
    <a href="<?= $router->url('namedroute', ['id'=> 20]); ?>">Details articles id 20</a>
    <a href="<?= $router->url('testroute', ['id'=> 10]); ?>">Action controleur</a>
    <?php
});

$router->get('/articles',function(){
    echo 'Liste articles';
});

$router->get('/articles/:id',function($id){
    echo "Article id $id";
},'namedroute');

// CONTROLLERS
//$router->get('/test/controller','\\App\\Controller\\TestController@index');
//$router->get('/test/controller/:id','\\App\\Controller\\TestController@withparam','testroute');

$router->get('/test/controller','Test@index');
$router->get('/test/controller/:id','Test@withparam','testroute');

//var_dump($router->getRoutes());
$success = $router->tryCall();
if(!$success){
    header("HTTP/1.0 404 Not Found");
    include("notfound.php");
}