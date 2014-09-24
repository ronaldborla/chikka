Chikka API SDK for PHP
======

Chikka API PHP SDK (v.1.0)

This reposity contains a PHP SDK for the [Chikka API](https://api.chikka.com/)
To learn more about the API, please visit [https://api.chikka.com/docs/overview](https://api.chikka.com/docs/overview)

Usage
-----

To send an SMS
```php
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
$messageId = $send->msg->message_id;

// Check if message was sent
if ($send->success()) {

  echo 'Message successfully sent';
} else {
  // Print error message
  echo 'Message not sent. ', $send->message;
}
```

To receive a message and reply
Note: In your Chikka API Settings, you need to edit the "Message Receiver URL" and point it to this script
Example: http://website.com/chikka/receive_message_and_reply.php
```php
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
```

To learn more about the usage of the SDK regarding replying to a message independently and receiving notifications, please refer to the examples