<?php namespace Borla\Chikka\Support;

use Borla\Chikka\Exceptions\InvalidCallback;

/**
 * Utilities
 */

class Utilities {

  /**
   * Check if a character is a digit
   */
  static function isDigit($char) {
    // Get ascii value
    $ascii = ord($char);
    // Return
    return ($ascii >= 48) && ($ascii <= 57);
  }

  /**
   * Extract numeric
   * @return string The numeric string
   */
  static function extractNumerics($string, array $include = []) {
    // Make sure it's string
    $string = (string) $string;
    // Set numerics
    $numerics = '';
    // Get length
    $len = strlen($string);
    // Loop through string
    for ($i = 0; $i < $len; $i++) {
      // If digit
      if (static::isDigit($string[$i])) {
        // Append to numerics
        $numerics .= $string[$i];
      }
      // If decimal
      if ($include && in_array($string[$i], $include)) {
        // Append to numerics
        $numerics .= $string[$i];
      }
    }
    // Return numerics
    return $numerics;
  }

  /**
   * Parse mobile number
   */
  static function parseMobileNumber($number) {
    // Extract numerics
    $numerics = static::extractNumerics($number);
    // Set country code
    $countryCode = '';
    // Get last 10 digits
    $short = static::right($numerics, 10, $countryCode);
    // Set carrier code
    $carriercode = '';
    // Get number
    $number = static::right($short, 7, $carrierCode);
    // Return
    return compact('countryCode', 'carrierCode', 'number');
  }

  /**
   * Get substr
   */
  static function right($str, $len, &$left = null) {
    // Get length of string
    $strlen = strlen($str);
    // Get start
    $start = $strlen - $len;
    // If start is less than 0
    if ($start < 0) {
      // Set to 0
      $start = 0;
    }
    // Set left
    $left = substr($str, 0, $start);
    // Return
    return substr($str, $start);
  }

  /**
   * Check if all array keys exist
   */
  static function arrayKeysExist(array $keys, array $array) {
    // Loop through keys
    foreach ($keys as $key) {
      // If doesn't exist
      if ( ! array_key_exists($key, $array)) {
        // Return false
        return false;
      }
    }
    // Return true by default
    return true;
  }

  /**
   * Extract array from array with keys
   */
  static function arrayExtract(array $keys, array $array, array $callbacks = []) {
    // Set extracted
    $extracted = [];
    // Loop through keys
    foreach ($keys as $key) {
      // Add to extracted, only if it exists
      if (array_key_exists($key, $array)) {
        // Set item
        $item = $array[$key];
        // If there's a callback
        if (isset($callbacks[$key]) && is_callable($callbacks[$key])) {
          // Call it
          $item = call_user_func_array($callbacks[$key], [$item]);
        }
        // Add to extracted
        $extracted[$key] = $item;
      }
    }
    // Return extracted
    return $extracted;
  }

  /**
   * Except
   */
  static function arrayExcept(array $keys, array $array) {
    // Except
    $except = [];
    // Loop through array
    foreach ($array as $key=> $item) {
      // If key doesn't exist
      if ( ! in_array($key, $keys)) {
        // Add to except
        $except[$key] = $item;
      }
    }
    // Return
    return $except;
  }

  /**
   * Execute callback
   */
  static function executeCallback($callback, array $params = array()) {
    // If callback has @
    if (is_string($callback) && strpos($callback, '@') !== false) {
      // Split
      $arrCallback = explode('@', $callback);
      // Set object
      $object = class_exists($arrCallback[0]) ? (new $arrCallback[0]()) : null;
      // Set method
      $method = isset($arrCallback[0]) ? $arrCallback[0] : null;
      // Set callback
      $callback = array($object, $method);
    }
    // If not callable
    if ( ! is_callable($callback)) {
      // Throw error
      throw new InvalidCallback('Callback must be callable');
    }
    // Execute and return
    return call_user_func_array($callback, $params);
  }

}