<?php

namespace Datmachine;

use Datmachine\Resource;
use \RESTful\URISpec;

class Plan extends Resource
{
    protected static $_uri_spec = null;

    public static function init()
    {
        self::$_uri_spec = new URISpec('plans', 'id', '/');
        self::$_registry->add(get_called_class());
    }

}
