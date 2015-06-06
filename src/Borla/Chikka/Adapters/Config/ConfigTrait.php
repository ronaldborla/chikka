<?php namespace Borla\Chikka\Adapters\Config;

use Borla\Chikka\Models\Config;

/**
 * Config trait
 */

trait ConfigTrait {
  
  /**
   * Set config
   */
  public function setConfig(Config $config) {
    // Set it
    $this->config = $config;
    // Return self
    return $this;
  }

  /**
   * Get config
   */
  public function getConfig() {
    // Return configuration
    return $this->config;
  }

}