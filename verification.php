<?php 
    $title = 'Verification';
    include_once 'includes/layout/header.php';
    include_once "includes/functions.php";
    include_once "includes/botconfig.php";
    include_once __DIR__.'/../vendor/autoload.php';

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
    'redirect_uri' => 'https://ixwhere.online/verification',
    'response_type' => 'code',
    'scope' => 'identify guilds'
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
    'redirect_uri' => 'https://ixwhere.online/verification',
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;


  header('Location: ' . $_SERVER['PHP_SELF']);
}

if(session('access_token')) {
  $user = apiRequest($apiURLBase);

  $discordID = htmlspecialchars($user->id);
  if(checkVerification($user->id) == 1){
        
    if(checkVerified($user->id) == 0){

        try{
            
            if(checkDiscordID($user->id) == 1){
        
                $user = pdoQuery($db, "SELECT `username` FROM `users` WHERE `discordID`=?", [htmlspecialchars($discordID)])->fetchColumn();
                $checkBanned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `discordID`=?", [htmlspecialchars($discordID)])->fetchColumn();
                $checkBanreason = pdoQuery($db, "SELECT `banreason` FROM `users` WHERE `discordID`=?", [htmlspecialchars($discordID)])->fetchColumn();

                if($checkBanned == 1){

                    try{
                        $discord->channel->createMessage(
                            [
                                'channel.id' => $verificationChannel,
                                'embed'      => [
                                    'timestamp' => date("c"),
                                    "color" => hexdec("#ff0000"),
                                    'title' => 'On-Site Verification Failed '.date('Y-m-d H:i:s'),
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
                                            "value" => htmlspecialchars($user)
                                        ],
                                        [
                                            "name" => "ID",
                                            "value" => htmlspecialchars($discordID)
                                        ],
                                        [
                                            "name" => "Reason",
                                            "value" => "Banned"
                                        ],
                                        [
                                            "name" => "Ban Reason",
                                            "value" => htmlspecialchars($checkBanreason)
                                        ],
                                    ]
                                ],
                            ]
                        );
                    }catch(Exception $e){}

                    $PDODelete = $db -> prepare('DELETE FROM `discord_verification` WHERE `id` = :id');
                    $PDODelete -> execute(array(':id' => $id));
                    try{
                        $discord->channel->createMessage(['channel.id' => $publicVerifyChannel, 'content' => "<@$discordID> you are banned from IXWare and can't get verified."]);
                    }catch(Exception $e){}
                }
            }

            // Input your options for this query including your optional API Key and query flags.
            $proxycheck_options = array(
            'API_KEY' => '6l6v42-5704xl-8et094-01q584', // Your API Key.
            'ASN_DATA' => 1, // Enable ASN data response.
            'DAY_RESTRICTOR' => 7, // Restrict checking to proxies seen in the past # of days.
            'VPN_DETECTION' => 1, // Check for both VPN's and Proxies instead of just Proxies.
            'RISK_DATA' => 1, // 0 = Off, 1 = Risk Score (0-100), 2 = Risk Score & Attack History.
            'INF_ENGINE' => 1, // Enable or disable the real-time inference engine.
            'TLS_SECURITY' => 0, // Enable or disable transport security (TLS).
            'QUERY_TAGGING' => 1, // Enable or disable query tagging.
            );
            
            $result_array = \proxycheck\proxycheck::check(realIP(), $proxycheck_options);
            if ($result_array['block'] == "yes" ) {
            
                try{
                    $discord->channel->createMessage(
                        [
                            'channel.id' => $verificationChannel,
                            'embed'      => [
                                'timestamp' => date("c"),
                                "color" => hexdec("#ff0000"),
                                'title' => 'On-Site Verification Failed '.date('Y-m-d H:i:s'),
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
                                        "name" => "ID",
                                        "value" => htmlspecialchars($discordID)
                                    ],
                                    [
                                        "name" => "Reason",
                                        "value" => htmlspecialchars($result_array['block_reason'])
                                    ],
                                    [
                                        "name" => "IP",
                                        "value" => realIP()
                                    ],
                                ]
                            ],
                        ]
                    );
                }catch(Exception $e){}
                try{
                    $discord->channel->createMessage(['channel.id' => $publicVerifyChannel, 'content' => "<@$discordID> verification failed, contact an administrator."]);
                }catch(Exception $e){}
            }

            if(empty($error)){

                try{

                    $check = json_encode($discord->guild->getGuildMember(['guild.id' => $guildid, 'user.id' => (int)$discordID]));
                    if(strpos($check, "nick")){

                        $discordJSON = json_decode($check, 1);
                        $discordJSON1 = json_encode($discordJSON["user"]);
                        $discordJSON2 = json_decode($discordJSON1, 1);

                        $PDOLog = $db -> prepare('INSERT INTO `discord_verified` VALUES(:id, :username, :ip, :agent, :date)');
                        $PDOLog -> execute(array(':id' => htmlspecialchars($discordJSON2['id']), ':username' => htmlspecialchars($discordJSON2['username']) . '#' . htmlspecialchars($discordJSON2['discriminator']), ':ip' => encrypt(realIP()), ':agent' => htmlspecialchars($_SERVER['HTTP_USER_AGENT']), ':date' => date("Y-m-d H:i:s")));
                        
                        $PDODelete = $db -> prepare('DELETE FROM `discord_verification` WHERE `discordID` = :id');
                        $PDODelete -> execute(array(':id' => $discordID));

                        if(checkDiscordID($discordID)){

                            $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$discordID, 'role.id' => $registeredID]);  

                            $discordIDRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `discordID`=?", [htmlspecialchars($discordID)])->fetchColumn(0);
                            if($discordIDRank == "Premium"){

                                $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$discordID, 'role.id' => $membershipID]);  
                            }
                        }
                        $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$discordID, 'role.id' => $regularID]);  

                        try{
                            $discord->channel->createMessage(['channel.id' => $publicVerifyChannel, 'content' => " <@$discordID> Successfully verified."]);
                        }catch(Exception $e){}


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

                            //$discord->guild->createGuildBan(['guild.id' => $guildid, 'user.id' => (int)$id, 'delete-message-days' => "7", 'reason' => "Alts"]);
                        }else{
                            $string = "None";
                            $color = "#00ff04";
                        }
                        $success = "Successfully verified your discord account.";

                        $user = pdoQuery($db, "SELECT `username` FROM `discord_verified` WHERE `id`=?", [htmlspecialchars($discordID)])->fetchColumn(0);
                        try{
                            $discord->channel->createMessage(
                                [
                                    'channel.id' => $verificationChannel,
                                    'embed'      => [
                                        'timestamp' => date("c"),
                                        "color" => hexdec($color),
                                        'title' => 'On-Site Verification  '.date('Y-m-d H:i:s'),
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
                                                "value" => htmlspecialchars($user)
                                            ],
                                            [
                                                "name" => "ID",
                                                "value" => htmlspecialchars($discordID)
                                            ],
                                            [
                                                "name" => "Alt's Detected (Banned Main and Alt)",
                                                "value" => "```$string```"
                                            ],
                                        ]
                                    ],
                                ]
                            );
                        }catch(Exception $e){}
                    }else{

                        $PDODelete = $db -> prepare('DELETE FROM `discord_verification` WHERE `id` = :id');
                        $PDODelete -> execute(array(':id' => $id));
                        $error = "An error occured. Make sure you are in the discord server.";
                    }
                }catch(Exception $e){
                    $error = "An error occured. Ignore this message if you still got verified in the discord server.";
                }
            }
        }catch(Exception $e){
            
            if(checkVerified($discordUserID) == 1){

                $PDODelete = $db -> prepare('DELETE FROM `discord_verified` WHERE `id` = :id');
                $PDODelete -> execute(array(':id' => $discordUserID));
            }
            try{
                $discord->channel->createMessage(['channel.id' => $publicVerifyChannel, 'content' => "<@$discordID> couldn't verify you, discord bot is most likely ratelimited, please try again later."]);
            }catch(Exception $e){}
        }
    }
  }
}

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

