<?php namespace Borla\Chikka\Support\Http;

use Borla\Chikka\Adapters\Http\HttpInterface;
use Borla\Chikka\Support\Http\Response;
use GuzzleHttp\Client;

/**
 * Use Guzzle
 */

class Guzzle implements HttpInterface {

  /**
   * Guzzle client
   */
  protected $client;

  /**
   * Post
   */
  public function post($url, $body, array $headers = array()) {
    // Execute post
    $response = $this->getClient()->post($url, [
      'body'    => $body,
      'headers' => $headers,
      'verify'  => dirname(__FILE__) . '/certs/ca-bundle.crt',
    ]);
    // Get headers
    $headers = $response->getHeaders();
    // Parse headers
    array_walk($headers, function(&$item, $key) {
      // Glue array into one
      $item = implode(';', $item);
    });
    // Return response
    return new Response((string) $response->getBody(), $response->getStatusCode(), $headers);
  }

  /**
   * Get client
   */
  protected function getClient() {
    // If there's client
    if ($this->client) {
      // Use it
      return $this->client;
    }
    // Use guzzle
    return $this->client = new Client();
  }

}