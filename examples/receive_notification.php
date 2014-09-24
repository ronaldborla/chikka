<?php

  /**
   * Receive notification from Chikka server
   * Note: In your Chikka API Settings, you need to edit the "Notification Receiver URL" and point it to this script
   * Example: http://website.com/chikka/receive_notification.php
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
  $chikka->receiveNotification(function($notification) {
    // The Chikka_Notification object will be passed on as parameter in this callback
    if ($notification->sent()) {
      // Get message id
      $messageId = $notification->message_id;
      // Get cost here
      $cost = $notification->cost();

      // Do whatever you want about the information above
    }
    // Return true to tell Chikka that we received and accepted the message
    return true;
  });