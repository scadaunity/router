<?php

namespace Scadaunity\Router;

use Scadaunity\Router\Request;
use Scadaunity\Router\Dispacher;
use Scadaunity\Router\RouteCollection;

/**
 *
 */
class Route
{

    protected static $route_collection;

    protected static $dispacher;

    public function __construct()
    {
        self::$route_collection = new RouteCollection;
        self::$dispacher = new Dispacher;
        $this->request = new Request;
    }

    public static function get($pattern, $callback)
    {
        self::$route_collection->add('get', $pattern, $callback);
        return self::$route_collection;
    }

    public static function post($pattern, $callback)
    {
        self::$route_collection->add('post', $pattern, $callback);
        return $this;
    }

    public static function put($pattern, $callback)
    {
        self::$route_collection->add('put', $pattern, $callback);
        return $this;
    }

    public static function delete($pattern, $callback)
    {
        self::$route_collection->add('delete', $pattern, $callback);
        return $this;
    }

    public static function find($request_type, $pattern)
    {
        return self::$route_collection->where($request_type, $pattern);
    }

    protected static function dispach($route, $namespace = "App\\"){
      return self::$dispacher->dispach($route->callback, $route->uri, $namespace);
    }

    protected function notFound()
    {
        return header("HTTP/1.0 404 Not Found", true, 404);
    }

    public static function resolve($request){
      echo "<pre>";
      var_dump($request);
      die();
        $route = self::find($request->method(), $request->uri());
        if($route)
        {
            return self::dispach($route);
        }
        return $this->notFound();
    }
}
