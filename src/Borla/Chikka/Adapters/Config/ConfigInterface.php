<?php namespace Borla\Chikka\Adapters\Config;

use Borla\Chikka\Models\Config;

/**
 * Config interface
 */

interface ConfigInterface {

  /**
   * Set config
   * @param \Borla\Chikka\ModelsConfig $config Configuration model
   */
  public function setConfig(Config $config);

  /**
   * Get config
   * @return \Borla\Chikka\Models\Config Configuration
   */
  public function getConfig();

}