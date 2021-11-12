<?php 
    @session_start();
    $title = 'General Settings';
    include_once 'includes/layout/header.php';
    include_once 'includes/checks.php';
    include_once 'includes/botconfig.php';
    include_once 'loader.php';
    include_once __DIR__.'/../vendor/autoload.php';
    Loader::register('lib','RobThree\\Auth');
    use \RobThree\Auth\TwoFactorAuth;
       
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $activated = pdoQuery($db, "SELECT `expireActivate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $faenabled = pdoQuery($db, "SELECT `2FA` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $notificationSound = pdoQuery($db, "SELECT `notificationPath` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
    
    if($banned != 0){
        
        header("location: support");
        exit();
    }
    if(checkMaintenance() == 'Maintenance' && $getRank != "Admin"){
      header('Location: dashboard');
      die();
    }
    if (isset($_GET["id"])){
      $error = "Please update your discord ID!";
    }
    if (isset($_GET["success"])){
      $success = "Successfully updated your discord ID!";
    }
    if (isset($_GET["failed"])){
      $error = "The discord ID is already linked to an other account, contact a IXWare Administrator!";
    }
     
    $username = htmlspecialchars($_SESSION['username']);

    if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
    }
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

define('OAUTH2_CLIENT_ID', '816007320454430798');
define('OAUTH2_CLIENT_SECRET', 'FjY6v6hTRPjcQmT4Te6KXGmWmpT5Vbxy');

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';

@session_start();

// Start the login process by sending the user to Discord's authorization page
if(get('action') == 'login') {

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'https://ixwhere.online/settings',
    'response_type' => 'code',
    'scope' => 'identify guilds guilds.join'
  );

  // Redirect the user to Discord's authorization page
  header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}

unset($_SESSION['access_token']);

// When Discord redirects the user back here, there will be a "code" and "state" parameter in the query string
if(get('code')) {

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => 'https://ixwhere.online/settings',
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;


  header('Location: ' . $_SERVER['PHP_SELF']);
}

