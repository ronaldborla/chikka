<?php namespace Borla\Chikka\Adapters\Attachment;

/**
 * Attachment interface
 */

interface AttachmentInterface {

  /**
   * Attach an object
   */
  function attach($name, $object);

  /**
   * Get attachment
   */
  function getAttachmentsAttribute();

}