<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Models\Carrier;
use Borla\Chikka\Models\Mobile;

use Borla\Chikka\Support\Loader;
use Borla\Chikka\Support\Utilities;

use Borla\Chikka\Exceptions\MissingCarrier;

/**
 * Cost object
 */

class Cost extends Model {

  /**
   * Constructor
   * @param int|float|string $amount Amount of cost
   * @param int|string|Mobile|Carrier $mobileOrCarrier Can be mobile number or instance of Mobile or Carrier
   */
  function __construct($amount = 0, $mobileOrCarrier = null) {
    // Set amount and original
    $this->amount = $this->original = $this->cleanAmount($amount);
    // If there's mobile
    if ($mobileOrCarrier !== null) {
      // Fix
      $this->fix($mobileOrCarrier);
    }
  }

  /**
   * To string
   */
  function __toString() {
    // Use value
    return $this->value();
  }

  /**
   * Zero
   */
  public function getZeroFormat() {
    // Return
    return 'FREE';
  }

  /**
   * Get format
   */
  public function getNonZeroFormat() {
    // Return
    return 'PN';
  }

  /**
   * Format
   */
  public function format($format) {
    // Return
    return str_replace(
            ['N', 'n'],
            [number_format($this->amount, 2), $this->amount],
            $format);
  }

  /**
   * Get value
   */
  public function value() {
    // Return
    return $this->format(($this->amount == 0) ? $this->getZeroFormat() : $this->getNonZeroFormat());
  }

  /** 
   * Adjust cost amount for a given carrier
   */
  public function adjust(Carrier $carrier = null) {
    // If there's carrier
    if ($carrier !== null) {
      // Set carrier
      $this->carrier = $carrier;
    }
    // If there's no carrier
    if ( ! isset($this->carrier)) {
      // Throw error
      throw new MissingCarrier('Carrier is required to adjust cost');
    }
    // Get costs
    $costs = $this->getPossibleCostsPerCarrier()[$this->carrier->network];
    // Loop through costs per carrier
    foreach ($costs as $i=> $cost) {
      // If cost equals
      if ($this->amount == $cost) {
        // Quit
        break;
      }
      // If amount is less than cost
      if ($this->amount < $cost || ! isset($costs[$i + 1])) {
        // Use previous
        $this->amount = $cost;
        // Break
        break;
      }
    }
    // Return self
    return $this;
  }

  /**
   * Fix cost by mobile or carrier
   * @param int|string|Mobile|Carrier $mobileOrCarrier Can be mobile number or instance of Mobile or Carrier
   */
  public function fix($mobileOrCarrier) {
    // If instance of carrier
    if ($mobileOrCarrier instanceof Carrier) {
      // Use
      $this->carrier = $mobileOrCarrier;
    }
    // If instance of mobile
    elseif ($mobileOrCarrier instanceof Mobile) {
      // Get carrier
      $this->carrier = $mobileOrCarrier->carrier;
    }
    // Else 
    else {
      // Get carrier
      $this->carrier =  Loader::mobile($mobileOrCarrier)->carrier;
    }
    // Adjust and return
    return $this->adjust();
  }

  /**
   * Get carrier possible costs
   */
  protected function getPossibleCostsPerCarrier() {
    // Return
    return [
      // In ascending order
      Carrier::SMART=> [0, 1, 2.5, 5, 10, 15],
      Carrier::GLOBE=> [0, 1, 2.5, 5, 10, 15],
      Carrier::SUN  => [0],
    ];
  }

  /**
   * Cleanup number
   */
  protected function cleanAmount($amount) {
    // Extract amount
    return (float) Utilities::extractNumerics($amount, ['.']);
  }

}