if(session('access_token')) {
  $user = apiRequest($apiURLBase);

  if(checkDiscordID($user->id) != 1){


    $params = '{"access_token" : "'.$_SESSION['access_token'].'"}';
    $ch = curl_init("https://discordapp.com/api/v6/guilds/816009969677238303/members/$user->id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);


    $headers[] = "Authorization: Bot lol";
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Content-Length: '.strlen($params);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_exec($ch);

    $currentDiscord = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    if($currentDiscord != 0){

      try{
        $discord->guild->removeGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$currentDiscord, 'role.id' => $membershipID]);
        $discord->guild->removeGuildMemberRolee(['guild.id' => $guildid, 'user.id' => (int)$currentDiscord, 'role.id' => $registeredID]);    
      }catch(Exception $e){}
    }

    $PDOS = $db -> prepare('UPDATE `users` SET `discordID` = :did WHERE `id` = :id');
    $PDOS -> execute(array(':did' => $user->id, ':id' => htmlspecialchars($_SESSION['ID'])));
  
    try{
      $discord->channel->createMessage(['channel.id' => $linkedChannel, 'content' => "<@$user->id> linked his account to `$username`."]);
      if($getRank == "Admin"){
        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $adminID]);  
        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $membershipID]);
        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $registeredID]);      
      }elseif($getRank == "Premium"){
        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $membershipID]);
        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $registeredID]);        
      }elseif($getRank == "User"){
        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $registeredID]);    
      }

      $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $regularID]);  
      
      if(checkVerified($user->id) == 0){

        $PDOLogx = $db -> prepare('INSERT INTO `discord_verified` VALUES(:id, :username, :ip, :agent, :date)');
        $PDOLogx -> execute(array(':id' => $user->id, ':username' => htmlspecialchars($_SESSION['username']), ':ip' => encrypt(realIP()), ':agent' => htmlspecialchars($_SERVER['HTTP_USER_AGENT']), ':date' => date("Y-m-d H:i:s")));  
        
        if(checkAlts(encrypt(realIP())) > 1){
              
            $get = pdoQuery($db, "SELECT * FROM `discord_verified` WHERE `ip`=?", [htmlspecialchars(encrypt(realIP()))]);
            $results = $get->fetchAll(PDO::FETCH_ASSOC);
            $a = array();
    
            foreach($results as $result){
    
                $id = htmlspecialchars($result['id']);
                $user = htmlspecialchars($result['username']);
                $tog = "$id - $user";
                array_push($a, $tog);
            }
            array_pop($a);
            $string = rtrim(implode("\xA", $a), "\xA");
            $color = "#ff0000";
        }else{
            $string = "None";
            $color = "#00ff04";
        }
    
        try{
            $discord->channel->createMessage(
                [
                    'channel.id' => $verificationChannel,
                    'embed'      => [
                        'timestamp' => date("c"),
                        "color" => hexdec($color),
                        'title' => 'Account-Linked & Verified User '.date('Y-m-d H:i:s'),
                        "thumbnail" => [
                            "url" => "https://ixwhere.online/resources/img/logo.png"
                        ],
                        "image" => [
                            "url" => "https://ixwhere.online/resources/img/IXWARE.gif"
                        ],
                        "author" => [
                            "name" => "IXWare",
                            "url" => "https://www.ixwhere.online/"
                        ],
                        "footer" => [
                            "text" => "Powered by ixwhere.online",
                            "icon_url" => "https://ixwhere.online/resources/img/logo.png"
                        ],
                        "fields" => [
                            [
                                "name" => "Username",
                                "value" => htmlspecialchars($_SESSION['username'])
                            ],
                            [
                                "name" => "ID",
                                "value" => htmlspecialchars($user->id)
                            ],
                            [
                                "name" => "Alt's Detected",
                                "value" => "```$string```"
                            ],
                        ]
                    ],
                ]
            );
        }catch(Exception $e){}
      }

      $dm = $discord->user->createDm(['recipient_id' => (int)$user->id]);
      $discord->channel->createMessage(['content' => "Successfully linked discord account to `$username` - if it's not your account then contact an IXWare Administrator to report that issue.", 'channel.id' => $dm->id]);
    }catch(Exception $e){}

    logUser($_SESSION['ID'], htmlspecialchars($_SESSION['username']), "Linked his discord id: $user->id");
    $ix = $_SERVER['SERVER_NAME'];
    header("Location: https://$ix/settings?success");
    exit();
  }else{
    $ix = $_SERVER['SERVER_NAME'];
    header("Location: https://$ix/settings?failed");
    exit();
  }
}


//if(get('action') == 'logout') {
  // This must to logout you, but it didn't worked(

 // $params = array(
 //   'access_token' => $logout_token
//  );

  // Redirect the user to Discord's revoke page
//  header('Location: https://discordapp.com/api/oauth2/token/revoke' . '?' . http_build_query($params));
//  die();
//}

function apiRequest($url, $post=FALSE, $headers=array()) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

  $response = curl_exec($ch);


  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if(session('access_token'))
    $headers[] = 'Authorization: Bearer ' . session('access_token');

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}

function get($key, $default=NULL) {
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}

?>

