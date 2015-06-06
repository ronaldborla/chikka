<?php namespace Borla\Chikka;

use Borla\Chikka\Models\Config;
use Borla\Chikka\Models\Message;

use Borla\Chikka\Adapters\Config\ConfigInterface;
use Borla\Chikka\Adapters\Config\ConfigTrait;
use Borla\Chikka\Support\Loader;
use Borla\Chikka\Support\Utilities;

use Borla\Chikka\Exceptions\InvalidConfig;

/**
 * Chikka API
 */

class Chikka implements ConfigInterface {

  use ConfigTrait;

  /** 
   * Configuration
   */
  protected $config;

  /**
   * Constructor
   * @param array|Config Array or Config
   */
  function __construct($config) {
    // If config is already a Config 
    if ($config instanceof Config) {
      // Set it directly
      $this->setConfig($config);
    }
    elseif (is_array($config)) {
      // Load as config
      $this->setConfig(new Config($config));
    }
    else {
      // Throw error
      throw new InvalidConfig('Configuration must be an array or an instance of \Borla\Chikka\Models\Config');
    }
  }

  /**
   * Send SMS
   * @param string|Mobile $mobile Mobile number can be string or an instance of \Borla\Chikka\Models\Mobile
   * @param string|Message $message Message can be string or an instance of \Borla\Chikka\Models\Message
   */
  public function send($mobile, $message, $messageId = null) {
    // Create message
    $message = $this->createMessage([
      'id'      => $messageId,
      'mobile'  => $mobile,
      'message' => $message,
    ]);
    // Return sender response
    return Loader::sender($this->config)->send($message);
  }

  /**
   * Receive
   */
  public function receive(array $post) {
    // Return receiver
    return Loader::receiver($this->config, $post);
  }

  /**
   * Reply
   */
  public function reply($requestId, $mobile, $message, $cost = 0, $messageId = null, $adjustCost = true) {
    // Create message
    $message = $this->createMessage([
      'id'        => $messageId,
      'mobile'    => $mobile,
      'message'   => $message,
      'request_id'=> $requestId,
      'cost'      => $cost,
    ]);
    // Return sender response
    return Loader::sender($this->config)->reply($message, $adjustCost);
  }

  /**
   * Reply to a message
   */
  public function replyTo(Message $message, $messageOrContent, $cost = 0, $messageId = null, $adjustCost = true) {
    // Return
    return $this->reply($message->id, $message->mobile, $messageOrContent, $cost, $messageId, $adjustCost);
  }

  /**
   * Create message
   */
  protected function createMessage($message) {
    // If instance of message
    if ($message instanceof Message) {
      // Return immediately
      return $message;
    }
    // If array
    elseif (is_array($message)) {
      // If there's message instance in message
      if (isset($message['message']) && $message['message'] instanceof Message) {
        // Get message and set attributes
        return $message['message']->setAttributes(Utilities::arrayExcept(['message'], $message));
      }
    }
    // Return
    return Loader::message($message);
  }

}