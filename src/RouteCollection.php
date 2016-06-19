<?php

namespace PHPRouter;


class RouteCollection
{
    private $routes = [];

    public function getRoutes()
    {
        return $this->routes;
    }

    public function registerRoutes($methods,$pattern,$callable,$namespace=null){
        foreach($methods as $method){
            if($this->isValidMethod($method) && !$this->containsRoute($method,$pattern)){
                $this->registerRoute($method,$pattern,$callable,$namespace);
            }
        }
    }

    public function registerRoute($method,$pattern,$callable,$namespace=null){
        if(!$this->containsRoute($pattern,$method))
        {
            if(!$this->isValidMethod($method)){
                throw new RouterException("$method is not a valid method");
            }

            $route = new Route($method,$pattern,$callable,$namespace);
            $this->routes[$method][] = $route;
        }
        else {
            throw new RouterException("$method $pattern already used");
        }
    }

    private function isValidMethod($method){
        $regex = "#^(GET|POST|PUT|PATCH|DELETE)$#";
        return preg_match($regex,$method);
    }

    private function containsRoute($method,$pattern){
        if(isset($this->routes[$method])){
            foreach ($this->routes[$method] as $route){
                if($route->pattern ===  $pattern){
                    return true;
                }
            }
        }
        return false;
    }

    public function getMatchedRoute($url,$method){
        foreach($this->routes[$method] as $route){
            if($route->match($url)){
                return $route;
            }
        }
        return null;
    }

    public function getRoute($method,$pattern){
        foreach($this->routes[$method] as $route){
            if($route->pattern == $pattern){
                return $route;
            }
        }
        return null;
    }

}