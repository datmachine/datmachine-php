<?php

namespace Datmachine;

/**
 * Configurable settings.
 *
 *  You can either set these settings individually:
 *
 *  <code>
 *  \Balanced\Settings::$api_key = 'my-api-key-secret';
 *  </code>
 *
 *  or all at once:
 *
 *  <code>
 *  \Balanced\Settngs::configure(
 *      'https://api.balancedpayments.com',
 *      'my-api-key-secret'
 *      );
 *  </code>
 */
class Settings
{
    const VERSION = '1.0.0';

    public static $url_root = 'https://api.datmachine.co/sites/',
                  $api_key = null,
                  $agent = 'datmachine-php',
                  $version = Settings::VERSION,
                  $accept = 'application/json',
                  $site_id = null;

    /**
     * Configure all settings.
     *
     * @param string url_root The root (schema://hostname[:port]) to use when constructing api URLs.
     * @param string api_key The api key secret to use for authenticating when talking to the api. If null then api usage is limited to uauthenticated endpoints.
     */
    public static function configure($site_id, $api_key)
    {
      if (is_null(self::$site_id)){
        self::$site_id = $site_id;
        self::$url_root = self::$url_root . self::$site_id;
      }
      self::$api_key = $api_key;	      
    }
}
