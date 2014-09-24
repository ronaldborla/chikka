<?php

  /**
   * Reply to a message
   * If you want to reply to a message later, instead of replying right away after receiving a message,
   * You can use this method
   */

  // Require Chikka SDK file
  require('../src/Chikka.php');

  // Set credentials
  $credentials = array(
    'client_id' => '64ab21656c8907868853122ec3b22ee0c10fa5ae18a3cd6077dcad9fdc7a9482',
    'secret_key'=> '2bd436acf77bcb7d0d77a6637961c6b3a53399b042535311a088c377cafd6ad7',
    'shortcode' => '29290001'
  );

  // Request ID is received from Chikka (when a user sends a message to your shortcode, 
  // Chikka will notify you through your "Message Receiver URL")
  $requestId = '128_alphanumeric_characters';
  // Mobile number and message
  // Mobile number can have the prefix +63, 63, or 0
  $mobileNumber = '09181234567';
  $message = 'Hello world';

  // Instantiate Chikka (passing credentials)
  $chikka = new Chikka($credentials);

  // To reply to a message, you need to pass on the `request_id` and `mobile_number`
  // Therefore, if you're planning to reply to a message later, you have to secure these 2 parameters
  $reply = $chikka->reply($requestId, $mobileNumber, $message, 2);

  // If you don't want to specify a `message_id` as a 5th parameter in the reply() function,
  // A `message_id` is automatically generated (16 digits)
  // You can retrieve the `message_id` through the following
  $messageId = $send->message->message_id;

  // Check if message was sent
  if ($send->success()) {

    echo 'Reply successfully sent';
  } else {
    // Print error message
    echo 'Reply not sent. ', $send->message;
  }