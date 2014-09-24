<?php

  /**
   * Chikka API SDK for PHP
   * @author Ronald Borla
   * @copyright September 23, 2014
   * @package Chikka
   * @version 1.0
   * @repository https://github.com/ronaldborla/chikka
   * @dependencies cURL library for PHP
   */

  /**
   * Chikka base object
   */
  class Chikka_Base
  {

    /**
     * Valid fields for this object
     */
    protected $fields = array();

    /**
     * Raw array data
     */
    protected $data = array();

    /**
     * Constructor
     */
    function __construct($data = array())
    {
      // Set data
      $this->setData($data);
    }

    /**
     * Get property
     */
    function __get($name)
    {
      // If set in data
      if (in_array($name, $this->fields)) {
        // Return
        return isset($this->data[$name]) ? $this->data[$name] : null;
      }
    }

    /**
     * Set property
     */
    function __set($name, $value)
    {
      // If valid field
      if (in_array($name, $this->fields)) {
        // Set
        $this->data[$name] = $value;
      }
    }

    /**
     * Set message data
     */
    private function setData($data)
    {
      // Set existing data to raw array data
      if (is_array($data) && $data) {
        // Loop through each data
        foreach ($data as $field=> $value) {
          // Set to data if valid
          if (in_array($field, $this->fields)) {
            // Set it
            $this->data[$field] = $value;
          }
        }
      }
      // Return 
      return $this;
    }

    /**
     * Create instance
     */
    static function instance($data = array())
    {
      // Return new instance
      return new static($data);
    }

  }

  /**
   * Chikka response class
   */
  class Chikka_Response extends Chikka_Base
  {

    /**
     * Valid fields for this object
     */
    protected $fields = array(
      // Status
      'status',
      // Message along the response
      'message',
      // Further description of the response
      'description'
    );

    /**
     * Message object
     */
    protected $message;

    /**
     * Get property
     */
    function __get($name)
    {
      if ($name == 'message') {
        // return message
        return $this->message;
      }
      // Return get
      return parent::__get($name);
    }

    /**
     * Check if response succeeded
     * @return bool True if success
     */
    function success()
    {
      // If status is 200, then succes
      return ($this->status == 200);
    }

    /**
     * Failed
     */
    function failed()
    {
      // Negation of success
      return !$this->success();
    }

    /**
     * Set message object
     */
    function setMessage(Chikka_Message $message)
    {
      // Set object
      $this->message = $message;
      // Return this object
      return $this;
    }

  }

  /**
   * Chikka data
   */
  class Chikka_Data extends Chikka_Base
  {

    /**
     * Chikka object
     */
    private $chikka;

    /**
     * Add parameter in constructor
     */
    function __construct(Chikka $chikka, $data = array())
    {
      // Call parent
      parent::__construct($data);
      // Set chikka
      $this->setChikka($chikka);
    }

    /**
     * Get property
     */
    function __get($name)
    {
      if ($name == 'chikka') {
        // return Chikka
        return $this->chikka;
      }
      // Return get
      return parent::__get($name);
    }

    /**
     * Set chikka object
     */
    function setChikka(Chikka $chikka)
    {
      // Set object
      $this->chikka = $chikka;
      // Return this object
      return $this;
    }

  }

  /**
   * Chikka message class
   */
  class Chikka_Message extends Chikka_Data
  {

    /**
     * Message max length
     */
    const MAX_LENGTH = 420;
    /**
     * ID max length
     */
    const ID_MAX_LENGTH = 32;

    /**
     * Valid fields for this object
     */
    protected $fields = array(
      // This is the request id as message is received
      'request_id', 
      // Message id
      'message_id',
      // Sender's or Recipient's mobile number
      'mobile_number',
      // Message received or to send
      'message',
      // Timestamp message was received
      'timestamp'
    );

    /**
     * Reply to a message
     * @param string $message Content of the message
     * @param float $cost The floating value of cost (0 for FREE)
     * @param string $messageId Message ID (if false, messageId is generated automatically)
     */
    function reply($message, $cost = 0, $messageId = false)
    {
      // If there's no request id
      if (!$this->request_id) {
        // Return false
        return false;
      }
      // Generate message id if not set
      $this->message_id = static::cleanupMessageId($messageId ? $messageId : Chikka::generateMessageId());

      // Set parameters
      $params = $this->getRequestParameters(array(
        'message_type'  => 'REPLY',
        'request_id'    => $this->request_id,
        'message_id'  => $this->message_id,
        'message'     => static::cleanupMessage($message),
        'request_cost'  => Chikka::getNearestValidCost($cost, $this->mobile_number)
      ));
      // Send request, and return response
      return $this->chikka->request($params)->setMessage($this);
    }

    /**
     * Send message
     */
    function send($message, $messageId = false)
    {
      // Generate message id if not set
      $this->message_id = static::cleanupMessageId($messageId ? $messageId : Chikka::generateMessageId());

      // Set parameters
      $params = $this->getRequestParameters(array(
        'message_type'=> 'SEND',
        'message_id'  => $this->message_id,
        'message'     => static::cleanupMessage($message)
      ));

      // Send request, and return response
      return $this->chikka->request($params)->setMessage($this);
    }

    /**
     * Get request params
     */
    function getRequestParameters($merge = array())
    {
      // Return basic request parameters + merge
      return $this->chikka->getRequestParameters(
        // Merge parameters
        $merge + array('mobile_number' => Chikka::cleanupMobileNumber($this->mobile_number)));
    }

    /**
     * Cleanup message id
     */
    static function cleanupMessageId($messageId)
    {
      // Trim message
      return substr($messageId, 0, static::ID_MAX_LENGTH);
    }

    /**
     * Cleanup message
     */
    static function cleanupMessage($message)
    {
      // Trim message
      return substr($message, 0, static::MAX_LENGTH);
    }

  }

  /**
   * Chikka notification
   */
  class Chikka_Notification extends Chikka_Data
  {

    /**
     * Valid fields for this object
     */
    protected $fields = array(
      // Message id
      'message_id',
      // Status
      'status',
      // Credits cost (amount deducted from account)
      'credits_cost',
      // Timestamp notification was received
      'timestamp'
    );

    /**
     * Get cost
     */
    function cost()
    {
      // Return cost
      return floatval($this->credits_cost);
    }

    /**
     * Check message status if sent
     * @return bool True if message was delivered successfully
     */
    function sent()
    {
      // True if status is 'SENT'
      return (strtoupper($this->status) == 'SENT');
    }

    /**
     * Check message status if failed
     * @return bool True if failed
     */
    function failed()
    {
      // Negate sent
      return !$this->sent();
    }

  }

  /**
   * Chikka class
   */
  class Chikka extends Chikka_Base
  {

    /**
     * Chikka API request URL
     */
    const REQUEST_URL = 'https://post.chikka.com/smsapi/request';

    /**
     * Set fields
     */
    protected $fields = array(
      // Client id
      'client_id',
      // Secret key
      'secret_key',
      // Short code
      'shortcode'
    );

    /**
     * Networks
     */
    const SMART = 'SMART';
    const GLOBE = 'GLOBE';
    const SUN   = 'SUN';

    /**
     * Receive data type
     */
    const MESSAGE       = 'MESSAGE';
    const NOTIFICATION  = 'NOTIFICATION';

    /**
     * Constructor
     * @param array $credentials Should contain (client_id, secret_key, shortcode)
     */
    function __construct($credentials)
    {
      // If fields doesn't exist
      if (!static::fieldsExist($credentials, $this->fields)) {
        // Error
        throw new Exception('Missing credentials: client_id, secret_key, or shortcode');
      }
      // Call parent
      parent::__construct($credentials);
    }

    /**
     * Receive a message
     */
    function receiveMessage($callback = null)
    {
      // Return receive
      return $this->receive($callback, array(
        // Set type
        'type'=> static::MESSAGE,
        // Message type
        'message_type'=> 'incoming',
        // Fields
        'fields'=> array(
          'message_type',
          'mobile_number',
          'shortcode',
          'request_id',
          'message',
          'timestamp'
        )
      ));
    }

    /**
     * Receive notification
     */
    function receiveNotification($callback = null)
    {
      // Return receive
      return $this->receive($callback, array(
        // Set type
        'type'=> static::NOTIFICATION,
        // Message type
        'message_type'=> 'outgoing',
        // Fields
        'fields'=> array(
          'message_type',
          'shortcode',
          'message_id',
          'status',
          'credits_cost',
          'timestamp'
        )
      ));
    }

    /**
     * Receive data
     */
    function receive($callback = null, $params = array())
    {
      // If any field doesn't exist
      if (!static::fieldsExist($_POST, $params['fields'])) {
        // Exit
        return false;
      }

      // Message type should be 'incoming'
      if ((strtolower($_POST['message_type']) == $params['message_type']) && 
          // Shortcode should match current shortcode
          ($_POST['shortcode'] == $this->shortcode)) {

        // Return object
        $return = false;

        // Switch type
        switch ($params['type']) {
          // Message
          case static::MESSAGE:
            // Declare message
            $return = new Chikka_Message($this, $_POST);
            break;
          // Notification
          case static::NOTIFICATION:
            // Declare message
            $return = new Chikka_Notification($this, $_POST);
            break;
        }

        // If there's a callback, call it
        if ($return && $callback && is_callable($callback)) {
          // Do call, pass $return as parameter
          $response = call_user_func_array($callback, array($return));

          // If response is true
          if ($response === true) {
            // Accept
            $this->accept();
          } elseif ($response === false) {
            // If false, error
            $this->error();
          }
        }

        // Return message
        return $return;
      }

      // Return false by default
      return false;
    }

    /**
     * Send Message
     */
    function send($mobileNumber, $message, $messageId = false)
    {
      // Create new message
      $chikkaMessage = new Chikka_Message($this, array(
        // Set mobile number
        'mobile_number' => $mobileNumber
      ));
      // Send and return response
      return $chikkaMessage->send($message, $messageId);
    }

    /**
     * Reply to a message
     */
    function reply($requestId, $mobileNumber, $message, $cost = 0, $messageId = false)
    {
      // Create message
      $chikkaMessage = new Chikka_Message($this, array(
        // Set request id
        'request_id'=> $requestId,
        // Set mobile number
        'mobile_number'=> $mobileNumber
      ));
      // Reply and return
      return $chikkaMessage->reply($message, $cost, $messageId);
    }

    /**
     * Accept received Message/Notification
     */
    function accept()
    {
      // Print 'Accepted'
      echo 'Accepted';
    }

    /**
     * Error/Reject received SMS/Notification
     */
    function error()
    {
      // Print 'Error'
      echo 'Error';
    }

    /**
     * Send request
     * @param array $data Array data
     * @return Chikka_Response Chikka response object
     */
    function request($data)
    {
      // Send post data
      $post = static::post(static::REQUEST_URL, $data);
      // Json decode
      $json = @json_decode($post, true);
      // Return with response
      return new Chikka_Response($json);
    }

    /**
     * Get request params
     */
    function getRequestParameters($merge = array())
    {
      // Return basic request parameters + merge
      return $merge + $this->data;
    }

    /**
     * Send post data using curl
     * @param string $url URL to send post data
     * @param array $data Array to encode as post data
     */
    static function post($url, $data)
    {
      // Initialize curl
      $curl = curl_init();
      // Set options
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_POST, 1);
      // Encode fields
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      // Return response
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      // SSL
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      // Execute
      $response = curl_exec($curl);
      // Close
      curl_close($curl);
      // Return response
      return $response;
    }

    /**
     * Generate message id
     */
    static function generateMessageId()
    {
      // Cache last timestamp and unique id
      static $lastTimestamp, $uniqueId;

      // Generate
      $newId = time();
      // If same as last timestamp
      if ($newId == $lastTimestamp) {
        // Increment unique id
        $uniqueId++;
      } else {
        // Reset unique id
        $uniqueId = 0;
      }

      // Set last timestamp as new id
      $lastTimestamp = $newId;
      // Append uniqueid left padded with 0s
      $newId .= str_pad($uniqueId, 6, '0', STR_PAD_LEFT);
      // Return
      return $newId;
    }

    /**
     * Get nearest valid cost according to mobile number
     */
    static function getNearestValidCost($amount, $mobileNumber)
    {
      // Get floatval
      $amount = floatval($amount);
      // Least amount should be 0
      if ($amount < 0) $amount = 0;
      // Get network of mobile number
      $network = static::getNetwork($mobileNumber);
      // Set cost to return
      $cost = '';

      // Loop through network costs
      foreach (static::getNetworkCosts($network) as $value) {
        // Set cost
        $cost = static::amountToCost($value);
        // If value is greater or equal to amount
        if ($value >= $amount) {
          // Return cost
          return $cost;
        }
      }
      // Return max cost
      return $cost;
    }

    /**
     * Get network possible costs
     */
    static function getNetworkCosts($network)
    {
      // Set costs
      $costs = array();
      // Select network
      switch ($network) {
        // If Sun, Smart, or Globe
        case static::SUN:
        case static::SMART:
        case static::GLOBE:
          // Set costs
          $costs = array(0, 1, 2.50, 5, 10, 15);
          // If Sun
          if ($network == static::SUN) {
            // Append 2
            $costs[] = 2;
            // Sort
            sort($costs);
          }
          break;
      }
      // Return costs
      return $costs;
    }

    /**
     * Convert an amount to cost
     */
    static function amountToCost($amount)
    {
      if ($amount <= 0) {
        // Return free
        return 'FREE';
      } else {
        // Return with prepended 'P'
        return 'P' . number_format($amount, 2, '.', '');
      }
    }

    /**
     * Get network of a mobile number
     * @param string $mobileNumber Mobile number
     */
    static function getNetwork($mobileNumber)
    {
      // If invalid number
      if (($mobileNumber = static::getLast10Digits($mobileNumber)) === false) {
        // Return false
        return false;
      }
      // Get first 3 digits
      $prefix = substr($mobileNumber, 0, 3);

      // Set prefixes
      $allPrefixes = array(
        // Smart networks
        static::SMART=> array(813,907,908,909,910,912,918,919,920,921,928,929,930,938,939,946,947,948,949,989,998,999),
        // Globe networks
        static::GLOBE=> array(817,905,906,915,916,917,925,923,925,926,927,928,926,927,935,936,937,994,996,997),
        // SUN networks
        static::SUN  => array(922,923,925,932,933,934,942,943)
      );

      // Loop through all prefixes
      foreach ($allPrefixes as $network=> $prefixes) {
        // If in prefixes
        if (in_array($prefix, $prefixes)) {
          // Return network
          return $network;
        }
      }

      // Return false by default
      return false;
    }

    /**
     * Cleanup mobile number
     */
    static function cleanupMobileNumber($mobileNumber)
    {
      // If valid mobile number
      if (($mobileNumber = static::getLast10Digits($mobileNumber)) !== false) {
        // Return with prepended country code
        return '63' . $mobileNumber;
      } else {
        // Return false
        return false;
      }
    }

    /**
     * Get last 10 digits of a number
     */
    static function getLast10Digits($number)
    {
      // Get length
      $len = strlen($number);
      // If length is less than 10, return false
      if ($len < 10) return false;
      // Get last 10 digits
      return substr($number, $len - 10);
    }

    /**
     * Check if fields exist in array
     */
    static function fieldsExist($array, $fields)
    {
      // Check if required post fields are set
      foreach ($fields as $field) {
        // Check if individual post is set
        if (!isset($array[$field])) {
          // Exit immediately
          return false;
        }
      }
      // Return true
      return true;
    }

  }