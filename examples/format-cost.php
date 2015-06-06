<?php

use Borla\Chikka\Models\Cost;

// Require autoload
require('../vendor/autoload.php');

// Adjust cost
var_dump((new Cost(2, '09087800765'))->toArray());