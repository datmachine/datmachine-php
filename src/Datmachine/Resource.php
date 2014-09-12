<?php

namespace Datmachine;

use Datmachine\Errors\Error;
use RESTful\Exceptions\HTTPError;

class Resource extends \RESTful\Resource
{
    public static $fields, $f;

    protected static $_client, $_registry, $_uri_spec;

    public static function init()
    {
        self::$_client = new \RESTful\Client('\Datmachine\Settings', null, __NAMESPACE__ .'\Resource::convertError');
        self::$_registry = new \RESTful\Registry();
        self::$f = self::$fields = new \RESTful\Fields();
    }

    public static function convertError($response)
    {
        if (property_exists($response->body, 'errors'))
            $error = Error::createFromResponse($response);
        else
            $error = new HTTPError($response);
        return $error;
    }

    public static function getClient()
    {
        $class = get_called_class();
        return $class::$_client;
    }

    public static function getRegistry()
    {
        $class = get_called_class();
        return $class::$_registry;
    }

    public static function getURISpec()
    {
        $class = get_called_class();
        return $class::$_uri_spec;
    }
    
    public static function all(){
      $class = get_called_class();
      $uri_spec = self::getURISpec();
      $uri = $uri_spec->collection_uri;
      $response = self::getClient()->get($uri);
      
      $resource_name = $uri_spec->name;
      
      $objects = $response->body->$resource_name;
      $links = $response->body->links;
      
      $bar = array();
      
      foreach ($objects as $object){
        $foo = new \StdClass();
        $foo->$resource_name = $object;
        $foo->links = $links;
        $bar[] = new $class($foo);
        #$bar[] = $foo;
      }
        
      
      return $bar;
    }
}
