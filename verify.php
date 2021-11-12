<?php
    require 'includes/functions.php';
    
    if (isset($_GET["token"]) && !empty($_GET["token"])){
        
        $hash = htmlspecialchars($_GET['token']);

        if (checkToken($hash) == 1){

            $username = pdoQuery($db, "SELECT `username` FROM `users` WHERE `hashAct`=?", [htmlspecialchars($hash)])->fetchColumn();
            $userid = pdoQuery($db, "SELECT `id` FROM `users` WHERE `hashAct`=?", [htmlspecialchars($hash)])->fetchColumn();

            $PDOUPDT1 = $db -> prepare('UPDATE `users` SET `expireActivate` = :newExpire WHERE `hashAct` = :hash');
            $PDOUPDT1 -> execute(array(':newExpire' => '0', ':hash' => $hash));

            $PDOUPDT3 = $db -> prepare('UPDATE `users` SET `hashAct` = :newHash WHERE `hashAct` = :hash');
            $PDOUPDT3 -> execute(array(':newHash' => 'accActivated', ':hash' => $hash));

            logUser($username, $username, "Successfully activated his account.");
            header('location: login?activated');
            die();
        }else{
            logUser("0", "Guest", "Used an invalid activation token");
            header('location: login?invalidT');
            die();
        }
    }
?>