<?php

  /**
   * Receive SMS from Chikka server, and reply to SMS
   * Note: In your Chikka API Settings, you need to edit the "Message Receiver URL" and point it to this script
   * Example: http://website.com/chikka/receive_message_and_reply.php
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

  // Receive message
  $chikka->receiveMessage(function($message) {
    // The Chikka_Message object will be passed on as parameter in this callback
    // To reply, just use the reply() function for the Chikka_Message object
    // To specify the cost of message, enter the amount in float as a second parameter 
    // Amounts are automatically rounded off to its nearest ceiling valid cost
    // Example, if the network of the sender's mobile number is Smart or Globe, and you set 2 pesos as the cost
    // Since 2 pesos is not a valid cost for Smart or Globe, it will automatically be rounded off to 2.50 pesos
    $message->reply('Hello, this was your message: ' . $message->message, 2);
    // Return true to tell Chikka that we received and accepted the message
    return true;
  });