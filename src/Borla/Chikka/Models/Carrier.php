<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Exceptions\UnknownCarrier;

/**
 * Mobile Carrier model
 */

class Carrier extends Model {

  /**
   * Networks
   */
  const SMART = 1;
  const GLOBE = 2;
  const SUN   = 3;

  /**
   * Constructor
   */
  function __construct($code) {
    // Set code
    $this->code = $code;
    // Set network
    $this->network = static::identifyNetwork($code);
    // If there's no network
    if ($this->network === false) {
      // Throw error
      throw new UnknownCarrier('Unknown carrier code: ' . $code);
    }
    // Set name
    $this->name = static::networks()[$this->network];
  }

  /**
   * Check if network matches
   */
  public function is($network) {
    // Return
    return $this->network == $network;
  }

  /**
   * Use name
   */
  function __toString() {
    // Return
    return $this->name;
  }

  /**
   * Get networks
   */
  static function networks() {
    // Return
    return [
      static::SMART=> 'Smart',
      static::GLOBE=> 'Globe',
      static::SUN  => 'Sun Cellular',
    ];
  }

  /**
   * Get codes
   */
  static function codes() {
    // Return codes
    return [
      // Smart codes
      static::SMART=> [813, 907, 908, 909, 910, 912, 918, 919, 920, 921, 928, 929, 930, 938, 939, 946, 947, 948, 949, 989, 998, 999],
      // Globe codes
      static::GLOBE=> [817, 905, 906, 915, 916, 917, 925, 923, 925, 926, 927, 928, 926, 927, 935, 936, 937, 994, 996, 997],
      // SUN codes
      static::SUN  => [922, 923, 925, 932, 933, 934, 942, 943],
    ];
  }

  /**
   * Identify network
   * @param string|int $carrierCode Carrier code parsed by Utilities::parseMobileNumber()
   */
  static function identifyNetwork($carrierCode) {
    // Loop through codes
    foreach (static::codes() as $network=> $codes) {
      // If found
      if (in_array($carrierCode, $codes)) {
        // Return network
        return $network;
      }
    }
    // Return false
    return false;
  }

}