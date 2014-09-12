<?php

namespace Datmachine;

use Datmachine\Resource;
use \RESTful\URISpec;

/**
 * Represent a buyer or merchant within your marketplace.
 *
 * You create these using new Balanced\Customer.
 *
 * <code>
 * $customer = new Customer(array(
 *     "name" => "John Lee Hooker",
 *     "twitter" => "@balanced",
 *     "phone" => "(904) 555-1796",
 *     "meta" => array(
 *         "meta can store" => "any flat key/value data you like",
 *         "github" => "https://github.com/balanced",
 *         "more_additional_data" => 54.8
 *     ),
 *     "facebook" => "https://facebook.com/balanced",
 *     "address" => array(
 *         "city" => "San Francisco",
 *         "state" => "CA",
 *         "postal_code" => "94103",
 *         "line1" => "965 Mission St",
 *         "country_code" => "US"
 *     ),
 *     "business_name" => "Balanced",
 *     "ssn_last4" => "3209",
 *     "email" => $email_address,
 *     "ein" => "123456789"));
 * </code>
 */
class Customer extends Resource
{
    protected static $_uri_spec = null;

    public static function init()
    {
        self::$_uri_spec = new URISpec('customers', 'id', '/');
        self::$_registry->add(get_called_class());
    }

    
    public function createSubscription($plan_id)
    {
        return $this->subscriptions->create(array(
          'plan_id' => $plan_id
        ));
    }
    
    public function allSubscriptions()
    {
      $uri = $this->subscriptions->uri;
      
      $response = self::getClient()->get($uri);
     
      
      $objects = $response->body->subscriptions;
      $links = $response->body->links;
      
      $bar = array();
      
      foreach ($objects as $object){
        $foo = new \StdClass();
        $foo->subscriptions = $object;
        $foo->links = $links;
        $bar[] = new \Datmachine\Subscription($foo);
      }
        
      
      return $bar;

    }
      
    
    public function lastSubscription()
    {
      return end($this->allSubscriptions());

    }
    
}