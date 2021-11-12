<?php 
    include_once 'includes/botconfig.php';
    include_once __DIR__.'/../vendor/autoload.php';

    var_dump($discord->guild->getGuildMember(['guild.id' => $guildid, 'user.id' => (int)758051326554013806]));  
    $test = json_encode($discord);
    echo $test;
?>