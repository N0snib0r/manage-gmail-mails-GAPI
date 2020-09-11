<?php
include_once 'google-api-php-client-v2.7.0-PHP7.4/vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig(''); //JSON PENDIENTE GMAIL
$client->setScopes(Google_Service_Gmail::MAIL_GOOGLE_COM);

