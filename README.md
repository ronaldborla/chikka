# Chikka API SDK for PHP
## Chikka API PHP SDK (v.2.1.0)

This reposity contains a PHP SDK for the [Chikka API](https://api.chikka.com/).
Chikka is an SMS service for local use in Philippines. It supports 3 mobile networks: Globe, Smart, and Sun Cellular.
To learn more about the API, please visit [https://api.chikka.com/docs/overview](https://api.chikka.com/docs/overview)

## Installation
### Using Composer

Add the following lines in your `composer.json` file
```
"require": {
    "ronaldborla/chikka": "~2.1.0"
},
```

Although optional, this package will automatically use [Guzzle](https://github.com/guzzle/guzzle) if it is included via Composer

### To use with Guzzle via Composer

```
"require": {
    "guzzlehttp/guzzle": "~5.0",
    "ronaldborla/chikka": "~2.1.0"
},
```

### Laravel 5

To use this package in Laravel 5, you need to add its service provider in `/config/app.php`, add the following line under `'providers'`:

```
'providers'=> [
    ...
    'Borla\Chikka\Service',
    ...
],
```

Then under `'aliases'`:

```
'aliases'=> [
    ...
    'Chikka'=> 'Borla\Chikka\Support\Facades\Chikka',
    ...
],
```

### Configuration

To setup a configuration for Chikka in Laravel 5, you need to publish the configuration by running the following command in your terminal (you need to `cd` in your current project's root directory):

```
php artisan vendor:publish
```

After executing the command above, a configuration file will be available under `'/config/chikka.php'`. The configuration file will contain the following:

```
return [
    // Shortcode to use
    'shortcode'=> 'YOUR_SHORTCODE',
    // Client ID
    'client_id'=> 'YOUR_CLIENT_ID',
    // Secret key
    'secret_key'=> 'YOUR_SECRET_KEY',
];
```

You need to change these according to your Chikka API account. If you do not have an account yet for Chikka API, you can sign up at [https://api.chikka.com/](https://api.chikka.com/)

## Usage
### Sending an SMS

First, you need to initialize Chikka

```
// Use namespace
use Borla\Chikka\Chikka;

// Require autoload generated by Composer
require('./vendor/autoload.php');

// Set configuration
$config = [
    'shortcode'=> '29290XXXX',
    'client_id'=> 'YOUR_CLIENT_ID',
    'secret_key'=> 'YOUR_SECRET_KEY',
];

// Create Chikka object
$chikka = new Chikka($config);
```

When you've already created a Chikka object, you can use `send()` to send SMS

```
// Mobile number of receiver and message to send
$mobile = '09081234567';
$message = 'Hello world';

// Send SMS
$chikka->send($mobile, $message);
```

Later, you might need to track down the message you just sent. You will need to retrieve the id of the message. The `\Borla\Chikka\Models\Message` object that is sent by the code above will automatically be attached to the `\Borla\Chikka\Models\Response` object returned by the `send()` function. Therefore, to retrieve the message id, you can do the following instead:

```
$mobile = '09081234567';
$message = 'Hello world';

// Send SMS and retrieve response
$response = $chikka->send($mobile, $message);

// Get message id
$messageId = $response->attachments->message->id;
```

The Chikka API naturally requires a unique 32-character `message_id` when you send an SMS, but this package automatically creates it for you. If you need to specify your own message id, you can pass a third parameter when calling the `send()` function

```
$mobile = '09081234567';
$message = 'Hello world';
$messageId = 'UNIQUE_32-CHARACTER_MESSAGE_ID';

// Send SMS
$chikka->send($mobile, $message, $messageId);
```

### Sending an SMS in Laravel 5

Insure first that you followed the Installation and Configuration steps as mentioned above and sending an SMS in Laravel 5 will be a lot simpler. All you need to do is the following:

```
// Mobile number of receiver and message to send
$mobile = '09081234567';
$message = 'Hello world';

// Send SMS
Chikka::send($mobile, $message);
```

Take note that you don't need to create an instance of the `\Borla\Chikka\Chikka` object. Laravel will automatically do that for you. With the help of `Facade`, you can quickly access Chikka like the example provided above

### Receiving an SMS

To receive an SMS, you will need to setup a receiver URL in your server that Chikka will use as a callback. As soon as your account in Chikka API will receive an SMS from a mobile phone user, Chikka will notify you by sending POST data to your receiver URL. This package will interpret the `$_POST` variable in your PHP script and perform the necessary steps in order to help you simplify your code

First, you need to create a PHP receiver file. Let's call it `receiver.php`. Upload it your server, and place it anywhere publicly accessible. For example: `/website.com/public/sms/receiver.php`. Suppose you can access this receiver at `http://website.com/sms/receiver.php`, then this will be your Receiver URL. You will then need to configure your Chikka API account settings. Go to your Chikka API settings page at [https://api.chikka.com/api/settings](https://api.chikka.com/api/settings). Under "SMS", click "Edit". Paste the URL to your receiver under "Message Receiver URL" (you can also set the "Notification Receiver URL" the same as the "Message Receiver URL", but since we're setting up receiving SMS now, then we can skip it for later)

When you're already set up, as soon as your Chikka API account will receive an SMS, the POST data will be forwarded to this PHP receiver. To process this POST data, edit your `receiver.php` file with the following:

```
// Receive POST data from Chikka
echo $chikka->receive($_POST)
    // Process message
    ->message(function($message) {
        // Do whatever you want to do with the message
        $content = $message->content;
        $sender = $message->mobile;
        // Return true to tell Chikka that you have successfully received the message
        return true;
    });
```

Let's dissect the code above line by line. In the first line, we use the `receive()` function to receive data. In most cases, since Chikka puts the message data in POST, you can access it through PHP's `$_POST` variable. Pass this as a parameter for `receive()`

This function will return an instance of `\Borla\Chikka\Receiver`. The `Receiver` object will automatically detect whether your server has just received a message or a notification from Chikka. If this object receives a message, you can call its `message()` function and pass a callback which will then allow you to process the message as shown in the second line above

The `message()` function accepts a callback function as the first parameter. If the `Receiver` received a message from Chikka, it will execute your callback function and pass a `\Borla\Chikka\Models\Message` object containing the information of the message sent to your Chikka API account. Your callback must return `true` to let Chikka know that you have successfully received the message. If you set it to return `false` instead, then Chikka will treat it as an error and will attempt to resend the POST data up to 3 times to your receiver URL

You can acquire the message content by accessing the `\Borla\Chikka\Models\Message` object's `message` or `content` attribute (`->content` is an alias for `->message`). To acquire the mobile number of the sender, you can access the `mobile` attribute. `->mobile` returns a `\Borla\Chikka\Models\Mobile` object which contains information about the mobile number used (including the country code and carrier)

### Receiving and replying to an SMS

You learned that the `message()` function in the `\Borla\Chikka\Receiver` object as discussed previously accepts a callback function, and passes a `\Borla\Chikka\Models\Message` as the first parameter. It also passes a `\Borla\Chikka\Sender` object as the second parameter. This `Sender` object is responsible for replying to an SMS. This is also the object that sends an SMS as discussed previously under "**Sending an SMS**"

You can use the `reply()` function of the `Sender` object to reply to a message:

```
// Receive POST data from Chikka
echo $chikka->receive($_POST)
    // Process message
    ->message(function($message, $sender) {
        // Get message content first
        $content = $message->content;
        
        // Set new content for replying to message
        $message->content = 'Hello to you, too';
        // Set message id as null (to force the Message object to generate a new message id)
        $message->id = null;
        // Send reply
        $response = $sender->reply($message);
        
        // New message id
        $messageId = $response->attachments->message->id;
        // Return true to tell Chikka that you have successfully received the message
        return true;
    });
```

Notice above that we now have `$sender` as the second parameter in our callback function. This is the `Sender` object that we need in order to execute the `reply()` function. The `reply()` function simply accepts a `Message` object as the first parameter. Since the `$message` passed on to the callback function already contains the mobile number of the sender as well as the unique `request id` from Chikka, then the `Sender` object will know where to send the reply. Take note that as per Chikka API specification, in order to identify the message to reply to, you will have to supply the `request_id` sent along in the POST data from Chikka

### Receiving and replying to an SMS in Laravel 5

You can setup a receiver URL for your server by setting it up as a `Route`. In your `/app/Http/routes.php` file:

```
// Add this to your routes
Route::post('/sms/receiver', 'SmsController@receiver');
```

Now the route above can be accessed at `http://website.com/sms/receiver` which you will need to configure in your Chikka API settings. In your `/app/Http/Controllers/SmsController.php` file:

```
<?php namespace App\Http\Controllers;

use Controller as BaseController;
use Borla\Chikka\Chikka;
use Input;

class SmsController extends BaseController {

    /**
     * The receiver
     */
    public function receiver() {
        // Return response as string
        return (string) Chikka::receive(Input::all())
            // Receive message
            ->message(array($this, 'processMessage'));
    }
    
    /**
     * Process message
     * @param Borla\Chikka\Models\Message $message The Message object
     * @param Borla\Chikka\Sender $sender The Sender object
     * @return bool
     */
    private function processMessage($message, $sender) {
        // Get message content
        $content = $message->content;
        
        // Set new content for replying to message
        $message->content = 'Hello to you, too';
        // Set message id as null (to force the Message object to generate a new message id)
        $message->id = null;
        // Send reply
        $response = $sender->reply($message);
    
        // New message id
        $messageId = $response->attachments->message->id;
        // Return true to tell Chikka that you have successfully received the message
        return true;
    }

}
```

### Mobile number format

Technically, Chikka will accept a mobile number in its international format: `639*********` (e.g. `639081234567`), but this package automatically parses any mobile number. So you can input any format you want (e.g. `(+63)908-1234-567`, `09081234567`, `9081234567`, `0908-123-4567`). It doesn't matter if it contains any non-numeric characters, it will still be recognized, for as long as it has a valid mobile carrier prefix (Globe, Smart, or Sun Cellular). To get the list of supported mobile carrier prefixes, you can open the `\Borla\Chikka\Models\Carrier` model, under the `codes()` static function

Once a mobile number has been instantiated as a `\Borla\Chikka\Models\Mobile` object, then it can be converted into any of the following formats:

```
use Borla\Chikka\Models\Mobile;

// Instantiate a Mobile object
$mobile = new Mobile('09081234567');

// Get short format: 9081234567
$short = $mobile->short();

// Get international format: 639081234567
$intl = $mobile->intl();

// Get local format: 09087800765
$local = $mobile->local();

// Get pretty format: (+63) 908-1234567
$pretty = $mobile->pretty();
// Or simply convert to string
$pretty = (string) $mobile;
```

## Receiving a notification

The `Receiver` object is still responsible in processing a notification from Chikka the same way it receives a message. It is done very similarly with the `message()` function, but this time, it uses the `notification()` function

The `notification()` function of the `Receiver` object also accepts a callback function, where a `\Borla\Chikka\Models\Notification` object is passed as a first a parameter:

```
// Receive POST data from Chikka
echo $chikka->receive($_POST)
    // Process notification
    ->notification(function(notification) {
        // Do whatever you want to do with the notification
        // When the message sent failed
        if ($notification->failed()) {
            ...
        }
        // Or when it succeeded
        elseif ($notification->sent()) {
            ...
        }
        // Get credits cost
        $credits = $notification->credits;
        
        // Return true to tell Chikka that you have successfully received the message
        return true;
    });
```

## Receiving a notification in Laravel 5

Since we have previously set up a `Route` for our receiver, we can simply modify our `receiver()` function in the `SmsController` so that it can also process received notifications:

```
    /**
     * The receiver
     */
    public function receiver() {
        // Return response as string
        return (string) Chikka::receive(Input::all())
            // Receive message
            ->message(array($this, 'processMessage'))
            // Receve notification
            ->notification(array($this, 'processNotification'));
    }
    
    ...
    private function processMessage()
    ...
    
    /**
     * Add a new function which can process the notification
     */
    private function processNotification($notification) {
        // Do whatever you want to do with the notification
        // When the message sent failed
        if ($notification->failed()) {
            ...
        }
        // Or when it succeeded
        elseif ($notification->sent()) {
            ...
        }
        // Get credits cost
        $credits = $notification->credits;
        
        // Return true to tell Chikka that you have successfully received the message
        return true;
    }
    
```

Notice that we only added a `->notification()` call after the `->message()` function call under the `receiver()` function. This makes receiving of messages and notifications a lot simpler. The `message()` and `notification()` function of the `Receiver` object returns itself to allow for method chaining