<?php

/**
 * Default configuration
 */

return [

  /** 
   * Receiver
   */
  'receiver'=> 'Borla\Chikka\Receiver',

  /** 
  * Sender
  */
  'sender'=> 'Borla\Chikka\Sender',

  /**
   * Custom models
   */
  'models'=> [

    /**
     * Handles carrier
     */
    'carrier'=> 'Borla\Chikka\Models\Carrier',

    /**
     * Handles cost
     */
    'cost'=> 'Borla\Chikka\Models\Cost',

    /**
     * Handles country
     */
    'country'=> 'Borla\Chikka\Models\Country',

    /**
     * Handles message
     */
    'message'=> 'Borla\Chikka\Models\Message',

    /**
     * Handles mobile
     */
    'mobile'=> 'Borla\Chikka\Models\Mobile',

    /**
     * Handles notification
     */
    'notification'=> 'Borla\Chikka\Models\Notification',

    /**
     * Handles request
     */
    'request'=> 'Borla\Chikka\Models\Request',

    /**
     * Handles response
     */
    'response'=> 'Borla\Chikka\Models\Response',

  ],

];