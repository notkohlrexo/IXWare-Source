<?php

include_once "includes/functions.php";
include_once __DIR__.'/../vendor/autoload.php';

$rateLimitServer = [
    'host' => '127.0.0.1',
    'port' => 6379
];

try{

    $adapter = new \PalePurple\RateLimit\Adapter\Predis(new \Predis\Client($rateLimitServer));
    $rateLimiter = new \PalePurple\RateLimit\RateLimit("encryptAPI", 500, 3600, $adapter);

    $id = realIP();
    $count = $rateLimiter->getAllowance($id);
    if ($rateLimiter->check($id)) {

        if(isset($_GET['code']) && !empty($_GET['code'])){
            echo decrypt($_GET["code"]);
        }
    }
}catch(Exception $e) {
    die("An error occured");
}
?>