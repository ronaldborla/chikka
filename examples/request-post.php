<?php

use Borla\Chikka\Support\Http\Http;

// Require autoload
require('../vendor/autoload.php');

// Create post
var_dump((new Http())->post('http://www.davaodirectory.com/login', [
  'auth'=> 'ronaldborla',
  'password'=> 'hitshit',
], [
  'Accept'=> 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Encoding'=> 'gzip, deflate',
  'Accept-Language'=> 'en-US,en;q=0.8',
  'Connection'=> 'keep-alive',
  'Content-Type'=> 'application/x-www-form-urlencoded',
  'User-Agent'=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.81 Safari/537.36',
])->headers);