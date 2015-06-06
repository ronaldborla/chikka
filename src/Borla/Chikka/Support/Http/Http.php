<?php namespace Borla\Chikka\Support\Http;

use Borla\Chikka\Support\Http\Curl;
use Borla\Chikka\Support\Http\Guzzle;
use Borla\Chikka\Support\Http\Response;

use Borla\Chikka\Exceptions\InvalidType;

/**
 * Handles Http requests
 */

class Http {

  /**
   * Client to use
   */
  protected $client;

  /**
   * Create post request
   * @return \Borla\Chikka\Support\Http\Response
   */
  public function post($url, $body, array $headers = array()) {
    // If body is array
    if (is_array($body)) {
      // Convert to string
      $body = http_build_query($body);
    }
    // Do post and get response
    $response = $this->getClient()->post($url, $body, $headers);
    // If not response
    if ( ! $response instanceof Response) {
      // Throw error
      throw new InvalidType('HttpInterface post method must return an instance of \Borla\Chikka\Support\Http\Response');
    }
    // Return response
    return $response;
  }

  /**
   * Get client
   */
  protected function getClient() {
    // If there's already a client
    if ($this->client) {
      // Use client
      return $this->client;
    }
    // Prioritize guzzle
    if (class_exists('\GuzzleHttp\Client')) {
      // Use guzzle
      return $this->client = new Guzzle();
    }
    // Otherwise, curl
    elseif (function_exists('curl_init')) {
      // Use curl
      return $this->client = new Curl();
    }
  }

}