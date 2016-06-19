<?php

namespace PHPRouter;

/**
 * Class Router Se charge d'enregistrer les routes, retrouver la route correspondante à l'url courante et exécuter le callable (fonction, action d'un contrôleur)
 * @package App\Router
 */
class Router
{
    private $routeCollection;
    private $namedRoutes =[];
    private  $config;

    public function __construct($config = null)
    {
        $this->loadConfig($config);
        $this->routeCollection = new RouteCollection();
    }

    public function getRoutes(){
        return $this->routeCollection->getRoutes();
    }

    public function get($pattern,$callable,$routeName = null){
        if(isset($routeName)){
            $this->registerNamedRoute($routeName,$pattern);
        }
       $this->registerRoute('GET',$pattern,$callable);
    }
    public function post($pattern,$callable){
       $this->registerRoute('POST',$pattern,$callable);
    }
    public function put($pattern,$callable){
       $this->registerRoute('PUT',$pattern,$callable);
    }
    public function delete($pattern,$callable){
       $this->registerRoute('DELETE',$pattern,$callable);
    }
    public function match($methods,$pattern,$callable){
       $this->registerRoutes($methods,$pattern,$callable);
    }
    public function any($pattern,$callable){
        $methods = ["GET","POST","PUT","DELETE"];
        $this->registerRoutes($methods,$pattern,$callable);
    }

    public function registerRoutes($methods,$pattern,$callable){
        $namespace = $this->getControllersNamespace();
        $this->routeCollection->registerRoutes($methods,$pattern,$callable,$namespace);
    }

    public function registerRoute($method,$pattern,$callable){
        $namespace = $this->getControllersNamespace();
        $this->routeCollection->registerRoute($method,$pattern,$callable,$namespace);
    }
    
    public function tryCall($url = null,$method = null){
        try
        {
            if(is_null($url)) $url = $_GET['url'];
            if(is_null($method)) $method = $_SERVER['REQUEST_METHOD'];

            // cherche une route correspondant à url
            $route = $this->routeCollection->getMatchedRoute($url,$method);
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
            $route = $this->routeCollection->getRoute('GET',$url);
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

    private function loadConfig($config){
        if(isset($config) && is_array($config)){
            $this->config = $config;
        }
    }
    
    private function getControllersNamespace(){
        if(isset($this->config) && array_key_exists("controllers_namespace",$this->config)){
            return $this->config['controllers_namespace'];
        }
        return null;
    }
    
    private function registerNamedRoute($routeName,$pattern){
        if(!array_key_exists($routeName,$this->namedRoutes)){
            $this->namedRoutes[$routeName]= $pattern;
        }
    }
}