<?php

  /**
   * Sending SMS with the Chikka API
   */

  // Require Chikka SDK file
  require('../src/Chikka.php');

  // Set credentials
  $credentials = array(
    'client_id' => '64ab21656c8907868853122ec3b22ee0c10fa5ae18a3cd6077dcad9fdc7a9482',
    'secret_key'=> '2bd436acf77bcb7d0d77a6637961c6b3a53399b042535311a088c377cafd6ad7',
    'shortcode' => '29290001'
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