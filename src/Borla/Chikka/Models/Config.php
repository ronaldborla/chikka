<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Exceptions\InvalidConfig;
use Borla\Chikka\Exceptions\MissingConfig;
use Borla\Chikka\Support\Utilities;

/**
 * Config
 */

class Config extends Model {

  /**
   * Constructor
   */
  function __construct(array $config) {
    // Required config
    $required = ['shortcode', 'client_id', 'secret_key'];
    // Loop
    foreach ($required as $key) {
      // If not set
      if ( ! isset($config[$key])) {
        // Throw error
        throw new MissingConfig('Missing configuration: ' . $key);
      }
      // Set it
      $this->$key = $config[$key];
    }
    // Load models
    if (isset($config['models'])) {
      // Set it
      $this->models = $config['models'];
    }
  }

  /**
   * When setting shortcode
   */
  protected function setShortcodeAttribute($value) {
    // Extract numerics only
    $shortcode = Utilities::extractNumerics($value);
    // If shortcode doesn't begin with 29290
    if (substr($shortcode, 0, 5) != '29290') {
      // Throw error
      throw new InvalidConfig('Shortcode must start with `29290`. `' . $shortcode . '` is given');
    }
    // Set shortcode
    return $shortcode;
  }

}