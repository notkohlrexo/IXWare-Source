<?php
    @session_start();
    require_once 'functions.php';
    
    if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}');

    //--Getting columns--//
    //$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $registrationDate = pdoQuery($db, "SELECT `registrationDate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $banReason = pdoQuery($db, "SELECT `banreason` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $checkExist = pdoQuery($db, "SELECT COUNT(*) FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
    $getToken = pdoQuery($db, "SELECT `token` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $subEndDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $subscription = pdoQuery($db, "SELECT `subscription` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $fullBanned = pdoQuery($db, "SELECT `fullBanned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
    $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $expire = pdoQuery($db, "SELECT `expire` FROM `used_licenses` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

    
    // if($getRank != "Admin"){
        
    //     session_regenerate_id(true);
    //     unset($_SESSION['username']);
    //     unset($_SESSION['ID']);
    //     session_destroy();
    //     header("location: login");
    //     exit();
    // }


    if($banned != 0){
        $date = new DateTime($banned);
        $now = new DateTime();
        
        if($date < $now) {
    
          $PDOS = $db -> prepare('UPDATE `users` SET `banned` = :ban WHERE `id` = :id');
          $PDOS -> execute(array(':ban' => 0, ':id' => htmlspecialchars($_SESSION['ID'])));
        }
    }

    if($subscription == "Monthly" || $subscription == "3Months"){

        $date = new DateTime($subEndDate);
        $now = new DateTime();
        
        if($date < $now) {
            //sub expired
            $PDOUpdate = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
            $PDOUpdate -> execute(array(':sub' => '0', ':id' => htmlspecialchars($_SESSION['ID'])));

            $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subscription` = :sub WHERE `id` = :id');
            $PDOUpdate1 -> execute(array(':sub' => 'None', ':id' => htmlspecialchars($_SESSION['ID'])));

            $PDOUpdate2 = $db -> prepare('UPDATE `users` SET `rank` = :rank WHERE `id` = :id');
            $PDOUpdate2 -> execute(array(':rank' => 'User', ':id' => htmlspecialchars($_SESSION['ID'])));

            $currentDiscord = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
            if($currentDiscord != 0){
        
              try{
                $discord->guild->removeGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$currentDiscord, 'role.id' => $membershipID]);   
              }catch(Exception $e){}
            }
        }
    }
    if($fullBanned == 1){

        session_regenerate_id(true);
        unset($_SESSION['username']);
        unset($_SESSION['ID']);
        session_destroy();
        header("location: login");
        exit();
    }

    if (!LoggedIn()){
        header('location: login');
        die();
    }
    if (isset($_SESSION['TOKEN'])){
        if ($getToken != $_SESSION['TOKEN']){
            $user = $_SESSION["username"];
            $realIP = hash('sha256', realIP());
            $country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_countryName'};
            $city = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_city'};
            session_regenerate_id(true);
            unset($_SESSION['username']);
            unset($_SESSION['ID']);
            unset($_SESSION['TOKEN']);
            session_destroy();
            header("location: index");
        }
    }else{
        session_regenerate_id(true);
        unset($_SESSION['username']);
        unset($_SESSION['ID']);
        unset($_SESSION['JWTToken']);
        session_destroy();
        header("location: login");
    }
    if ($checkExist == 0){
        session_regenerate_id(true);
        unset($_SESSION['username']);
        unset($_SESSION['ID']);
        session_destroy();
        header("location: login");
        exit();
    }
?>