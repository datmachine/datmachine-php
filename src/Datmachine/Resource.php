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
        self::$_client = new \Datmachine\Client('\Datmachine\Settings', null, __NAMESPACE__ .'\Resource::convertError');
        self::$_registry = new \RESTful\Registry();
        self::$f = self::$fields = new \RESTful\Fields();
    }
    
    protected function _objectify($request, $links = null)
    {
        // initialize uris
        $this->_collection_uris = array();
        $this->_member_uris = array();
        $this->_unmatched_uris = array();

        $class = get_called_class();

        if ($this->getURISpec()->override != null) {
            $resource_name = $this->getURISpec()->override;
        } else {
            $resource_name = $this->getURISpec()->name;
        }

        if(isset($request->$resource_name) && $links == null) {
            $fields = $request->$resource_name;
            $links = $request->links;
        } else {
            $fields = $request;
        }

        if($fields) {
            foreach ($fields as $key => $val) {
                $this->$key = $val;
            }
        }
        if($links) {
            foreach($links as $key => $val) {
                // the links might include links for other resources as well
                $parts = explode('.', $key);
                if($parts[0] != $resource_name) continue;
                $name = $parts[1];

                $url = preg_replace_callback(
                    '/\{(\w+)\.(\w+)\}/',
                    function($match) use ($fields) {
                        $name = $match[2];
                        if(isset($fields->$name))
                            return $fields->$name;
                        elseif(isset($fields->links->$name))
                            return $fields->links->$name;
                    },
                    $val);
                // we have a url for a specific item, so check if it was side loaded
                // otherwise stub it out
                $result = self::getRegistry()->match($url);
                if($result != null) {
                    $class = $result['class'];
                    if($result['collection']) {
                        $this->_collection_uris[$name] = array(
                            'class' => $class,
                            'uri'   => $url,
                        );
                    } else {
                        $this->_member_uris[$name] = array(
                            'class' => $class,
                            'uri'   => $url,
                        );
                    }
                } else {
                    $this->_unmatched_uris[$name] = array(
                        'uri' => $url
                    );
                }
            }
        }
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
