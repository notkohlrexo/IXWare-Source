<?php 
    $title = 'Login';
    include_once 'includes/layout/header.php';
    include_once "includes/functions.php";
    include_once "includes/botconfig.php";
    include_once __DIR__.'/../vendor/autoload.php';
    include_once 'loader.php';

    Loader::register('lib','RobThree\\Auth');
    use \RobThree\Auth\TwoFactorAuth;

    if (LoggedIn()){
        header('location: dashboard');
        die();
    }

    $host = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    if(strpos($host, 'rblx-trade.com')){
        header('location: https://discord.ixwhere.com');
        die();
    }elseif(strpos($host, 'rblx-api.com')){
        header('location: https://discord.ixwhere.com');
        die();
    }

    $clientID = '646003957666-p6ar1o9gmf2pd12npf5if98ndrkekg2t.apps.googleusercontent.com';
    $clientSecret = 'oPGusHBu8zxL0eRJs7gIR5-X';
    $redirectUri = 'https://ixwhere.online/login';
    
    // create Client Request to access Google API
    $client = new Google_Client();
    $client->setClientId($clientID);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope("email");
    $client->addScope("profile");
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); 

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
    'redirect_uri' => 'https://ixwhere.online/login',
    'response_type' => 'code',
    'scope' => 'identify guilds guilds.join email'
  );

  header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}

unset($_SESSION['access_token']);

if(get('code')) {

  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => 'https://ixwhere.online/login',
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;


  header('Location: ' . $_SERVER['PHP_SELF']);
}

