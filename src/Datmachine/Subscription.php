<?php

namespace Datmachine;

use Datmachine\Resource;
use \RESTful\URISpec;

class Subscription extends Resource
{
    protected static $_uri_spec = null;

    public static function init()
    {
        self::$_uri_spec = new URISpec('subscriptions', 'id', '/');
        self::$_registry->add(get_called_class());
    }
    
    public function cancel()
    {
      $this->status = "cancelled";
      return($this->save());
    }

}