<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Models\Cost;
use Borla\Chikka\Models\Mobile;

use Borla\Chikka\Adapters\Timestamp\TimestampInterface;
use Borla\Chikka\Adapters\Timestamp\TimestampTrait;

use Borla\Chikka\Exceptions\InvalidAttribute;

use Borla\Chikka\Support\Loader;
use Borla\Chikka\Support\Utilities;

/**
 * Message
 */

class Message extends Model implements TimestampInterface {

  use TimestampTrait;

  /**
   * Message types
   */
  const SEND      = 1;
  const INCOMING  = 2;
  const REPLY     = 3;

  /**
   * Constructor
   */
  function __construct($data) {
    // If not array
    if ( ! is_array($data)) {
      // Set message
      $data = [
        'message'=> $data
      ];
    }
    // Set attributes
    $this->setAttributes(Utilities::arrayExtract([
      'message_id',
      'id',
      'message_type',
      'type',
      'mobile_number',
      'mobile',
      'shortcode',
      'request_id',
      'request_cost',
      'cost',
      'message',
      'timestamp',
    ], $data));
  }

  /**
   * When an attribute is set
   */
  protected function onSetAttribute($name, $value) {
    // Listen to these attributes
    $listen = [
      'message_id'=> 'id',
      'message_type'=> 'type',
      'mobile_number'=> 'mobile',
      'request_cost'=> 'cost',
    ];
    // If set
    if (isset($listen[$name])) {
      // If not yet set
      if ( ! isset($this->{$listen[$name]})) {
        // Set corresponding attribute
        $this->{$listen[$name]} = $value;
      }
    }
  }

  /**
   * Types
   */
  static function types() {
    // Return
    return [
      static::SEND      => 'send',
      static::INCOMING  => 'incoming',
      static::REPLY     => 'reply',
    ];
  }

  /**
   * To send
   */
  public function toSend() {
    // Set type
    $this->type = static::SEND;
    // Refresh
    $this->refresh();
    // Return array
    return [
      'message_type'  => $this->message_type,
      'mobile_number' => $this->mobile_number,
      'message_id'    => $this->id,
      'message'       => $this->getMessage(),
    ];
  }

  /**
   * To reply
   */
  public function toReply($adjustCost = true) {
    // Set type
    $this->type = static::REPLY;
    // If to adjust
    if ($adjustCost) {
      // Do adjust
      $this->cost->adjust($this->mobile->carrier);
    }
    // Refresh
    $this->refresh();
    // Return array
    return [
      'message_type'  => $this->message_type,
      'mobile_number' => $this->mobile_number,
      'request_id'    => $this->request_id,
      'message_id'    => $this->id,
      'message'       => $this->getMessage(),
      'request_cost'  => $this->request_cost,
    ];
  }

  /**
   * Refresh
   */
  protected function refresh() {
    // Refresh other attributes
    $this->message_id = $this->id;
    $this->message_type = $this->getMessageType();
    $this->mobile_number = $this->mobile->intl();
    $this->request_cost = (string) $this->cost;
  }

  /**
   * Set id
   */
  protected function setIdAttribute($value) {
    // If there's no value
    if ( ! $value) {
      // Return md5 of current time
      return md5(microtime(true));
    }
    // Otherwise
    else {
      // Return only first 32
      return substr($value, 0, 32);
    }
  }

  /**
   * Set type
   */
  protected function setTypeAttribute($value) {
    // If null
    if ($value === null) {
      // Return null
      return null;
    }
    // If numeric
    if (is_int($value) || is_numeric($value)) {
      // If not valid
      if (isset(static::types()[$value])) {
        // Return int value
        return (int) $value;
      }
    }
    // Else
    else {
      // Find 
      if (($type = array_search(strtolower($value), static::types())) !== false) {
        // Return type
        return $type;
      }
    }
    // Throw error
    throw new InvalidAttribute('Invalid type value: ' . $value);
  }

  /**
   * Set mobile
   */
  protected function setMobileAttribute($value) {
    // If null
    if ($value === null) {
      // Return null
      return null;
    }
    // Return mobile
    return ($value instanceof Mobile) ? $value : Loader::mobile($value);
  }

  /**
   * Get cost
   */
  protected function getCostAttribute() {
    // If there's no cost
    if ( ! isset($this->attributes['cost'])) {
      // Create
      $this->attributes['cost'] = new Cost();
    }
    // Return cost
    return $this->attributes['cost'];
  }

  /**
   * Cost
   */
  protected function setCostAttribute($value) {
    // Return cost
    return ($value instanceof Cost) ? $value : Loader::cost($value);
  }

  /**
   * Get content
   */
  protected function getContentAttribute() {
    // Return message
    return $this->message;
  }

  /**
   * Set content
   */
  protected function setContentAttribute($value) {
    // Set message as well
    return $this->message = $value;
  }

  /**
   * Get message
   */
  public function getMessage() {
    // Return 
    return substr($this->message, 0, $this->getMaxLength());
  }

  /**
   * Get message type
   */
  public function getMessageType() {
    // Get message type
    $messageType = static::types()[$this->type];
    // If not incoming
    if ($this->type != static::INCOMING) {
      // Change to uppercase
      $messageType = strtoupper($messageType);
    }
    // Return
    return $messageType;
  }

  /**
   * Get max length
   */
  public function getMaxLength() {
    // Return
    return 420;
  }

}