if(session('access_token')) {
  $user = apiRequest($apiURLBase);

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

  try{

    if(checkDiscordID($user->id) != 1){

        $id = getID(50);
        $givenusername = $user->username . '#' . $user->discriminator;
        $discordAvatarID = $user->id;
        $discordAvatar = $user->avatar;
        $picture = "https://cdn.discordapp.com/avatars/$discordAvatarID/$discordAvatar.png";
        if (checkUserName($givenusername) == 0){

            if(checkID($id) == 0){

                if(checkEmail($email) == 0){

                    if (checkIP(hash('sha256', realIP())) == 0){

                        $country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_countryName'};
                        $PDORegister = $db -> prepare('INSERT INTO `users` VALUES(:ID, :user, :password, :email, :country, :ip, :rank, 0, 0, 0, :subscription, :date, 0, :avatar, :notification, :timestamp, :hash, :discord, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0)');
                        $PDORegister -> execute(array(':ID' => $id, ':user' => htmlspecialchars($givenusername), ':password' => '0', ':email' => htmlspecialchars($user->email), ':country' => htmlspecialchars($country), ':ip' => htmlspecialchars(hash('sha256', realIP())), ':rank' => 'User', ':subscription' => 'None', ':date' => htmlspecialchars(date('d-m-Y')), ':avatar' => htmlspecialchars($picture), ':notification' => 'resources/audio/notification.ogg', ':timestamp' => '0', ':hash' => 'accActivated', ':discord' => htmlspecialchars($user->id)));
                        
                        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$user->id, 'role.id' => $registeredID]);    
                        $success = "Successfully registered, you can now log in via discord.";

                        try{
                            $discord->channel->createMessage(
                                [
                                    'channel.id' => $logChannel,
                                    'embed'      => [
                                        'timestamp' => date("c"),
                                        "color" => hexdec("#7289da"),
                                        'title' => 'Registered (DISCORD API) at: '.date('Y-m-d H:i:s'),
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
                                                "name" => "UserName",
                                                "value" => $givenusername
                                            ],
                                        ]
                                    ],
                                ]
                            );
                        }catch(Exception $e){
                            logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN LOGIN DISCORD API. $e");
                        }
                    }else{
                        $error = 'You already have an account!';
                    }
                }else{
                    $error = 'E-Mail is already taken!';
                }
            }else{
                $error = 'Registration failed due to ID generation, please log in again!';
            }
        }else{
            $error = "An error occured, please try again.";
        }
    }else{

        $userID = pdoQuery($db, "SELECT `ID` FROM `users` WHERE `discordID`=?", [htmlspecialchars($user->id)])->fetchColumn();
        $userName = pdoQuery($db, "SELECT `username` FROM `users` WHERE `discordID`=?", [htmlspecialchars($user->id)])->fetchColumn();
        $getCountry = pdoQuery($db, "SELECT `country` FROM `users` WHERE `discordID`=?", [htmlspecialchars($user->id)])->fetchColumn();
        $country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_countryName'};
        $checkBan = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `discordID`=?", [htmlspecialchars($user->id)])->fetchColumn();
        $token = getID(50);

        $_SESSION['username'] = $userName;
        $_SESSION['ID'] = $userID;
        $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
        $_SESSION['IP'] = md5(realIP());
        $_SESSION['TOKEN'] = $token;

        // Must be same as on node
        $key = 'twqJnNv7uQZjqkKvkLhTeLJur67WPNVxTmW3mresAzEv5WsnUJ';

        if (empty($_SESSION['JWTToken'])) {
            $data = [
                'userid' => $userID,
                'iat' => time(),
                'exp' => strtotime('+90 days', time())
            ];

            $_SESSION['JWTToken'] = \Firebase\JWT\JWT::encode($data, $key);
        }

        $PDOToken = $db -> prepare('UPDATE `users` SET `token` = :token WHERE `username` = :username');
        $PDOToken -> execute(array(':token' => $token, ':username' => $userName));

        setcookie("login", $userName, time() + 86400);

        if($checkBan == 0){

            if (!empty($country) & !empty($getCountry)){
                if ($getCountry != $country){
                    if($getRank != "Admin" && $getRank == "Premium"){
                        $countryChanges = pdoQuery($db, "SELECT `countryChanges` FROM `users` WHERE `discordID`=?", [htmlspecialchars($user->id)])->fetchColumn();
    
                        if($countryChanges >= 3){
    
                            $date1 = new DateTime();
                            $date1->modify('+1 Week');
                            $date1 = $date1->format('d-m-Y');
    
                            $PDOID = $db -> prepare('UPDATE `users` SET `banned` = :ban WHERE `discordID` = :id');
                            $PDOID -> execute(array(':ban' => $date1, ':id' => htmlspecialchars($user->id)));
                            $PDOID1 = $db -> prepare('UPDATE `users` SET `banreason` = :ban WHERE `discordID` = :id');
                            $PDOID1 -> execute(array(':ban' => 'AUTO-BAN - Different Location. If you think that you got false-banned then create a ticket.', ':id' => htmlspecialchars($user->id)));
                            $discord->channel->createMessage(['channel.id' => $logChannel, 'content' => "AUTO-Banned `$user` due to different country.\r\nCountry from User: `$userName` doesn't match with registered country! Country while login: `$country` - Registered Country: `$getCountry`"]);
                        }else{
                            $PDOIDX = $db -> prepare('UPDATE `users` SET `countryChanges` = :cChange WHERE `discordID` = :id');
                            $PDOIDX -> execute(array(':cChange' => $countryChanges + 1, ':id' => htmlspecialchars($user->id)));
                            $discord->channel->createMessage(['channel.id' => $logChannel, 'content' => "The account: `$userName` logged into a different country, the system will auto-ban him if he reaches 3 different country logins, current different country logins: `$countryChanges`."]);
                        }
                    }
                }
            }
        }

        try{
            $discord->channel->createMessage(
                [
                    'channel.id' => $logChannel,
                    'embed'      => [
                        'timestamp' => date("c"),
                        "color" => hexdec("#7289da"),
                        'title' => 'Login (DISCORD API) at: '.date('Y-m-d H:i:s'),
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
                                "name" => "UserName",
                                "value" => $userName
                            ],
                        ]
                    ],
                ]
            );
        }catch(Exception $e){
            logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN LOGIN DISCORD API. $e");
        }

        checkAccounts();
        session_regenerate_id(true);
        $success = 'Sucessfully logged in! Redirecting...';
        logUser($userID, $userName, 'Logged in');
        header('location: dashboard');
        die();
    }

  }catch(Exception $e){
    logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN SETTINGS. $e");
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
    if(isset($_POST['submit2faToken'])){

        if(!empty($_POST['verifyToken'])){
        
            if(!empty($_POST['userName']) && !empty($_POST['id']) && !empty($_POST['token'])){

                if (\Volnix\CSRF\CSRF::validate($_POST)) {

                    $user = htmlspecialchars($_POST['userName']);
                    $FAtoken = htmlspecialchars($_POST['verifyToken']);
                    $userID = htmlspecialchars($_POST['id']);
                    $token = htmlspecialchars($_POST['token']);
            
                    $tfa = new TwoFactorAuth('IXWare');
                    $secretkey = pdoQuery($db, "SELECT `2FAToken` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                    
                    if($tfa->verifyCode(htmlspecialchars(decrypt($secretkey)), $FAtoken) !== true){
                        $error = "The 2FA Token is invalid.";
                    }else{
                        $_SESSION['username'] = $user;
                        $_SESSION['ID'] = $userID;
                        $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
                        $_SESSION['IP'] = md5(realIP());
                        $_SESSION['TOKEN'] = $token;
            
                        // Must be same as on node
                        $key = 'twqJnNv7uQZjqkKvkLhTeLJur67WPNVxTmW3mresAzEv5WsnUJ';
            
                        if (empty($_SESSION['JWTToken'])) {
                            $data = [
                                'userid' => $userID,
                                'iat' => time(),
                                'exp' => strtotime('+90 days', time())
                            ];
            
                            $_SESSION['JWTToken'] = \Firebase\JWT\JWT::encode($data, $key);
                        }
            
                        $PDOToken = $db -> prepare('UPDATE `users` SET `token` = :token WHERE `username` = :username');
                        $PDOToken -> execute(array(':token' => $token, ':username' => $user));
            
                        setcookie("login", $user, time() + 86400);
            
                        checkAccounts();
                        session_regenerate_id(true);
                        logUser($userID, $user, 'Logged in');
                        header('location: dashboard');
                        die();
                    }
                }else{
                    header("HTTP/1.1 401 Unauthorized");
                    echo file_get_contents('includes/layout/error/401.php');
                    die();
                }
            }else{
                $error = "Unauthorized";
            }
        }
    }
    if (isset($_GET['code']) && isset($_GET['scope'])) {
        try {

            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token['access_token']);
            
            // get profile info
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            $email =  $google_account_info->email;
            $name =  $google_account_info->given_name;
            $new_name = str_replace(' ', '', $name);
            if (strlen($new_name) < 4 ){
                $new_name = $new_name . getID(5);
            }
            if(strlen($new_name) > 10){
                $new_name = substr($new_name, 0, 6);
            }
            $picture =  $google_account_info->picture;

            if (checkEmail($email) !== 0){
                $userID = pdoQuery($db, "SELECT `ID` FROM `users` WHERE `email`=?", [htmlspecialchars($email)])->fetchColumn();
                $userName = pdoQuery($db, "SELECT `username` FROM `users` WHERE `email`=?", [htmlspecialchars($email)])->fetchColumn();
                $getCountry = pdoQuery($db, "SELECT `country` FROM `users` WHERE `email`=?", [htmlspecialchars($email)])->fetchColumn();
                $country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_countryName'};
                $checkBan = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `email`=?", [htmlspecialchars($email)])->fetchColumn();
                $token = getID(50);

                $_SESSION['username'] = $userName;
                $_SESSION['ID'] = $userID;
                $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
                $_SESSION['IP'] = md5(realIP());
                $_SESSION['TOKEN'] = $token;

                // Must be same as on node
                $key = 'twqJnNv7uQZjqkKvkLhTeLJur67WPNVxTmW3mresAzEv5WsnUJ';

                if (empty($_SESSION['JWTToken'])) {
                    $data = [
                        'userid' => $userID,
                        'iat' => time(),
                        'exp' => strtotime('+90 days', time())
                    ];

                    $_SESSION['JWTToken'] = \Firebase\JWT\JWT::encode($data, $key);
                }

                $PDOToken = $db -> prepare('UPDATE `users` SET `token` = :token WHERE `username` = :username');
                $PDOToken -> execute(array(':token' => $token, ':username' => $userName));

                setcookie("login", $userName, time() + 86400);

                if($checkBan == 0){

                    if (!empty($country) & !empty($getCountry)){
                        if ($getCountry != $country){
                            if($getRank != "Admin" && $getRank == "Premium"){
                                $countryChanges = pdoQuery($db, "SELECT `countryChanges` FROM `users` WHERE `email`=?", [htmlspecialchars($email)])->fetchColumn();
    
                                if($countryChanges >= 3){
    
                                    $date1 = new DateTime();
                                    $date1->modify('+1 Week');
                                    $date1 = $date1->format('d-m-Y');
    
                                    $PDOID = $db -> prepare('UPDATE `users` SET `banned` = :ban WHERE `email` = :email');
                                    $PDOID -> execute(array(':ban' => $date1, ':email' => $email));
                                    $PDOID1 = $db -> prepare('UPDATE `users` SET `banreason` = :ban WHERE `email` = :email');
                                    $PDOID1 -> execute(array(':ban' => 'AUTO-BAN - Different Location. If you think that you got false-banned then create a ticket.', ':email' => $email));
                                    $discord->channel->createMessage(['channel.id' => $logChannel, 'content' => "AUTO-Banned `$user` due to different country.\r\nCountry from User: `$userName` doesn't match with registered country! Country while login: `$country` - Registered Country: `$getCountry`"]);
                                }else{
                                    $PDOIDX = $db -> prepare('UPDATE `users` SET `countryChanges` = :cChange WHERE `email` = :email');
                                    $PDOIDX -> execute(array(':cChange' => $countryChanges + 1, ':email' => $email));
                                    $discord->channel->createMessage(['channel.id' => $logChannel, 'content' => "The account: `$userName` logged into a different country, the system will auto-ban him if he reaches 3 different country logins, current different country logins: `$countryChanges`."]);
                                }
                            }
                        }
                    }
                }

                try{
                    $discord->channel->createMessage(
                        [
                            'channel.id' => $logChannel,
                            'embed'      => [
                                'timestamp' => date("c"),
                                "color" => hexdec("#DB4437"),
                                'title' => 'Login (GOOGLE API) at: '.date('Y-m-d H:i:s'),
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
                                        "name" => "UserName",
                                        "value" => $userName
                                    ],
                                ]
                            ],
                        ]
                    );
                }catch(Exception $e){
                    logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN LOGIN DISCORD API. $e");
                }

                checkAccounts();
                session_regenerate_id(true);
                $success = 'Sucessfully logged in! Redirecting...';
                logUser($userID, $userName, 'Logged in');
                header('location: dashboard');
                die();
            }else{
                $id = getID(50);
                $id2 = getID(5);
                $givenusername = htmlspecialchars("GIX-$new_name-$id2");
                if (checkUserName("GIX-$new_name-$id2") == 0){

                    if(checkID($id) == 0){

                        if(checkEmail($email) == 0){
    
                            if (checkIP(hash('sha256', realIP())) == 0){

                                $country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_countryName'};
                                $PDORegister = $db -> prepare('INSERT INTO `users` VALUES(:ID, :user, :password, :email, :country, :ip, :rank, 0, 0, 0, :subscription, :date, 0, :avatar, :notification, :timestamp, :hash, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0)');
                                $PDORegister -> execute(array(':ID' => $id, ':user' => htmlspecialchars($givenusername), ':password' => '0', ':email' => htmlspecialchars($email), ':country' => htmlspecialchars($country), ':ip' => htmlspecialchars(hash('sha256', realIP())), ':rank' => 'User', ':subscription' => 'None', ':date' => htmlspecialchars(date('d-m-Y')), ':avatar' => htmlspecialchars($picture), ':notification' => 'resources/audio/notification.ogg', ':timestamp' => '0', ':hash' => 'accActivated'));
                            
                                
                                $success = "Successfully registered, you can now log in via google.";
                                try{
                                    $discord->channel->createMessage(
                                        [
                                            'channel.id' => $logChannel,
                                            'embed'      => [
                                                'timestamp' => date("c"),
                                                "color" => hexdec("#DB4437"),
                                                'title' => 'Registered (GOOGLE API) at: '.date('Y-m-d H:i:s'),
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
                                                        "name" => "UserName",
                                                        "value" => $givenusername
                                                    ],
                                                ]
                                            ],
                                        ]
                                    );
                                }catch(Exception $e){
                                    logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN LOGIN DISCORD API. $e");
                                }
                            }else{
                                $error = 'You already have an account!';
                            }
                        }else{
                            $error = 'E-Mail is already taken!';
                        }
                    }else{
                        $error = 'Registration failed due to ID generation, please log in again!';
                    }
                }else{
                    $error = "An error occured, please try again.";
                }
            }
        
        } catch (RequestException $e) {
        
            header("HTTP/1.1 400 Bad Request");
            echo file_get_contents('includes/layout/error/400.php');
            die();
            
        } catch (\Exception $e) {
        
            header("HTTP/1.1 400 Bad Request");
            echo file_get_contents('includes/layout/error/400.php');
            die();

        }
    }
    if (isset($_GET["pwReset"])){
        if(empty($_GET["pwReset"])){
            $success = "You've successfully set a new password. You can now login with your new password.";
        }
    }
    if (isset($_GET["success"])){
        if(empty($_GET["success"])){
            $success = 'Successfully registered! Check your inbox/spam folder to activate your account!';
        }
    }
    if (isset($_GET["banned"])){
        if(!empty($_GET['reason'])){
            $reason = htmlspecialchars($_GET["reason"]);
            $error = "You are banned, reason: $reason";
        }
    }
    if (isset($_GET["activated"])){
        if(empty($_GET["activated"])){
            $success = 'Account succesfully activated!';
        }
    }
    if (isset($_GET["invalidT"])){
        if(empty($_GET["invalidT"])){
            $error = 'Activation Token is invalid!';
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ixLogin'])) {

        $secretKey = ' x';
        $captcha = $_POST['g-recaptcha-response'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response,true);

        if(intval($responseKeys["success"]) !== 1) {
            $error = 'You need to check the captcha to proceed!';
        } else {

            if (isset($_POST['ixLogin'])){

                if (\Volnix\CSRF\CSRF::validate($_POST)) {
                    $user = htmlspecialchars($_POST["username"]);
                    $pass = htmlspecialchars($_POST["password"]);

                    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();

                    if (checkLogin() == '0' && $getRank != "Admin"){
                        $error = 'Logins are currently disabled, please check again in some minutes.';
                        logUser("0", "Guest", "Tried to login but logins are disabled, used UserName: $user");
                    }

                    if (empty($error)){
    
                        if(checkUserName($user) == 1){
                            $gethashedPW = pdoQuery($db, "SELECT `pass` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn()."\n";
                            $checkBan = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $getCountry = pdoQuery($db, "SELECT `country` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $registrationDate = pdoQuery($db, "SELECT `registrationDate` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $getIP = pdoQuery($db, "SELECT `ip` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $rank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $secretToken = pdoQuery($db, "SELECT `secretToken` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $faenabled = pdoQuery($db, "SELECT `2FA` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $googleAPI = pdoQuery($db, "SELECT `googleAPI` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $discordAPI = pdoQuery($db, "SELECT `discordAPI` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                            $userID = pdoQuery($db, "SELECT `id` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();

                            $realIP = hash('sha256', realIP());
                            $country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_countryName'};
                            $city = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_city'};


                            if($googleAPI == 1){
                                $error = "You need to log in using the google button.";
                            }
                            elseif($discordAPI == 1){
                                $error = "You need to log in using the discord button.";
                            }

                            if(empty($error)){

                                if(password_verify($pass, trim($gethashedPW))){
        
                                    if (empty($error)){
                                        $userID = pdoQuery($db, "SELECT `ID` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                                        $userName = pdoQuery($db, "SELECT `username` FROM `users` WHERE `username`=?", [htmlspecialchars($user)])->fetchColumn();
                                        $token = getID(50);


                                        if (empty($getIP)){
                                            $getIP = 'Empty';
                                        }
                                        if (empty($getCountry)){
                                            $getCountry = 'Empty';
                                        }
                                        if (empty($realIP)){
                                            $realIP = 'Empty';
                                        }
                                        if (empty($country)){
                                            $country = 'Empty';
                                        }
                                        if (empty($city)){
                                            $city = 'Empty';
                                        }
                                        if ($getIP == $realIP){
                                            $match = "True";
                                        }else{
                                            $match = "False";
                                        }
                                        try{
                                            $discord->channel->createMessage(
                                                [
                                                    'channel.id' => $logChannel,
                                                    'embed'      => [
                                                        'timestamp' => date("c"),
                                                        "color" => hexdec("#a30a0a"),
                                                        'title' => 'Login at: '.date('Y-m-d H:i:s'),
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
                                                                "name" => "UserName",
                                                                "value" => $user
                                                            ],
                                                            [
                                                                "name" => "Registered IP",
                                                                "value" => $getIP
                                                            ],
                                                            [
                                                                "name" => "IP while login",
                                                                "value" => $realIP
                                                            ],
                                                            [
                                                                "name" => "Registered Country",
                                                                "value" => $getCountry
                                                            ],
                                                            [
                                                                "name" => "Country while login",
                                                                "value" => $country
                                                            ],
                                                            [
                                                                "name" => "City while login",
                                                                "value" => $city
                                                            ],
                                                            [
                                                                "name" => "IP Match",
                                                                "value" => $match
                                                            ],
                                                            [
                                                                "name" => "User-Agent while login",
                                                                "value" => $_SERVER['HTTP_USER_AGENT']
                                                            ],
                                                            [
                                                                "name" => "Registered Discord ID",
                                                                "value" => $discordID
                                                            ],
                                                        ]
                                                    ],
                                                ]
                                            );
                                        }catch(Exception $e){
                                            logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN LOGIN.");
                                        }
                                        
    
                                        if($checkBan == 0){
                                                 
                                            if (!empty($country) & !empty($getCountry)){
                                                if ($getCountry != $country){
                                                    if($getRank != "Admin" && $getRank == "Premium"){
                                                        $countryChanges = pdoQuery($db, "SELECT `countryChanges` FROM `users` WHERE `username`=?", [htmlspecialchars($userName)])->fetchColumn();
    
                                                        if($countryChanges >= 3){
    
                                                            $date1 = new DateTime();
                                                            $date1->modify('+1 Week');
                                                            $date1 = $date1->format('d-m-Y');
    
                                                            $PDOID = $db -> prepare('UPDATE `users` SET `banned` = :ban WHERE `username` = :username');
                                                            $PDOID -> execute(array(':ban' => $date1, ':username' => $userName));
                                                            $PDOID1 = $db -> prepare('UPDATE `users` SET `banreason` = :ban WHERE `username` = :username');
                                                            $PDOID1 -> execute(array(':ban' => 'AUTO-BAN - Different Location. If you think that you got false-banned then create a ticket.', ':username' => $userName));
                                                            $discord->channel->createMessage(['channel.id' => $logChannel, 'content' => "AUTO-Banned `$userName` due to different country.\r\nCountry from User: `$userName` doesn't match with registered country! Country while login: `$country` - Registered Country: `$getCountry`"]);
                                                        }else{
                                                            $PDOIDX = $db -> prepare('UPDATE `users` SET `countryChanges` = :cChange WHERE `username` = :username');
                                                            $PDOIDX -> execute(array(':cChange' => $countryChanges + 1, ':username' => $userName));
                                                            $discord->channel->createMessage(['channel.id' => $logChannel, 'content' => "The account: `$userName` logged into a different country, the system will auto-ban him if he reaches 3 different country logins, current different country logins: `$countryChanges`."]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                        if($faenabled == 1){

                                            echo "<div class='modal fade' id='showPW' tabindex='-1' role='dialog' aria-hidden='true'>
                                            <div class='modal-dialog modal-lg' role='document'>
                                              <div class='modal-content'>
                                                <div class='modal-header bg-dark text-darkwhite'>
                                                  <h5 class='modal-title w-100 modaltext'>Verify 2FA</h5>
                                                  <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>
                                                </div>
                                                <div class='modal-body bg-dark text-darkwhite'>
                                                  <form method='POST'>
                                                    <div class='md-form mt-0'>
                                                      <input type='text' placeholder='Token' name='verifyToken' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                                      <input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/>
                                                      <input type='hidden' name='userName' value='$userName'/>
                                                      <input type='hidden' name='id' value='$userID'/>
                                                      <input type='hidden' name='token' value='$token'/>
                                                    </div>
                                                    <button type='submit' name='submit2faToken' class='btn btn-outline-primary waves-effect mt-3 center-button settings-button' style='width: 100%;'>Submit</button>
                                                  </form>
                                                </div>
                                              </div>
                                            </div>
                                          </div>"; 
                                        }else{

                                            $_SESSION['username'] = $userName;
                                            $_SESSION['ID'] = $userID;
                                            $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
                                            $_SESSION['IP'] = md5(realIP());
                                            $_SESSION['TOKEN'] = $token;
                    
                                            // Must be same as on node
                                            $key = 'twqJnNv7uQZjqkKvkLhTeLJur67WPNVxTmW3mresAzEv5WsnUJ';

                                            if (empty($_SESSION['JWTToken'])) {
                                                $data = [
                                                    'userid' => $userID,
                                                    'iat' => time(),
                                                    'exp' => strtotime('+90 days', time())
                                                ];

                                                $_SESSION['JWTToken'] = \Firebase\JWT\JWT::encode($data, $key);
                                            }

                                            $PDOToken = $db -> prepare('UPDATE `users` SET `token` = :token WHERE `username` = :username');
                                            $PDOToken -> execute(array(':token' => $token, ':username' => $userName));
            
                                            setcookie("login", $userName, time() + 86400);

                                            //checkAccounts();
                                            session_regenerate_id(true);
                                            logUser($userID, $user, 'Logged in');
                                            header('location: dashboard');
                                            die();
                                        }
                                    }
                                }else{
                                    $error = "Password isn't right!";
                                    logUser("0", "Guest", 'Tried to log in - wrong password');
                                }
                            }
                        }else{
                            $error = 'User doesnt exist!';
                            logUser("0", "Guest", "Tried to log in with a non-existent UserName, used UserName: $user");
                        }
                    }
                } else {
                    header("HTTP/1.1 401 Unauthorized");
                    echo file_get_contents('includes/layout/error/401.php');
                    die();
                }
            }
        }

    }
    ?>

<body>

    <!--Main Navigation-->
    <nav class="navbar navbar-expand-lg bg-transparent navbar-dark fixed-top scrolling-navbar">
      <div class="logo-wrapper waves-light mt-1 ml-3 d-none d-sm-block" style="width:100%">
        <a class="navbar-brand" href="login">ixwhere.online</a>
      </div>
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-7"
          aria-controls="navbarSupportedContent-7" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent-7">
          <ul class="navbar-nav nav-flex-icons ml-auto">
            <li class="nav-item">
              <a class="nav-link waves-effect waves-light" href="<?php echo htmlspecialchars(discordserver()); ?>" target="_blank"><i class="fab fa-discord"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link waves-effect waves-light" href="https://youtu.be/PSSq5jKGNKU" target="_blank"><i class="fab fa-youtube"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <section id="particles-js" class="purple-gradient-rgba" style="position: fixed;height:100%;width:100%;">
        <div class="mask">
            <div class="container-fluid h-100 d-flex justify-content-center align-items-center ">
                <div class="row">
                    <div class="col-xl-4 col-12 col-sm-10 col-lg-7 col-md-11 centered">
                        <div class="card opacity-card">
                            <div class="card-body d-flex flex-column">
                                <div style="display:inline-block;">
                                    <a href="<?= $client->createAuthUrl() ?>" class="hvr-icon-grow float-right ml-2" title="Sign In with Google"><i class="fab fa-google-plus-g text-google fa-lg text-white hvr-icon d-none d-sm-block"></i></a>
                                    <a href="?action=login" class="hvr-icon-grow float-right" title="Sign In with Discord"><i class="fab fa-discord text-discordblue fa-lg text-white hvr-icon d-none d-sm-block"></i></a>
                                    <a href="index" class="hvr-icon-back float-left"><i class="fad fa-arrow-left fa-lg text-white hvr-icon d-none d-sm-block"></i></a>
                                </div>
                                <h4 class="text-center text-white mt-5">IXWare</h4>
                                <p class="text-center text-white">Sign into your IXWare account</p>
                                <form class="mt-4 form-material" method="POST">
                                    <div class="d-flex justify-content-center">
                                        <div class="form-group mb-3 w-75">
                                            <div class="d-flex justify-content-center">
                                                <?php
                                                    if(!empty($error)){
                                                        echo '<div class="animated fadeIn sticky-top">'.error(htmlspecialchars($error)).'</div>';
                                                    }
                                                    if(!empty($success)){
                                                        echo '<div class="animated fadeIn sticky-top">'.success(htmlspecialchars($success)).'</div>';
                                                    }
                                                ?>
                                            </div>
                                            <input type="text" class="form-control text-darkwhite mb-3" autocomplete="off" name="username" placeholder="UserName" value="<?php if (isset($_POST['username'])){ echo htmlspecialchars($_POST['username']); }else{ echo ''; } ?>" required />
                                            <input type="password" class="form-control text-darkwhite mb-3" autocomplete="off" name="password" placeholder="Password" value="<?php if (isset($_POST['password'])){ echo htmlspecialchars($_POST['password']); }else{ echo ''; } ?>" required />
                                            <div class="g-recaptcha mt-2" data-sitekey="6LdqlroZAAAAAAC0xghvzhAO2giUnX42otLmOetF" required></div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button name="ixLogin" value="login" type="submit" class="btn btn-outline-light btn-sm text-darkwhite w-75 btn-login"><i class="fad fa-sign-in-alt fa-lg mr-2"></i>Sign In</button>
                                    </div>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                    <p class="text-center text-darkwhite mt-3" style="margin-bottom: 0rem !important;">
                                        <b>You ain't registered?</b><a href="registration" class="text-primary m-l-5"> Sign Up</a>
                                    </p>
                                    <p class="text-center text-darkwhite mt-2" style="margin-top: 0px;">
                                        <b>Forgotten your password?</b><a href="password-reset" class="text-primary m-l-5"> Reset it now</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <!--Main Navigation-->


  <!-- SCRIPTS -->

  <?php require_once 'includes/layout/footer.php'; ?>
  <!-- Particles -->
  <script src="resources/js/particles.js"></script>
  <script src="resources/js/app.js"></script>

</body>
</html>
