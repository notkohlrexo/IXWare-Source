<?php
    if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}');
    include_once __DIR__.'/../../vendor/autoload.php';
    use RestCord\DiscordClient;

    //Update if discord is deleted & UPDATE BOT Tokens in settings.php if deleted
    try{
        $token = "bottoken";
        $discord = new DiscordClient(['token' => $token]);
        $guildid = (int)"816009969677238303";
        $membershipID = (int)"816035023119581235";  
        $registeredID = (int)"816036115240190032";
        $adminID = (int)"816034359588290593";
        $regularID = (int)"816034937899057212";
        $linkedChannel = (int)"816036519293747210";
        $logChannel = (int)"816035585075707965";
        $verificationChannel = (int)"816035597549961236";
        $ticketChannel = (int)"816035626674946148";
        $publicVerifyChannel = (int)"816035251922534420";
    }catch(Exception $e){
    }
?>