<?php
  if (isset($_POST['defaultAvatar'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){

        $PDOURL = $db -> prepare('UPDATE `users` SET `avatarURL` = :url WHERE `id` = :id');
        $PDOURL -> execute(array(':url' => '0', ':id' => htmlspecialchars($_SESSION['ID'])));
    
        logUser($_SESSION['ID'], $_SESSION['username'], "Updated avatar to default");
        $success = "Successfully updated your avatar.";
      }else{
        $error = "You need to activate your account.";
      }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }

  if (isset($_POST['changePW'])){

      if (\Volnix\CSRF\CSRF::validate($_POST)) {

          if(!empty($_POST['currentPW']) || !empty($_POST['newPW']) || !empty($_POST['newRepeat'])){

            if ($activated == 0){

                if($_POST['newPW'] == $_POST['newRepeat']){
                  $password = htmlspecialchars($_POST['currentPW']);
                  $newPW = htmlspecialchars($_POST['newPW']);
                  $gethashedPW = pdoQuery($db, "SELECT `pass` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn()."\n";

                  if(password_verify($password, trim($gethashedPW))){
                      $options = array(
                          'cost' => 12,
                        );
                      $hashPassword = password_hash($newPW, PASSWORD_BCRYPT, $options)."\n";

                      $PDOPW = $db -> prepare('UPDATE `users` SET `pass` = :newPW WHERE `id` = :id');
                      $PDOPW -> execute(array(':newPW' => $hashPassword, ':id' => htmlspecialchars($_SESSION['ID'])));

                      logUser($_SESSION['ID'], $_SESSION['username'], "Changed his password");
                      $success = "Password successfully changed!";
                  }else{
                      logUser($_SESSION['ID'], $_SESSION['username'], "Tried to change password, but password is wrong");
                      $error = "Wrong password!";
                  }
              }else{
                  $error = "New passwords doesn't match.";
              }
            }else{
              $error = "You need to activate your account.";
            }
        }else{
            $error = "Please fill every textbox!";
        }
      }else{
        header("HTTP/1.1 401 Unauthorized");
        echo file_get_contents('includes/layout/error/401.php');
        die();
      }
  }
  if (isset($_POST['enablefa'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {
      
      if ($activated == 0){

        if($faenabled != 1){
          $tfa = new TwoFactorAuth('IXWare');
          $secret = $tfa->createSecret(160);
    
          echo "<div class='modal fade' id='showPW' tabindex='-1' role='dialog' aria-hidden='true'>
          <div class='modal-dialog modal-lg' role='document'>
            <div class='modal-content'>
              <div class='modal-header bg-dark text-darkwhite'>
                <h5 class='modal-title w-100 modaltext'>Two Factor Authentication</h5>
                <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>
              </div>
              <div class='modal-body bg-dark text-darkwhite'>
                <img src='" . $tfa->getQRCodeImageAsDataUri(htmlspecialchars($_SESSION['username']), $secret) . "'>
                <p class='modaltext mt-2'>Key (to enter it manually): <strong class='text-muted font-weight-bolder'>" . chunk_split($secret, 4, ' ') . "</strong></p>
                <form method='POST'>
                  <div class='md-form mt-0'>
                    <input type='text' placeholder='Token' name='verifyToken' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                    <input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/>
                    <input type='hidden' name='secret2fatoken' value='" . htmlspecialchars($secret) . "'>
                  </div>
                  <button type='submit' name='submit2faToken' class='btn btn-outline-primary waves-effect mt-3 center-button settings-button' style='width: 100%;'>Submit</button>
                </form>
              </div>
            </div>
          </div>
        </div>"; 
        }else{
          $error = "Two Factor Authentication is already enabled on your account.";
        }
      }else{
        $error = "You need to activate your account.";
      }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }
  if (isset($_POST['submit2faToken'])){

    if(!empty($_POST['verifyToken'])){

      if ($activated == 0){

        if (\Volnix\CSRF\CSRF::validate($_POST)) {

          $tfa = new TwoFactorAuth('IXWare');
          if($tfa->verifyCode(htmlspecialchars($_POST['secret2fatoken']), htmlspecialchars($_POST['verifyToken'])) === true){
            $PDOS = $db -> prepare('UPDATE `users` SET `2FA` = :enable WHERE `id` = :id');
            $PDOS -> execute(array(':enable' => '1', ':id' => htmlspecialchars($_SESSION['ID'])));
            $PDOS1 = $db -> prepare('UPDATE `users` SET `2FAToken` = :token WHERE `id` = :id');
            $PDOS1 -> execute(array(':token' => encrypt(htmlspecialchars($_POST['secret2fatoken'])), ':id' => htmlspecialchars($_SESSION['ID'])));
    
            $success = "Successfuly enabled Two Factor Authentication on your account.";
          }else{
            $error = "The token is invalid.";
          }
        }else{
          header("HTTP/1.1 401 Unauthorized");
          echo file_get_contents('includes/layout/error/401.php');
          die();
        }
      }else{
        $error = "You need to activate your account.";
      }
    }else{
      $error = "You need to provide the token to enable the Two Factor Authentication.";
    }
  }
  if (isset($_POST['submit2faTokenDisable'])){

    if(!empty($_POST['verifyTokenDisable'])){

      if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if ($activated == 0){

          $tfa = new TwoFactorAuth('IXWare');
          $secretkey = pdoQuery($db, "SELECT `2FAToken` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    
          if($tfa->verifyCode(htmlspecialchars(decrypt($secretkey)), htmlspecialchars($_POST['verifyTokenDisable'])) === true){
            $PDOS = $db -> prepare('UPDATE `users` SET `2FA` = :enable WHERE `id` = :id');
            $PDOS -> execute(array(':enable' => '0', ':id' => htmlspecialchars($_SESSION['ID'])));
            $PDOS1 = $db -> prepare('UPDATE `users` SET `2FAToken` = :token WHERE `id` = :id');
            $PDOS1 -> execute(array(':token' => '0', ':id' => htmlspecialchars($_SESSION['ID'])));
    
            $success = "Successfuly disabled Two Factor Authentication on your account.";
          }else{
            $error = "The token is invalid.";
          }
        }else{
          $error = "You need to activate your account.";
        }
      }else{
        header("HTTP/1.1 401 Unauthorized");
        echo file_get_contents('includes/layout/error/401.php');
        die();
      }
    }else{
      $error = "You need to provide the token to disable the Two Factor Authentication.";
    }
  }
  if (isset($_POST['disablefa'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){

        if($faenabled == 1){
            echo "<div class='modal fade' id='showPW' tabindex='-1' role='dialog' aria-hidden='true'>
            <div class='modal-dialog modal-lg' role='document'>
              <div class='modal-content'>
                <div class='modal-header bg-dark text-darkwhite'>
                  <h5 class='modal-title w-100 modaltext'>Disable Two Factor Authentication</h5>
                  <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>
                </div>
                <div class='modal-body bg-dark text-darkwhite'>
                  <form method='POST'>
                    <div class='md-form mt-0'>
                      <input type='text' placeholder='Token' name='verifyTokenDisable' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                      <input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/>
                    </div>
                    <button type='submit' name='submit2faTokenDisable' class='btn btn-outline-primary waves-effect mt-3 center-button settings-button' style='width: 100%;'>Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>"; 
        }else{
          $error = "Two Factor Authentication isn't enabled on your account.";
        }
      }else{
        $error = "You need to activate your account.";
      }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }
  if (isset($_POST['changeUsername'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){

        if(!empty($_POST['newUsername'])){

          $newUsername = htmlspecialchars($_POST['newUsername']);
          if($newUsername != $_SESSION['username']){


            if (ctype_alnum($newUsername) && strlen($newUsername) > 4 && strlen($newUsername) < 15){

              if (checkUserName($newUsername) != 1){

                $PDOS = $db -> prepare('UPDATE `users` SET `username` = :new WHERE `id` = :id');
                $PDOS -> execute(array(':new' => htmlspecialchars($newUsername), ':id' => htmlspecialchars($_SESSION['ID'])));

                logUser($_SESSION['ID'], $_SESSION['username'], "Changed userName to $newUsername");
                unset($_SESSION['username']);
                $_SESSION['username'] = $newUsername;
                $success = "Successfully changed your userName to: $newUsername";
              }else{
                $error = "A user with that userName already exists.";
              }
            }else{
              $error = 'UserName must be alphanumeric and 4-15 characters in length';
            }
          }else{
            $error = "In order to change your userName you need to provide a new one.";
          }
        }else{
          $error = "In order to change your userName you need to provide a new one.";
        }
      }else{
        $error = "You need to activate your account.";
      }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }
  if(isset($_POST['uploadAvatar'])){
  
    if(!empty($_FILES['avatar']['name'])){

      $id = getID(20);
      $errors= array();
      $file_size = $_FILES['avatar']['size'];
      $file_tmp = $_FILES['avatar']['tmp_name'];
      $file_type= $_FILES['avatar']['type'];
      $tmp = explode('.', $_FILES['avatar']['name']);
      $file_ext = htmlspecialchars(end($tmp));
      $file_name = "$id.$file_ext";

      $fileAccepted = checkFileExtensionSettings($file_ext);

      if($fileAccepted == 1){

        if($file_size < 8000000){

          $foldername = htmlspecialchars($_SESSION['username']);
          if (!file_exists("users/$foldername")){
            mkdir('users/' . $foldername, 0755, true);
          }
          if (!file_exists("users/$foldername/img")){
            mkdir('users/' . $foldername . '/img', 0755, true);
          }
  
          move_uploaded_file($file_tmp,"users/$foldername/img/". $file_name);
          $PDOS = $db -> prepare('UPDATE `users` SET `avatarURL` = :new WHERE `id` = :id');
          $PDOS -> execute(array(':new' => "https://ixwhere.online/users/$foldername/img/$file_name", ':id' => htmlspecialchars($_SESSION['ID'])));
        }else{
          $error = "File is >8 MB.";
        }
      }else{
        $error = "File Type '$file_ext' not allowed.";
      }

      if(empty($error)){
        $success = "Successfully changed your avatar.";
      }else{
        $error = $error;
      }
    }else{
      $error = "Please select a file";
    }
  }
  if(isset($_POST['updateNotification'])){
  
    if(!empty($_FILES['notification']['name'])){
      $id = getID(35);
      $errors = array();
      $file_size = $_FILES['notification']['size'];
      $file_tmp = $_FILES['notification']['tmp_name'];
      $file_type= $_FILES['notification']['type'];
      $tmp = explode('.', $_FILES['notification']['name']);
      $file_ext = htmlspecialchars(end($tmp));
      $file_name = htmlspecialchars("$id.$file_ext");

      $fileAccepted = checkFileExtensionNotification($file_ext);

      if($fileAccepted == 1){

        if($file_size < 10000000){

          if (!file_exists("resources/audio/$file_name")){
            move_uploaded_file($file_tmp,"resources/audio/". $file_name);

            $PDOS = $db -> prepare('UPDATE `users` SET `notificationPath` = :npath WHERE `id` = :id');
            $PDOS -> execute(array(':npath' => "resources/audio/$file_name", ':id' => htmlspecialchars($_SESSION['ID'])));
            logUser($_SESSION['ID'], $_SESSION['username'], "Changed Notification to $file_name");

          }else{
            $error = "An error occured, please try again.";
          }
        }else{
          $error = "File is >10 MB.";
        }
      }else{
        $error = "File Type '$file_ext' not allowed. Only '.ogg' allowed.";
      }

      if(empty($error)){
        $success = "Successfully changed your notification sound.";
      }else{
        $error = $error;
      }
    }else{
      $PDOS = $db -> prepare('UPDATE `users` SET `notificationPath` = :npath WHERE `id` = :id');
      $PDOS -> execute(array(':npath' => "resources/audio/notification.ogg", ':id' => htmlspecialchars($_SESSION['ID'])));
      $success = "Changed your notification to the default notification sound.";
    }
  }
?>

  <body class="bg-dark">

  <!--Main Navigation-->
  <header>
    <!-- Sidenav -->
    <nav id="sidenav-1" class="sidenav bg-dark" data-hidden="false" data-accordion="true">
      <a class="ripple d-flex justify-content-center py-4" href="#!" data-ripple-color="primary">
        <img id="IXWARE Logo" src="resources/img/ixwarelogogif.gif" alt="IX Logo" draggable="false" class="same-size-logo" />
      </a>

      <ul class="sidenav-menu">
        <li class="sidenav-item ml-4 mt-2 mb-1 text-white">
          <span>General</span>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link" href="dashboard">
            <i class="fas fa-tachometer-slow fa-fw mr-3"></i><span>Dashboard</span>
          </a>
        </li>
        <?php
          if ($getRank == "User" || $getRank == "Admin"){
            echo "<li class='sidenav-item'>
            <a class='sidenav-link' href='purchase'>
              <i class='fas fa-shopping-basket fa-fw mr-3'></i><span>Purchase</span>
            </a>
          </li>";
          }
        ?>
        <li class="sidenav-item ml-4 mt-4 mb-1 text-white">
          <span>Managing</span>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link">
            <i class="fas fa-users fa-fw mr-3"></i><span>Bots</span>
          </a>
          <ul class="sidenav-collapse">
            <?php
              if ($getRank == "Premium" || $getRank == "Admin"){

                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='bots'>Stubs <span class='badge bg-danger ml-2'></span></a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBots'>Javascript <span class='badge bg-danger ml-2'></span></a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='psBots'>Phishing <span class='badge bg-danger ml-2'>0</span></a>
                </li>";
              }elseif($getRank == "User"){

                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBots'>Javascript <span class='badge bg-danger ml-2'></span></a>
                </li>";
              }
            ?>
          </ul>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link">
            <i class="fas fa-shield-virus fa-fw mr-3"></i><span>Builder</span>
          </a>
          <ul class="sidenav-collapse">
            <?php
              if ($getRank == "Premium" || $getRank == "Admin"){
                
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='builder'>Stub</a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBuilder'>Javascript</a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='psBuilder'>Phishing</a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='bmBuilder'>Bookmark</a>
                </li>";
              }elseif($getRank == "User"){
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBuilder'>Javascript</a>
                </li>";
              }
            ?>
          </ul>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link">
            <i class="fas fa-file-download fa-fw mr-3"></i><span>Builds</span>
          </a>
          <ul class="sidenav-collapse">
            <?php
              if ($getRank == "Premium" || $getRank == "Admin"){
                
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='builds'>Stub</a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBuilds'>Javascript</a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='psBuilds'>Phishing</a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='bmBuilds'>Bookmark</a>
                </li>";
              }elseif($getRank == "User"){
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBuilds'>Javascript</a>
                </li>";
              }
            ?>
          </ul>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link">
            <i class="fas fa-tools fa-fw mr-3"></i><span>Tools</span>
          </a>
          <ul class="sidenav-collapse">
            <li class='sidenav-item'>
              <a class='sidenav-link' href='watchlist'>Watchlist</a>
            </li>
          </ul>
        </li>
        <li class="sidenav-item ml-4 mt-4 mb-1 text-white">
          <span>Account</span>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link">
            <i class="fas fa-sliders-h fa-fw mr-3"></i><span>Settings</span>
          </a>
          <ul class="sidenav-collapse">
            <li class="sidenav-item">
              <a class="sidenav-link active" href="settings">General</a>
            </li>
            <li class="sidenav-item">
              <a class="sidenav-link" href="builder-settings">Builder</a>
             </li>
          </ul>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link" href="ix-licenses">
            <i class="fab fa-keycdn fa-fw mr-3"#></i><span>Licenses</span>
          </a>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link" href="support">
            <i class="fas fa-question-circle fa-fw mr-3"#></i><span>Support</span>
          </a>
        </li>
        <li class="sidenav-item">
          <a class="sidenav-link" href="logout">
            <i class="fal fa-sign-out fa-fw mr-3"></i><span>Logout</span>
          </a>
        </li>
      </ul>
    </nav>
    <!-- Sidenav -->

    <!-- Navbar -->
    <nav
      id="main-navbar"
      class="navbar navbar-expand-lg navbar-light fixed-top bg-dark"
    >
      <!-- Container wrapper -->
      <div class="container-fluid">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item text-white">Profile Settings</li>
          </ol>
        </nav>
        <!-- Toggler -->
        <button
          data-toggle="sidenav"
          data-target="#sidenav-1"
          class="btn shadow-0 p-0 mr-3 d-block d-xxl-none"
          aria-controls="#sidenav-1"
          aria-haspopup="true"
        >
          <i class="fas fa-bars fa-lg"></i>
        </button>

        <!-- Right links -->
        <ul class="navbar-nav ml-auto d-flex flex-row">

          <!-- Icon -->
          <li class="nav-item">
            <a class="nav-link mr-3 mr-lg-0 mt-2 text-white">
              <?= $username ?>
            </a>
          </li>
          <!-- Icon -->
          <li class="nav-item">
            <a class="nav-link mr-3 mr-lg-0 mt-2">
              <?php
                if ($getRank == "User"){
                  echo "<span class='badge bg-light text-dark'>Default</span>";
                }elseif ($getRank == "Admin"){
                  echo "<span class='badge bg-danger'>Administrator</span>";
                }elseif ($getRank == "Premium"){
                  echo "<span class='badge bg-warning text-dark'>Premium</span>";
                }
              ?>
            </a>
          </li>
          <!-- Icon -->
          <li class="nav-item">
            <a class="nav-link mr-3 mr-lg-0">
<?php
		            $getAvatar = pdoQuery($db, "SELECT `avatarURL` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
                $av = htmlspecialchars($getAvatar);
                if ($av == '0' || empty($av)){
                  echo "<img src='resources/img/logo.png' class='img-fluid rounded-circle profile-size' alt='' />";
                }else{
                  echo "<img src='$av' class='img-fluid rounded-circle profile-size' alt='' />";
                }
              ?>
            </a>
          </li>
        </ul>
      </div>
      <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->
  </header>
  <!--Main Navigation-->


  <!--Main layout-->
  <main style="margin-top: 100px" class="d-flex ml-5 mr-5">
    <div class="container-fluid">
    <?php
              if(!empty($error)){
                  echo '<div class="animated fadeIn ml-5 mr-5">'.error(htmlspecialchars($error)).'</div>';
              }
              if(!empty($success)){
                  echo '<div class="animated fadeIn ml-5 mr-5">'.success(htmlspecialchars($success)).'</div>';
              }
            ?>
        <!-- Tabs navs -->
        <ul class="nav nav-tabs nav-fill mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active text-darkwhite" id="ex2-tab-1" data-toggle="tab" href="#ex2-tabs-1" role="tab" aria-controls="ex2-tabs-1" aria-selected="true"><i class="fad fa-sliders-h pr-2"></i>Account Settings</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-darkwhite" id="ex2-tab-2" data-toggle="tab" href="#ex2-tabs-2" role="tab" aria-controls="ex2-tabs-2" aria-selected="false"><i class="fad fa-list pr-2"></i>Logs</a>
            </li>
        </ul>
        <!-- Tabs navs -->

        <!-- Tabs content -->
        <div class="tab-content" id="ex2-content">
            <div class="tab-pane fade show active" id="ex2-tabs-1" role="tabpanel" aria-labelledby="ex2-tab-1" >
                <form method="POST" class="md-form md-form-resized" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card password-change-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                    <p class="font-weight-bolder text-left text-white">Change Password</p>
                                    <!-- Material input -->
                                    <div class="md-form mt-2">
                                        <input type="text" id="currentPW" placeholder="Current Password" name="currentPW" class="form-control text-darkwhite" style="background-color: transparent !important;">
                                    </div>
                                    <!-- Material input -->
                                    <div class="md-form mt-2">
                                        <input type="text" id="newPW" placeholder="New Password" name="newPW" class="form-control text-darkwhite" style="background-color: transparent !important;">
                                    </div>
                                    <!-- Material input -->
                                    <div class="md-form mt-2">
                                        <input type="text" id="newRepeat" placeholder="Repeat Password" name="newRepeat" class="form-control text-darkwhite" style="background-color: transparent !important;">
                                    </div>
                                    <button type="submit" name="changePW" value="pw" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">UPDATE</button>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card password-change-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                    <p class="font-weight-bolder text-left text-white">Change Avatar</p>
                                    <div class="form-file mt-2">
                                    <input type="file" class="form-file-input" id="customFile" accept=".jpg,.jpeg,.gif,.png" name="avatar" />
                                    <label class="form-file-label" for="customFile">
                                        <span class="form-file-text">Choose file...</span>
                                        <span class="form-file-button">Browse</span>
                                    </label>
                                    </div>
                                    <button type="submit" name="uploadAvatar" value="avatar" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">UPLOAD</button>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card fa-change-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                    <p class="font-weight-bolder text-left text-white">2FA</p>
                                    <div class="col text-darkwhite">
                                      <p>
                                        Enabled:
                                        <?php $fa=pdoQuery($db,"SELECT `2FA` FROM `users` WHERE `id`=?",[htmlspecialchars($_SESSION['ID'])])->fetchColumn();if($fa==1){echo "True";}else{echo "False";} ?>
                                      </p>
                                    </div>
                                    <button type="submit" name="enablefa" value="enablefa" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Enable</button>
                                    <button type="submit" name="disablefa" value="disablefa" class="btn btn-outline-primary waves-effect center-button settings-button" style="width: 100%;">Disable</button>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card fa-change-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                      <?php
                                        $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

                                        if($discordID == 0){
                                          echo '<p class="font-weight-bolder text-left text-white">Link your discord account</p>';
                                          echo '<p class="font-weight-bold text-muted text-left text-lightred text-middle">Make sure that you link your main discord account.</p>';
                                          echo '<p class="font-weight-bold text-muted text-left text-lightred text-middle">If its saying that your account is already linked then go back to dashboard and you should be able to use IXWare.</p>';
                                        }else{
                                          $ch = curl_init();
                                          curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/users/$discordID");
                                          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                                          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                              "Authorization: Bot lol"
                                          ));
                                          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                          $discord = curl_exec($ch);
                                          curl_close($ch);
          
                                          if(strpos($discord, "username") !== false){
                                            $json = json_decode($discord, 1);
                                            $user = $json["username"];
                                            $tag = $json["discriminator"];
                                            $id = $json["id"];
          
                                            echo '<p class="font-weight-lighter font-weight-bold text-white">Current linked Discord</p>';
                                            echo "<p class='text-darkwhite'>$user#$tag</p>";
                                            echo "<p class='text-darkwhite'>Discord ID: $id</p>";
                                          }else{
                                            echo "<p class='font-weight-lighter font-weight-bold'>Linked discord account unavailable, contact an administrator.</p>";
                                          }
                                        }
                                    ?>
                                    <a href="?action=login" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Log In</a>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-xl-6">
                            <div class="card username-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                    <p class="font-weight-bolder text-left text-white">Change UserName</p>
                                    <!-- Material input -->
                                    <div class="md-form mt-2">
                                        <input type="text" id="newUsername" value="<?php echo htmlspecialchars($_SESSION['username']);?>" name="newUsername" class="form-control text-darkwhite" style="background-color: transparent !important;">
                                    </div>
                                    <button type="submit" name="changeUsername" value="changeUsername" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Update</button>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card username-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                    <p class="font-weight-bolder text-left text-white">Change Notification Sound</p>
                                    <div class="form-file mt-2">
                                    <input type="file" class="form-file-input" id="changeNotification" accept=".ogg" name="notification" />
                                    <label class="form-file-label" for="changeNotification">
                                        <span class="form-file-text">Choose file...</span>
                                        <span class="form-file-button">Browse</span>
                                    </label>
                                    </div>
                                    <p class="text-darkwhite mt-2 p-margin">Current Notification Sound: '<?= htmlspecialchars($notificationSound); ?>'</p>
                                    <button type="submit" name="updateNotification" value="updateNotification" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Update</button>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="ex2-tabs-2" role="tabpanel" aria-labelledby="ex2-tab-2">
                    <div class="col-xl-12">
                      <div class="card bg-dark table" style="display: table;">
                          <div class="card-body d-flex flex-column">
                              <?php
                              $get = pdoQuery($db, "SELECT * FROM `logs` WHERE `id`=? order by `date` desc", [htmlspecialchars($_SESSION['ID'])]);
                              $results = $get->fetchAll(PDO::FETCH_ASSOC);
                              $row = $get->rowCount();

                              echo "<div class='table-responsive'>";
                                  echo "<table id='table-dataTable-settings' class='table table-hover text-darkwhite'>";
                                      echo "<thead class='text-nowrap'>
                                      <tr>
                                      <th scope='col' class='font-weight-bolder'>UserName</th>
                                      <th scope='col' class='font-weight-bolder'>IP</th>
                                      <th scope='col' class='font-weight-bolder'>Action</th>
                                      <th scope='col' class='font-weight-bolder'>User-Agent</th>
                                      <th scope='col' class='font-weight-bolder'>Date</th>
                                      </tr>
                                      </thead>
                                      <tbody>";
                                      foreach($results as $result){
                                        $name = htmlspecialchars($result['username']);
                                        $ip = htmlspecialchars($result['ip']);
                                        $action = htmlspecialchars($result['action']);
                                        $useragent = htmlspecialchars($result['useragent']);
                                        $date = htmlspecialchars($result['date']);

                                        echo "<tr>
                                        <td scope=\"row\" style='word-break:break-all;'>$name</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$ip</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$action</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$useragent</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$date</td>
                                        </tr>";
                                    }
                                  echo "</tbody></table>";
                              ?>
                          </div>
                      </div>
                    </div>
            </div>
        </div>
        <!-- Tabs content -->        
    </div>
  </main>
  <!--Main layout-->


  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>