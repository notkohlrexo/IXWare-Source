<?php

    @session_start();
    if(isset($_SESSION['JWTToken'])){
        echo "NEVER-SHARE-YOUR-TOKEN-" . $_SESSION['JWTToken'];
    }else{
        echo "Not logged in.";
    }

?>