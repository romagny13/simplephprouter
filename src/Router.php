<?php

namespace PHPRouter;

/**
 * Class Router Se charge d'enregistrer les routes, retrouver la route correspondante à l'url courante et exécuter le callable (fonction, action d'un contrôleur)
 * @package App\Router
 */

class Router{

    private $routes = [];
    private $namedRoutes =[];
    private  $config;

    public function __construct($config = null)
    {
        if(isset($config) && is_array($config)){
            $this->config = $config;
        }
    }

    public function get($pattern,$callable, $routeName = null){
        $route = $this->createRoute(["GET"],$pattern,$callable);
        if(isset($routeName) && $route != null){
            $this->registerNamedRoute($routeName,$pattern);
        }
        return $route;
    }
    public function post($pattern,$callable){
        return $this->createRoute(["POST"],$pattern,$callable);
    }
    public function put($pattern,$callable){
        return $this->createRoute(["PUT"],$pattern,$callable);
    }
    public function patch($pattern,$callable){
        return $this->createRoute(["PATCH"],$pattern,$callable);
    }
    public function delete($pattern,$callable){
        return $this->createRoute(["DELETE"],$pattern,$callable);
    }
    public function match($methods,$pattern,$callable){
        if(!$this->validMethods($methods)) throw new RouterException("Method(s) not valid");
        return $this->createRoute($methods,$pattern,$callable);
    }
    public function any($pattern,$callable){
        return $this->createRoute(["GET","POST","PUT","PATCH","DELETE"],$pattern,$callable);
    }

    public function run($url = null,$method = null){
        try
        {
            if(is_null($url)) $url = $_GET['url'];
            if(is_null($method)) $method = $_SERVER['REQUEST_METHOD'];

            // cherche une route correspondant à url
            $route = $this->getMatchedRoute($url,$method);
            if(isset($route)){
                // on remplace les patterns de paramètres par leurs valeurs
                // exécution du callable
                $parameters = $route->getParameters($url);
                $route->call($parameters);

                return true;
            }
        }
        catch (\Exception $ex){
            error_log($ex->getMessage());
        }
        return false;
    }

    public function url($routeName, $parameters =[]){
        if(array_key_exists($routeName,$this->namedRoutes)){
            $url =  $this->namedRoutes[$routeName];
            // remplacer les paramètres (exemple :id) par leurs valeurs (exemple 10)
            $route = $this->getRoute('GET',$url);
            if(is_null($route)){
                throw new RouterException("No route with $routeName found");
            }
            foreach ($parameters as $key=>$value){
                $regex = "#:$key#i";
                $url = preg_replace($regex,$value,$url);
            }
            return trim($url, '/');
        }
        throw new RouterException("No route with $routeName found");
    }

    public function has($method,$pattern){
        if(isset($this->routes[$method])){
            foreach ($this->routes[$method] as $route){
                if($route->pattern ===  $pattern){
                    return true;
                }
            }
        }
        return false;
    }

    private function createRoute($methods,$pattern,$callable){
        if($pattern === null) throw new RouterException("Pattern cannot be null");
        if($callable === null) throw new RouterException("Callable cannot be null");

        $namespace = $this->getControllersNamespace();
        $route = new Route($methods,$pattern,$callable,$namespace);

        foreach($methods as $method){
            $this->routes[$method][] = $route;
        }
        return $route;
    }

    private function validMethods($methods){
        $regex = "#^(GET|POST|PUT|PATCH|DELETE)$#";
        foreach ($methods as $method) {
            if(!preg_match($regex,$method)){
                return false;
            }
        }
        return true;
    }

    private function getMatchedRoute($url,$method){
        foreach($this->routes[$method] as $route){
            if($route->match($url)){
                return $route;
            }
        }
        return null;
    }

    private function getRoute($method,$pattern){
        foreach($this->routes[$method] as $route){
            if($route->pattern == $pattern){
                return $route;
            }
        }
        return null;
    }

    private function registerNamedRoute($routeName,$pattern){
        if(!array_key_exists($routeName,$this->namedRoutes)){
            $this->namedRoutes[$routeName]= $pattern;
        }
        else{
            throw new RouterException("Route named $routeName already used");
        }
    }

    private function getControllersNamespace(){
        if(isset($this->config) && array_key_exists("controllers_namespace",$this->config)){
            return $this->config['controllers_namespace'];
        }
        return null;
    }

}
