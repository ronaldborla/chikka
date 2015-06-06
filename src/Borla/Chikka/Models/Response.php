<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Adapters\Attachment\AttachmentInterface;
use Borla\Chikka\Adapters\Attachment\AttachmentTrait;

/**
 * Response model
 */

class Response extends Model implements AttachmentInterface {

  use AttachmentTrait;

  /**
   * Status
   */
  const ACCEPTED            = 200;
  const BAD_REQUEST         = 400;
  const UNAUTHORIZED        = 401;
  const METHOD_NOT_ALLOWED  = 403;
  const NOT_FOUND           = 404;
  const ERROR               = 500;

  /**
   * Constructor
   */
  function __construct(array $data) {
    // Set status
    $this->status = isset($data['status']) ? ((int) $data['status']) : static::ACCEPTED;
    // Set message
    $this->message = isset($data['message']) ? $data['message'] : static::messages()[static::ACCEPTED];
  }

  /**
   * Messages
   */
  static function messages() {
    // Return
    return [
      static::ACCEPTED            => 'ACCEPTED',
      static::BAD_REQUEST         => 'BAD REQUEST',
      static::UNAUTHORIZED        => 'UNAUTHORIZED',
      static::METHOD_NOT_ALLOWED  => 'METHOD NOT ALLOWED',
      static::NOT_FOUND           => 'NOT FOUND',
      static::ERROR               => 'ERROR',
    ];
  }

}