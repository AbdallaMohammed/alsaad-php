<?php

// Example of sending an sms
require_once '../vendor/autoload.php';

// Create client with username and password
$client = new Alsaad\Client([
    'username' => ALSAAD_USERNAME,
    'password' => ALSAAD_PASSWORD,
]);

// Send message using simple api params
$message = $client->message()->send([
    'to' => ALSAAD_TO,
    'from' => ALSAAD_SENDER,
    'text' => 'تجربة رسالة جديدة',
]);

echo "Status code: " . $message;

sleep(1);
