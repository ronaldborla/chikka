<?php namespace Borla\Chikka;

use Borla\Chikka\Models\Config;

use Borla\Chikka\Support\Loader;
use Borla\Chikka\Support\Utilities;

use Borla\Chikka\Adapters\Config\ConfigInterface;
use Borla\Chikka\Adapters\Config\ConfigTrait;

/**
 * Message/Notification Receiver
 */

class Receiver implements ConfigInterface {

  use ConfigTrait;

  /**
   * Configuration
   */
  protected $config;

  /**
   * Message
   */
  protected $message;

  /**
   * Notification
   */
  protected $notification;

  /**
   * @var bool Success
   */
  protected $success;

  /**
   * Constructor
   */
  function __construct(Config $config, array $post) {
    // Set config
    $this->setConfig($config);
    // If it's a message
    if ($this->isMessage($post)) {
      // Create new message
      $this->message = Loader::message($post);
      // If shortcode doesn't match
      if ($this->message->shortcode != $this->config->shortcode) {
        // Unset message
        unset($this->message);
      }
    }
    // If it's a notification
    if ($this->isNotification($post)) {
      // Create new notification
      $this->notification = Loader::notification($post);
      // If shortcode doesn't match
      if ($this->notification->shortcode != $this->config->shortcode) {
        // Unset notification
        unset($this->notification);
      }
    }
  }

  /**
   * To string
   */
  function __toString() {
    // Return response
    return $this->respond();
  }

  /**
   * Handle message
   */
  public function message($callback) {
    // If there's a message
    if ($this->hasMessage()) {
      // Execute callback passing the message
      $this->success = Utilities::executeCallback($callback, [
                        // First parameter is the message
                        $this->message,
                        // Second optional parameter is instance of Sender
                        Loader::sender($this->config), 
                      ]);
    }
    // Return self
    return $this;
  }

  /**
   * Handle notification
   */
  public function notification($callback) {
    // If there's a notification
    if ($this->hasNotification()) {
      // Execute callback passing the notification
      $this->success = Utilities::executeCallback($callback, [$this->notification]);
    }
    // Return self
    return $this;
  }

  /**
   * Get message
   */
  public function getMessage() {
    // Return message
    return $this->message;
  }

  /**
   * Get notification
   */
  public function getNotification() {
    // Return notification
    return $this->notification;
  }

  /**
   * Returns true when Receiver has received a message or notification from Chikka
   */
  public function hasReceived() {
    // Return
    return $this->hasMessage() || $this->hasNotification();
  }

  /**
   * Has message
   */
  public function hasMessage() {
    // Return
    return isset($this->message);
  }

  /** 
   * Has notification
   */
  public function hasNotification() {
    // Return
    return isset($this->notification);
  }

  /**
   * Check if POST received is a message
   */
  public function isMessage(array $post) {
    // Message keys
    $keys = ['message_type', 'mobile_number', 'shortcode', 'request_id', 'message', 'timestamp'];
    // Return
    return Utilities::arrayKeysExist($keys, $post);
  }

  /**
   * Check if POST received is a notification
   */
  public function isNotification(array $post) {
    // Message keys
    $keys = ['message_type', 'shortcode', 'message_id', 'status', 'credits_cost', 'timestamp'];
    // Return
    return Utilities::arrayKeysExist($keys, $post);
  }

  /**
   * Respond
   */
  public function respond() {
    // If success
    if ($this->success === true) {
      // Return 'Accepted'
      return 'Accepted';
    }
    // If not success
    if ($this->success === false) {
      // Return 'Error'
      return 'Error';
    }
    // Return empty
    return '';
  }

}