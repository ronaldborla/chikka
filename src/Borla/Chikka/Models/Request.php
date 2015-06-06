<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Models\Config;

use Borla\Chikka\Adapters\Config\ConfigInterface;
use Borla\Chikka\Adapters\Config\ConfigTrait;

/**
 * Request model
 */

class Request extends Model implements ConfigInterface {

  use ConfigTrait;

  /**
   * Configuration
   */
  protected $config;

  /**
   * Constructor
   */
  function __construct(Config $config, array $data) {
    // Set config
    $this->setConfig($config);
    // Set attributes
    $this->setAttributes($data);
  }

  /**
   * To array
   */
  public function toArray() {
    // Get array
    $array = parent::toArray();
    // Insert config
    $array['shortcode'] = $this->config->shortcode;
    $array['client_id'] = $this->config->client_id;
    $array['secret_key'] = $this->config->secret_key;
    // Return
    return $array;
  }

  /**
   * To query
   */
  public function query() {
    // Return
    return http_build_query($this->toArray());
  }

  /**
   * To string
   */
  function __toString() {
    // Return query
    return $this->query();
  }

}