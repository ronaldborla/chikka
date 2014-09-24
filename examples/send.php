<?php

  /**
   * Sending SMS with the Chikka API
   */

  // Require Chikka SDK file
  require('../src/Chikka.php');

  // Set credentials
  $credentials = array(
    'client_id' => 'your_client_id',
    'secret_key'=> 'your_secret_key',
    'shortcode' => '2929xxxx'
  );

  // Instantiate Chikka (passing credentials)
  $chikka = new Chikka($credentials);

  // Mobile number and message
  // Mobile number can have the prefix +63, 63, or 0
  $mobileNumber = '09181234567';
  $message = 'Hello world';

  // Send SMS ($send will contain the Chikka_Response object)
  $send = $chikka->send($mobileNumber, $message);

  // If you don't want to specify a `message_id` as a 3rd parameter in the send() function,
  // A `message_id` is automatically generated (16 digits)
  // You can retrieve the `message_id` through the following
  $messageId = $send->message->message_id;

  // Check if message was sent
  if ($send->success()) {

    echo 'Message successfully sent';
  } else {
    // Print error message
    echo 'Message not sent. ', $send->message;
  }