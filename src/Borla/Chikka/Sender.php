<?php namespace Borla\Chikka;

use Borla\Chikka\Models\Config;
use Borla\Chikka\Models\Message;

use Borla\Chikka\Support\Http\Http;
use Borla\Chikka\Support\Loader;

use Borla\Chikka\Adapters\Config\ConfigInterface;
use Borla\Chikka\Adapters\Config\ConfigTrait;

/**
 * Message sender
 */

class Sender implements ConfigInterface {

  use ConfigTrait;

  /**
   * Set config
   */
  protected $config;

  /**
   * Http handler
   */
  protected $http;

  /** 
   * Constructor
   */
  function __construct(Config $config) {
    // Set config
    $this->setConfig($config);
  }

  /**
   * Send
   */
  public function send(Message $message) {
    // Return
    return $this->request($message->toSend())
      // Attach message
      ->attach('message', $message);
  }

  /**
   * Reply
   */
  public function reply(Message $message, $adjustCost = true) {
    // Return
    return $this->request($message->toReply($adjustCost))
      // Attach message
      ->attach('message', $message);
  }

  /**
   * Create a request
   */
  public function request(array $data) {
    // Create request
    $request = Loader::request($this->config, $data);
    // Return response
    return Loader::response(
      // Execute
      $this->getHttp()->post($this->getRequestUrl(), (string) $request)->json
      // Attach request
    )->attach('request', $request);
  }

  /**
   * Get http
   */
  protected function getHttp() {
    // If there's http
    if ($this->http) {
      // Return
      return $this->http;
    }
    // Create http
    return $this->http = new Http();
  }

  /**
   * Get request url
   */
  protected function getRequestUrl() {
    // Return
    return 'https://post.chikka.com/smsapi/request';
  }
  
}