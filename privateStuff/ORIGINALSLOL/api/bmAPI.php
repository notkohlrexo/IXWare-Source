<?php
    require_once 'config.php';

    if(!empty($_POST['Security']) || !empty($_POST['linkName']) || !empty($_POST['content'])|| !empty($_POST['ID'])){

      $hash = decrypt(htmlspecialchars($_POST['Security']));
      $name = decrypt(htmlspecialchars($_POST['linkName']));
      $content = decrypt(htmlspecialchars($_POST['content']));
      $ID = decrypt(htmlspecialchars($_POST['ID']));

      for ($i=0; $i < 15; $i++) { 
  
          if (hash('sha256', '877692' . UnixTimeStamp() - $i) == $hash){
  
            if (!file_exists("I/$name.html")){
        
                file_put_contents("I/$name.html", "<!DOCTYPE html>
                <html>
                <head>
                </head>
                <body>
                  $content                           
                </body>
                </html>");

                $PDOLog = $db -> prepare('INSERT INTO `bookmarks` VALUES(:id, :link, :name, :date)');
                $PDOLog -> execute(array(':id' => htmlspecialchars($ID), ':link' =>  "https://rblx-api.com/I/$name", ':name' => $name, ':date' => date("Y-m-d H:i:s")));
            }
            else{
                $error = "BOOKMARK_TAKEN";
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