<body>

    <!--Main Navigation-->
    <nav class="navbar navbar-expand-lg bg-transparent navbar-dark fixed-top scrolling-navbar">
      <div class="logo-wrapper waves-light mt-1 ml-3 d-none d-sm-block" style="width:100%">
        <a class="navbar-brand" href="index">ixwhere.online</a>
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
                                    <button type='button' data-toggle="modal" data-target="#view-modal" style="border: none; outline: none; background: none; cursor: pointer; color: #fff; padding: 0; text-decoration: underline; font-family: inherit; font-size: inherit;">On-Site verification safety</button>
                                </div>
                                <h4 class="text-center text-white mt-5">IXWare</h4>
                                <p class="text-center text-white">We need to verify your discord account</p>
                                <?php
                                    if(!empty($error)){
                                        echo '<div class="animated fadeIn sticky-top">'.error(htmlspecialchars($error)).'</div>';
                                    }
                                    if(!empty($success)){
                                         echo '<div class="animated fadeIn sticky-top">'.success(htmlspecialchars($success)).'</div>';
                                    }
                                ?>
                                <div class="text-center">
                                    <a href="?action=login" class="btn btn-outline-light text-darkwhite w-75 btn-login" style="width: 100%;">Verify with discord</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <!--Main Navigation-->


<!-- Central Modal Small -->
<div class="modal fade" id="view-modal" tabdashboard="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">

  <!-- Change class .modal-sm to change the size of the modal -->
  <div class="modal-dialog modal-lg" role="document">


    <div class="modal-content">
      <div class="modal-header bg-dark text-darkwhite">
        <h5 class="modal-title w-100" id="myModalLabel">On-Site verification safety</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="modalButton">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-dark text-darkwhite">
        The on-site verification works via OAuth2 which means we work with the discord api to verify your discord account in our server. We only have access to your id and your guilds. Your ID will be stored in our database for future verifications.
      </div>
    </div>
  </div>
</div>
<!-- Central Modal Small -->

  <!-- SCRIPTS -->

  <?php require_once 'includes/layout/footer.php'; ?>
  <!-- Particles -->
  <script src="resources/js/particles.js"></script>
  <script src="resources/js/app.js"></script>

</body>
</html>
