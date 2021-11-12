<?php
    include_once 'includes/functions.php';
    include_once __DIR__.'/../vendor/autoload.php';
    
    $rateLimitServer = [
        'host' => '127.0.0.1',
        'port' => 6379
    ];

    try{

        $adapter = new \PalePurple\RateLimit\Adapter\Predis(new \Predis\Client($rateLimitServer));
        $rateLimiter = new \PalePurple\RateLimit\RateLimit("UTAPI", 100, 3600, $adapter);
    
        $id = realIP();
        $count = $rateLimiter->getAllowance($id);
        if ($rateLimiter->check($id)) {
            
            echo time();

        }
    }catch(Exception $e) {
        die("An error occured");
    }

    ?>