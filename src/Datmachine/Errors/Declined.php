<?php

namespace Datmachine\Errors;

use Datmachine\Errors\Error;

class Declined extends Error
{
  public static $codes = array('authorization-failed');
}