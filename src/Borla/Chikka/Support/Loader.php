<?php namespace Borla\Chikka\Support;

use Borla\Chikka\Models\Config;
use Borla\Chikka\Exceptions\InvalidClass;

/** 
 * Class loader
 */

class Loader {

  /** 
   * Static
   */
  public static function __callStatic($name, $args) {
    // Use get
    return static::instance($name, $args);
  }

  /**
   * Defaults
   */
  protected static function defaults() {
    // Static
    static $cache;
    // If there's any
    if ($cache) {
      // Return
      return $cache;
    }
    // Return
    return $cache = require(dirname(__FILE__) . '/../../../config/defaults.php');
  }

  /**
   * Get config
   */
  static function config($config) {
    // Cache
    static $cache;
    // If found
    if ($cache && array_key_exists($config, $cache)) {
      // Return
      return $cache[$config];
    }
    // If there's config
    if (function_exists('config')) {
      // Use this
      return $cache[$config] = config('chikka.' . $config);
    }
    // Or else
    else {
      // Load config from defaults
      $defaults = static::defaults();
      // Split by .
      $tree = explode('.', $config);
      // Loop through tree
      foreach ($tree as $branch) {
        // If not set
        if ( ! isset($defaults[$branch])) {
          // Return null
          return $cache[$config] = null;
        }
        // Set defaults
        $defaults = $defaults[$branch];
      }
      // Return
      return $cache[$config] = $defaults;
    }
  }

  /**
   * Get class
   */
  static function getClass($class) {
    // Cache
    static $cache;
    // If found
    if ($cache && array_key_exists($class, $cache)) {
      // Return
      return $cache[$class];
    }
    // Get class name
    $className = static::config($class);
    // If not found
    if ( ! $className) {
      // Attempt models
      $className = static::config('models.' . $class);
    }
    // If not found
    if ( ! $className) {
      // Throw error
      throw new InvalidClass('Class not set: ' . $class);
    }
    // Prepend \
    $className = '\\' . str_replace('/', '\\', trim($className, '/\\'));
    // If class doesn't exist
    if ( ! class_exists($className)) {
      // Invalid class
      throw new Exception('Class undefined: ' . $className);
    }
    // Return class name
    return $cache[$class] = $className;
  }

  /**
   * Instantiate class
   */
  static function instance($class, array $args = []) {
    // Get class name
    $class = static::getClass($class);
    // Count args
    switch (count($args)) {
      case 1:   return new $class($args[0]);
      case 2:   return new $class($args[0], $args[1]);
      case 3:   return new $class($args[0], $args[1], $args[2]);
      case 4:   return new $class($args[0], $args[1], $args[2], $args[3]);
      case 5:   return new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
      case 6:   return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
      case 7:   return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
      case 8:   return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
      case 9:   return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
      case 10:  return new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
    }
    // Return without args
    return new $class();
  }

  /**
   * Static Method
   */
  static function method($class, $method, array $args = []) {
    // Get class name
    $class = static::getClass($class);
    // Return
    return call_user_func_array(array($class, $method), $args);
  }

  /**
   * Get constant
   */
  static function constant($class, $name) {
    // Get class name
    $class = static::getClass($class);
    // Return
    return constant($class . '::' . $name);
  }

}