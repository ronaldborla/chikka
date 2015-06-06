<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Exceptions\InvalidCountry;

/**
 * Country
 */

class Country extends Model {

  /**
   * Countries
   */
  const PHILIPPINES = 63;

  /**
   * Constructor
   */
  function __construct($code) {
    // Set code
    $this->code = $code;
    // Set name
    if ( ! array_key_exists($this->code, static::names())) {
      // Throw error
      throw new InvalidCountry('Invalid country code: ' . $this->code);
    }
    // Set name
    $this->name = static::names()[$this->code];
  }

  /**
   * Match code
   */
  public function is($code) {
    // Return
    return $this->code == $code;
  }

  /**
   * Country names
   */
  static function names() {
    // Return
    return [
      static::PHILIPPINES=> 'Philippines',
    ];
  }

}