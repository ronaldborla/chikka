<?php namespace Borla\Chikka\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Chikka Facade
 * @see Borla\Chikka\Chikka
 */

class Chikka extends Facade {

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() { return 'chikka'; }

}