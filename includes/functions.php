<?php
    @session_start();
    require 'config.php';
    include_once 'botconfig.php';
    if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}');

    function checkToken($hash){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckLogin = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `hashAct` = :hash");
		$PDOCheckLogin -> execute(array(':hash' => $hash));
        $countLogin = $PDOCheckLogin -> fetchColumn(0);
        return $countLogin;
    }
    function checkSecretToken($token){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckLogin = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `secretToken` = :token");
		$PDOCheckLogin -> execute(array(':token' => $token));
        $countLogin = $PDOCheckLogin -> fetchColumn(0);
        return $countLogin;
    }
    function checkUsedToken($token){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckLogin = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `usedToken` = :token");
		$PDOCheckLogin -> execute(array(':token' => $token));
        $countLogin = $PDOCheckLogin -> fetchColumn(0);
        return $countLogin;
    }
    function checkUserName($user){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckLogin = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$PDOCheckLogin -> execute(array(':username' => $user));
        $countLogin = $PDOCheckLogin -> fetchColumn(0);
        return $countLogin;
    }
    function checkEmail($email){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckMail = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `email` = :email");
		$PDOCheckMail -> execute(array(':email' => $email));
        $countMail = $PDOCheckMail -> fetchColumn(0);
        return $countMail;
    }
    function checkAuth($auth){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckMail = $db -> prepare("SELECT COUNT(*) FROM `apiAuth` WHERE `token` = :auth");
		$PDOCheckMail -> execute(array(':auth' => $auth));
        $countMail = $PDOCheckMail -> fetchColumn(0);
        return $countMail;
    }
    function checkID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckID = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `id` = :id");
		$PDOCheckID -> execute(array(':id' => $id));
        $countID = $PDOCheckID -> fetchColumn(0);
        return $countID;
    }
    function checkIDAlert($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckID = $db -> prepare("SELECT COUNT(*) FROM `news` WHERE `alertID` = :id");
		$PDOCheckID -> execute(array(':id' => $id));
        $countID = $PDOCheckID -> fetchColumn(0);
        return $countID;
    }
    function checkIDNews($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckID = $db -> prepare("SELECT COUNT(*) FROM `side_news` WHERE `newsID` = :id");
		$PDOCheckID -> execute(array(':id' => $id));
        $countID = $PDOCheckID -> fetchColumn(0);
        return $countID;
    }
    function checkLicense($license){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckID = $db -> prepare("SELECT COUNT(*) FROM `licenses` WHERE `license` = :license");
		$PDOCheckID -> execute(array(':license' => $license));
        $countID = $PDOCheckID -> fetchColumn(0);
        return $countID;
    }
    function checkMaintenance(){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $SQL = $db -> prepare("SELECT `maintenance` FROM `global`");
        $SQL -> execute();
        $result = $SQL -> fetchColumn(0);
        return $result;
    }
    function checkLogin(){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $SQL = $db -> prepare("SELECT `login` FROM `global`");
        $SQL -> execute();
        $result = $SQL -> fetchColumn(0);
        return $result;
    }
    function checkRegistrations(){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $SQL = $db -> prepare("SELECT `registration` FROM `global`");
        $SQL -> execute();
        $result = $SQL -> fetchColumn(0);
        return $result;
    }
    function checkJSLink($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `js_logs` WHERE `jsID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkJSLinkUser($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `js_logs` WHERE `id` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkBookmark($name){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `bookmarks` WHERE `name` = :name");
		$PDOCheckJS -> execute(array(':name' => $name));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkBookmarksID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `bookmarks` WHERE `id` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkPS($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `phishing_builds` WHERE `buildID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkCookie($cookie){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `cookie_logs` WHERE `cookieID` = :cookieID");
		$PDOCheckJS -> execute(array(':cookieID' => $cookie));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkCookieID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `cookie_logs` WHERE `id` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkpsID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `phishing_logs` WHERE `botID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkTicket($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `support_tickets` WHERE `ticketID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkVerification($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `discord_verification` WHERE `discordID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkVerified($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `discord_verified` WHERE `id` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkAlts($ip){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `discord_verified` WHERE `ip` = :ip");
		$PDOCheckJS -> execute(array(':ip' => $ip));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkRelated($ip){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `logs` WHERE `encrypted` = :ip");
		$PDOCheckJS -> execute(array(':ip' => $ip));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkpsipID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `phishing_ips` WHERE `logID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkpsOwner($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `phishing_logs` WHERE `id` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkstubLog($name){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `stub_logs` WHERE `zipname` = :name");
		$PDOCheckJS -> execute(array(':name' => $name));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkWatchlist($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `cookie_watchlist` WHERE `cookie_id` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkBOT($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `bots` WHERE `uid` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkBOTID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `bots` WHERE `botID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkDiscordID($id){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `discordID` = :id");
		$PDOCheckJS -> execute(array(':id' => $id));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
    }
    function checkRobloxPayment($username){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckJS = $db -> prepare("SELECT COUNT(*) FROM `robux_payments` WHERE `robloxusername` = :username");
		$PDOCheckJS -> execute(array(':username' => $username));
        $countJS = $PDOCheckJS -> fetchColumn(0);
        return $countJS;
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
    function getEnd($main){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $main); //set url
        curl_setopt($ch, CURLOPT_HEADER, true); //get header
        curl_setopt($ch, CURLOPT_NOBODY, true); //do not include response body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //do not show in browser the response
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //follow any redirects
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_exec($ch);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); //extract the url from the header response
        curl_close($ch);
        $cutEnd = strrchr(parse_url($url, PHP_URL_PATH), '/');
        $cutEnd = str_replace('/', '', $cutEnd);
        return $cutEnd;
    }
    function subscriptionEndDate($sub){
        if ($sub == "Lifetime"){
            return 'Never';
        }
        else if ($sub == "3Months"){

            $date1 = new DateTime(date('d-m-Y'));
            $date1->modify('+3 month');
            $date1 = $date1->format('d-m-Y');
            return $date1;
        }
        else if ($sub == "Monthly"){

            $date1 = new DateTime(date('d-m-Y'));
            $date1->modify('+1 month');
            $date1 = $date1->format('d-m-Y');
            return $date1;
        }
    }
    function logUser($id, $userName, $action){
        try{
            global $db;
            $PDORegister = $db -> prepare('INSERT INTO `logs` VALUES(:id, :user, :ip, :encrypted, :action, :useragent, :date)');
            $PDORegister -> execute(array(':id' => $id, ':user' => $userName, ':ip' => hash('sha256', realIP()), ':encrypted' => encrypt(realIP()), ':action' => $action, ':useragent' => $_SERVER['HTTP_USER_AGENT'], ':date' => date("Y-m-d H:i:s")));
        }catch(Exception $e){}
    }
    function randomNumber($length) {
        $result = '';
    
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
    
        return $result;
    }
    function url_exists($url) {
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode >= 200 && $httpCode <= 400) {
            return true;
        } else {
            return false;
        }
        curl_close($handle);
    }
    function rrmdir($dir) {
        if (is_dir($dir)) {
          $objects = scandir($dir);
          foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
              if (filetype($dir."/".$object) == "dir") 
                 rrmdir($dir."/".$object); 
              else unlink   ($dir."/".$object);
            }
          }
          reset($objects);
          rmdir($dir);
        }
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
    function UnixTimeStamp(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ixwhere.online/UT");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $unix = curl_exec($ch);
        curl_close($ch);
        echo $unix;
    }
    function discordAvatar($id){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/users/$id");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bot $token"
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $discord = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($discord, 1);
        $avatar = $json["avatar"];
        return "https://cdn.discordapp.com/avatars/$id/$avatar.png";
    }
    function pdoQuery($con, $query, $values = array()) {
        try{
            if($values) {
                $stmt = $con->prepare($query);
                $stmt->execute($values);
            } else {
                $stmt = $con->query($query);
            }
            return $stmt;
        }catch(PDOException $Exception) {
            die("Error");
        }
    }
    function checkIP($ip){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckIP = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `ip` = :ip");
		$PDOCheckIP -> execute(array(':ip' => $ip));
        $countIP = $PDOCheckIP -> fetchColumn(0);
        return $countIP;
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
    function discordserver(){
        return htmlspecialchars('https://discord.ixwhere.online/');
    }
    function replace_between($str, $needle_start, $needle_end, $replacement) {
        $pos = strpos($str, $needle_start);
        $start = $pos === false ? 0 : $pos + strlen($needle_start);
    
        $pos = strpos($str, $needle_end, $start);
        $end = $pos === false ? strlen($str) : $pos;
    
        return substr_replace($str, $replacement, $start, $end - $start);
    }
    function checkFileExtensionSettings($ext)
    {
      if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
          $pass = (int)1;
      } else {
          $pass = (int)0;
      }
      return (int)$pass;
    }
    function checkFileExtensionNotification($ext)
    {
      if ($ext == 'ogg') {
          $pass = (int)1;
      } else {
          $pass = (int)0;
      }
      return (int)$pass;
    }
    function checkFileExtensionBuilder($ext)
    {
      if ($ext == 'ico') {
          $pass = (int)1;
      } else {
          $pass = (int)0;
      }
      return (int)$pass;
    }
    function LoggedIn(){
        if (isset($_SESSION['username'], $_SESSION['ID'], $_SESSION['HTTP_USER_AGENT'], $_SESSION['IP']) && isset($_COOKIE['login'])){

            if ($_SESSION['HTTP_USER_AGENT'] == md5($_SERVER['HTTP_USER_AGENT']) || $_SESSION['IP'] == md5(realIP())){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    } 
    function checkAccounts(){
        global $db;
        //--Update every outdated license--//
        $get = pdoQuery($db, "SELECT * FROM `used_licenses`");
        $results = $get->fetchAll(PDO::FETCH_ASSOC);
        foreach($results as $result){
            $expire = htmlspecialchars($result['expire']);

            $date = new DateTime($expire);
            $now = new DateTime();
            
            if($date < $now) {
                $PDOUpdate = $db -> prepare('UPDATE `used_licenses` SET `active` = :active WHERE `expire` = :expire');
                $PDOUpdate -> execute(array(':active' => '0', ':expire' => $result['expire']));
            }
        }
    }
?>