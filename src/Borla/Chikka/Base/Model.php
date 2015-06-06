<?php namespace Borla\Chikka\Base;

/** 
 * Base model
 */

class Model {

  /**
   * Attributes
   */
  protected $attributes = [];

  /**
   * Magic methods
   */
  public function __get($name) {
    // Get
    return $this->getAttribute($name);
  }

  public function __set($name, $value) {
    // Set
    $this->setAttribute($name, $value);
  }

  public function __isset($name) {
    // Return
    return isset($this->attributes[$name]);
  }

  public function __unset($name) {
    // Check first if exists
    if (array_key_exists($name, $this->attributes)) {
      // Unset
      unset($this->attributes[$name]);
    }
  }

  /**
   * Get attribute
   */
  public function getAttribute($name) {
    // Check first if mutator exists
    $mutator = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name))) . 'Attribute';
    // Check if method exists
    if (method_exists($this, $mutator)) {
      // Use it
      return $this->$mutator();
    }
    // If set
    if (array_key_exists($name, $this->attributes)) {
      // Return
      return $this->attributes[$name];
    }
  }

  /**
   * Get attributes
   */
  public function getAttributes() {
    // Return all
    return $this->attributes;
  }

  /**
   * Use set
   */
  public function set($nameOrAttributes, $value = false) {
    // Use set attributes
    if ( ! is_array($nameOrAttributes) && $value !== false) {
      // Set name in attributes
      $nameOrAttributes = [
        $nameOrAttributes=> $value
      ];
    }
    // Return with set attributes
    return $this->setAttributes($nameOrAttributes);
  }

  /** 
   * Set attribute
   */
  public function setAttribute($name, $value) {
    // Check first if mutator exists
    $mutator = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name))) . 'Attribute';
    // Check if method exists
    if (method_exists($this, $mutator)) {
      // Use it
      $value = call_user_func_array(array($this, $mutator), [$value]);
    }

    // If there's a callback
    if (method_exists($this, 'onSetAttribute')) {
      // Call
      call_user_func_array(array($this, 'onSetAttribute'), [$name, $value]);
    }

    // Set this attribute
    $this->attributes[$name] = $value;
  }

  /**
   * Set multiple attributes
   */
  public function setAttributes(array $attributes = []) {
    // Loop through attributes
    foreach ($attributes as $name=> $value) {
      // Set it individually
      $this->setAttribute($name, $value);
    }
    // Return self
    return $this;
  }

  /**
   * To array
   */
  public function toArray() {
    // Set array
    $array = [];
    // Loop through attributes
    foreach ($this->getAttributes() as $key=> $attribute) {
      // If attribute has toArray() method
      if (is_object($attribute) && method_exists($attribute, 'toArray')) {
        // Set array
        $array[$key] = $attribute->toArray();
      }
      // If can be converted to string
      elseif (is_object($attribute) && method_exists($attribute, '__toString')) {
        // Convert to string
        $array[$key] = (string) $attribute;
      }
      // Otherwise
      else {
        // Simply add to array
        $array[$key] = $attribute;
      }
    }
    // Return
    return $array;
  }

}