<?php

namespace Scadaunity\Router;

/**
 * storage em retrieve routes
 * @author Paulo César Andrade Souza>
 * @version 1.0.0
 */
 class RouteCollection
 {
    protected $routes_post = [];
    protected $routes_get = [];
    protected $routes_put = [];
    protected $routes_delete = [];


    public function add($request_type, $pattern, $callback)
    {
       switch($request_type)
       {
          case 'post':
            return $this->addPost($pattern, $callback);
            break;
          case 'get':
            return $this->addGet($pattern, $callback);
            break;
          case 'put':
            return $this->addPut($pattern, $callback);
            break;
          case 'delete':
            return $this->addDelete($pattern, $callback);
            break;
          defautl:
            throw new \Exception('Tipo de requisição não implementado');
       }
    }

    public function where($request_type, $pattern){

        switch($request_type){
          case 'post':
            return $this->findPost($pattern);
            break;
          case 'get':
            return $this->findGet($pattern);
            break;
          case 'put':
            return $this->findPut($pattern);
            break;
          case 'delete':
            return $this->findDelete($pattern);
            break;
          defautl:
            throw new \Exception('Tipo de requisição não implementado');
        }

    }

    protected function parseUri($uri)
    {
      return implode('/', array_filter(explode('/', $uri)));
    }

    protected function definePattern($pattern) {

      $pattern = implode('/', array_filter(explode('/', $pattern)));
      return '/^' . str_replace('/', '\/', $pattern) . '$/';

    }

    protected function addPost($pattern, $callback){

      $this->routes_post[$this->definePattern($pattern)] = $callback;
      return $this;

    }

    protected function addGet($pattern, $callback){

       $this->routes_get[$this->definePattern($pattern)] = $callback;
       return $this;

    }

    protected function addPut($pattern, $callback){

       $this->routes_put[$this->definePattern($pattern)] = $callback;
       return $this;

    }

    protected function addDelete($pattern, $callback){

       $this->routes_delete[$this->definePattern($pattern)] = $callback;
       return $this;

    }

    protected function findPost($pattern_sent)
    {

        $pattern_sent = $this->parseUri($pattern_sent);

        foreach($this->routes_post as $pattern => $callback) {

            if(preg_match($pattern, $pattern_sent, $pieces))
            {
                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;

    }


    protected function findGet($pattern_sent)
    {

        $pattern_sent = $this->parseUri($pattern_sent);

        foreach($this->routes_get as $pattern => $callback) {

            if(preg_match($pattern, $pattern_sent, $pieces))
            {
                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;

    }


    protected function findPut($pattern_sent)
    {

        $pattern_sent = $this->parseUri($pattern_sent);

        foreach($this->routes_put as $pattern => $callback) {

            if(preg_match($pattern, $pattern_sent, $pieces))
            {
                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;

    }


    protected function findDelete($pattern_sent)
    {

        $pattern_sent = $this->parseUri($pattern_sent);

        foreach($this->routes_delete as $pattern => $callback) {

            if(preg_match($pattern, $pattern_sent, $pieces))
            {
                return (object) ['callback' => $callback, 'uri' => $pieces];
            }
        }
        return false;

    }

    protected function strposarray(string $haystack, array $needles, int $offset = 0)
    {
        $result = false;
        if(strlen($haystack) > 0 && count($needles) > 0)
        {
            foreach($needles as $element){
                $result = strpos($haystack, $element, $offset);
                if($result !== false)
                {
                    break;
                }
            }
        }
        return $result;
    }


    protected function toMap($pattern)
    {

        $result = [];

        $needles = ['{', '[', '(', "\\"];

        $pattern = array_filter(explode('/', $pattern));

        foreach($pattern as $key => $element)
        {
            $found = $this->strposarray($element, $needles);

            if($found !== false)
            {
                if(substr($element, 0, 1) === '{')
                {
                    $result[preg_filter('/([\{\}])/', '', $element)] = $key - 1;
                } else {
                    $index = 'value_' . !empty($result) ? count($result) + 1 : 1;
                    array_merge($result, [$index => $key - 1]);
                }
            }
        }
        return count($result) > 0 ? $result : false;
    }

    protected function parsePattern(array $pattern)
    {
        // Define the pattern
        $result['set'] = $pattern['set'] ?? null;
        // Allows route name settings
        $result['as'] = $pattern['as'] ?? null;
        // Allows new namespace definition for Controllers
        $result['namespace'] = $pattern['namespace'] ?? null;
        return $result;
    }

 }
