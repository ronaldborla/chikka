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
    'client_id' => '64ab21656c8907868853122ec3b22ee0c10fa5ae18a3cd6077dcad9fdc7a9482',
    'secret_key'=> '2bd436acf77bcb7d0d77a6637961c6b3a53399b042535311a088c377cafd6ad7',
    'shortcode' => '29290001'
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