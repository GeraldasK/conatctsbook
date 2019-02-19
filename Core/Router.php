<?php

namespace Core;

class Router
{
    private $routes = [];
    private $params = [];

    public function setRoutes($route, $params = [])
    {
        $this->routes[$route] = $params;
    }

    public function matchRoutes($url)
    {
        foreach($this->routes as $route=>$params) {
            if($url == $route){
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function dispatchRoutes($url)
    {
        $url = $this->removeQueryStringVariables($url);
        if($this->matchRoutes($url)) {
            $controller = $this->params['controller'];
            $controller = ucfirst($controller);
            $controller = 'App\Controllers\\' . $controller;

            if (class_exists($controller)) {
                $controller_obj = new $controller();
                $action = $this->params['action'];
                $action = str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));
                $action = lcfirst($action);

                if(is_callable([$controller_obj, $action])) {
                    $controller_obj->$action();
                } else {
                    throw new \Exception("This action is uncallable");
                }
            } else {
                throw new \Exception("$controller - this class not exists");
            }
        } else {
            throw new \Exception("no route matches");
        }
    }
    
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}