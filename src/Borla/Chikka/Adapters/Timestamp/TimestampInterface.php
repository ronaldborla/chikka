<?php namespace Borla\Chikka\Adapters\Timestamp;

/**
 * Timestamp interface
 */

interface TimestampInterface {

  /**
   * Set timestamp
   */
  function setTimestampAttribute($value);

  /**
   * Get timezone
   */
  function getTimezone();
  
}