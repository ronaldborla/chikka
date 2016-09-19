<?php namespace Borla\Chikka\Adapters\Attachment;

use Borla\Chikka\Base\Model;

/**
 * Attachment Trait
 */

trait AttachmentTrait {

  /**
   * Attach an object
   */
  public function attach($name, $object) {
    // Set attachment
    $this->attachments->$name = $object;
    // Return
    return $this;
  }

  /**
   * Get attachment
   */
  public function getAttachmentsAttribute() {
    // Return
    if ( ! isset($this->attributes['attachments'])) {
      // Create
      $this->attributes['attachments'] = new Model();
    }
    // Return
    return $this->attributes['attachments'];
  }
  
}