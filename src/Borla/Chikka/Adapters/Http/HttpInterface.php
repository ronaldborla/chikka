<?php namespace Borla\Chikka\Adapters\Http;

/** 
 * Http interface
 */

interface HttpInterface {

  /**
   * Request post
   * @return \Borla\Chikka\Support\Http\Response
   */
  public function post($url, $body, array $headers = array());

}