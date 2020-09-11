<?php
include 'quickstart.php';

$client = getClient();

$service = new Google_Service_Gmail($client);
// $service = new Google_Service_Gmail_Message($client);
$usrId = 'robin.aslla.suc@gmail.com';
$messaId = '1740deddb3fd1261';

$result = getMessage($service, $usrId, $messaId);

if(!empty($result)){
    echo '<pre>';
    echo 'RESULTO NO VACIO' . var_dump($result);
    echo '</re>';
    echo '<br>';
}else {
    echo 'RESULT ESTA VACIO <br>';
}