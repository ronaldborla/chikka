<?php namespace Borla\Chikka\Support\Http;

use Borla\Chikka\Adapters\Http\HttpInterface;
use Borla\Chikka\Support\Http\Response;

/**
 * Curl
 */

class Curl implements HttpInterface {

  /**
   * Post
   */
  public function post($url, $body, array $headers = array()) {
    // Create a post request
    return $this->request($url, [
      // Use post
      CURLOPT_POST            => true,
      CURLOPT_POSTFIELDS      => $body,
      CURLOPT_HTTPHEADER      => $headers,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_VERBOSE         => true,
      CURLOPT_HEADER          => true,
      CURLOPT_CAINFO          => dirname(__FILE__) . '/certs/ca-bundle.crt',
    ]);
  }

  /**
   * Create a request
   */
  public function request($url, array $options = array()) {
    // Initialize a curl
    $curl = curl_init($url);
    // If there's options
    if ($options) {
      // Set options
      curl_setopt_array($curl, $options);
    }
    // Execute
    $response = curl_exec($curl);
    // Get header size
    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    // Get body
    $body = substr($response, $headerSize);
    // Get status
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    // Parse header
    $headers = $this->parseHeaders(substr($response, 0, $headerSize));
    // Close curl
    curl_close($curl);
    // Return response
    return new Response($body, $status, $headers);
  }

  /**
   * Parse headers
   */
  protected function parseHeaders($rawHeaders) {
    // Set headers
    $headers = [];
    // Loop through lines
    foreach (explode("\n", $rawHeaders) as $line=> $header) {
      // If line is first or header is empty
      if ($line == 0 || ! trim($header)) {
        // Skip
        continue;
      }
      // Find first :
      $colon = strpos($header, ':');
      // Name
      $name = trim(substr($header, 0, $colon));
      // Value
      $value = trim(substr($header, $colon + 1));
      // Set header
      $headers[$name] = $value;
    }
    // Return
    return $headers;
  }

}