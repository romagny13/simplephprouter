<?php
namespace PHPRouter;

class Route
{
    public $methods = [];
    public $pattern;
    public $callable; // function ou Controller@action
    public $callableIsFunction;
    public $controller;
    public $action;
    private $regexParams = [];

    public function __construct($methods,$pattern,$callable,$namespace=null)
    {
        $this->methods= $methods;
        $this->pattern = $pattern;
        $this->callable = $callable;

        if(is_string($this->callable)){
            $this->callableIsFunction = false;
            $split = explode('@', $this->callable); // "\App\Controller\ArticleController@index" ou Article@index

            if(isset($namespace)){
                $last = substr($namespace,strlen($namespace));
                if($last !='\\'){
                    $namespace = $namespace . '\\';
                }
                $controllerClass = $namespace.$split[0]. 'Controller';
                $this->controller = new $controllerClass();
                $this->action = $split[1];
            }
            else{
                $controllerClass = $split[0];
                $this->controller = new $controllerClass();
                $this->action = $split[1];
            }
        }
        else{
            $this->callableIsFunction = true;
        }
    }

    public function match($url)
    { // de la forme '' ou articles ou articles/10
        // on remplace les paramètres :id par leur regex pour tester si le pattern de la route correposnd à l'url reçue
        //$formatted_pattern = preg_replace('#:([\w]+)#', '([0-9]+)', $this->pattern);
        $formatted_pattern = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->pattern);
        $regex = "#^$formatted_pattern$#i";
        return preg_match($regex, $url);
    }

    public function getParameters($url){
        $formatted_pattern = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->pattern);
        $regex = "#^$formatted_pattern$#i";

        if(preg_match($regex, $url,$matches)){
            array_shift($matches); // supprime le premier élément pour ne pas avoir par exemple articles/1
            return $matches;
        }
        return Array();
    }

    public function with($parameter, $regex){
        $this->regexParams[$parameter] = str_replace('(', '(?:', $regex);
        return $this;
    }

    public function call($parameters = []){
        if($this->callableIsFunction){
            call_user_func_array($this->callable, $parameters);
        }
        else{
            call_user_func_array(array($this->controller, $this->action), $parameters);
        }
    }

    private function paramMatch($match){
        //var_dump($match); // :id et id par exemple
        if(isset($this->regexParams[$match[1]])){
            return  '('. $this->regexParams[$match[1]] . ')';
        }
        return '([0-9]+)';
    }
}
