<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;

use Borla\Chikka\Support\Loader;
use Borla\Chikka\Support\Utilities;
use Borla\Chikka\Exceptions\UnknownCountry;

/**
 * Mobile number
 */

class Mobile extends Model {

  /**
   * Constructor
   */
  function __construct($number) {
    // Parse mobile number
    $mobileNumber = Utilities::parseMobileNumber($number);
    // If country code is 0
    if ($mobileNumber['countryCode'] == $this->getLocalCode() || empty($mobileNumber['countryCode'])) {
      // Use default
      $mobileNumber['countryCode'] = $this->getDefaultCountryCode();
    }
    // If country code is invalid
    if ( ! in_array($mobileNumber['countryCode'], $this->getSupportedCountryCodes())) {
      // Throw error
      throw new UnknownCountry('Unknown country code: ' . $mobileNumber['countryCode']);
    }
    // Set country
    $this->country = Loader::country($mobileNumber['countryCode']);
    // Set carrier
    $this->carrier = Loader::carrier($mobileNumber['carrierCode']);
    // Set number
    $this->number = $mobileNumber['number'];
  }

  /**
   * Short
   */
  public function short() {
    // Return
    return $this->format('cn');
  }

  /**
   * International number
   */
  public function intl() {
    // Return
    return $this->format('Ccn');
  }

  /**
   * Local number
   */
  public function local() {
    // Return
    return $this->format('lcn');
  }

  /**
   * Format number
   */
  public function format($format) {
    // C = Country Code
    // c = Carrier code
    // n = Number
    return str_replace(
            ['C', 'c', 'n', 'l'], 
            [$this->country->code, $this->carrier->code, $this->number, $this->getLocalCode()],
            $format);
  }

  /**
   * Pretty format
   */
  public function pretty() {
    // Return
    return $this->format('(+C) c-n');
  }

  /**
   * Use pretty format for toString
   */
  function __toString() {
    // Return
    return $this->pretty();
  }

  /**
   * Get local code
   */
  protected function getLocalCode() {
    // Return 0
    return '0';
  }

  /**
   * Get default country code
   */
  protected function getDefaultCountryCode() {
    // Return
    return Country::PHILIPPINES;
  }

  /**
   * Get supported countries
   */
  protected function getSupportedCountryCodes() {
    // Return codes
    return [
      Country::PHILIPPINES
    ];
  }

}