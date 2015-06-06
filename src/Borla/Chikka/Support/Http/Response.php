<?php namespace Borla\Chikka\Support\Http;

use Borla\Chikka\Base\Model;

/**
 * Response
 */

class Response extends Model {

  /**
   * Constructor
   */
  function __construct($body, $status = 200, array $headers = array()) {
    // Set attributes
    $this->body = $body;
    $this->status = $status;
    $this->headers = $headers;
  }

  /**
   * To string
   */
  function __toString() {
    // Return body
    return $this->body;
  }

  /**
   * Get JSON attribute
   * @return array
   */
  function getJsonAttribute() {
    // Return
    return @json_decode($this->body, true);
  }

}