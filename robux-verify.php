<?php 
    $title = 'Robux Verify';
    include_once 'includes/layout/header.php';
    include_once "includes/functions.php";
    include_once "includes/email.php";
    include_once "includes/botconfig.php";
    include_once __DIR__.'/../vendor/autoload.php';
    include_once 'loader.php';

    Loader::register('lib','RobThree\\Auth');
    use \RobThree\Auth\TwoFactorAuth;

    $descVerify = "IX_" . getID(10);
?>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $secretKey = ' x';
        $captcha = $_POST['g-recaptcha-response'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response,true);

        if(intval($responseKeys["success"]) !== 1) {
            $error = 'You need to check the captcha to proceed!';
        } else {


            if (\Volnix\CSRF\CSRF::validate($_POST)) {

                if(!empty($_POST['ixConfirm'])){

                    if (!empty($_POST['membership'])){

                        if (!empty($_POST['descVerify'])){

                            if (!empty($_POST['rdiscord'])){

                                $membership = htmlspecialchars($_POST['membership']);
                                $verifyCode = htmlspecialchars($_POST['descVerify']);
                                $rdiscord = htmlspecialchars($_POST['rdiscord']);
    
                                try{
                                    try{
                                        $check = json_encode($discord->guild->getGuildMember(['guild.id' => $guildid, 'user.id' => (int)$rdiscord]));
                                    }catch(Exception $e){
                                        $error = "Make sure you are in the discord server. Check if your discord id is correct.";
                                    }
                                    if(empty($error)){

                                        if(strpos($check, "nick")){
    
                                            if($membership == "monthly" || $membership == "3months"){
                    
                                                if (!empty($_POST['rUsername'])){
                    
                                                    $robloxUsername = htmlspecialchars($_POST['rUsername']);
                    
                                                    $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_URL, "https://api.roblox.com/users/get-by-username?username=$robloxUsername");
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                                                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                                    $profile = curl_exec($ch);
                                                    curl_close($ch);
                                                    if(!strpos($profile, "User not found")){
                                                        
                                                        $robloxIDJSON = json_decode($profile, 1);
                                                        $robloxID = htmlspecialchars($robloxIDJSON['Id']);
                    
                                                        $ch = curl_init();
                                                        curl_setopt($ch, CURLOPT_URL, "https://inventory.roblox.com/v1/users/$robloxID/can-view-inventory");
                                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                                                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                                        $result = curl_exec($ch);
                                                        curl_close($ch);
                                                        if(strpos($result, "true")){
                    
                                                            $ch = curl_init();
                                                            curl_setopt($ch, CURLOPT_URL, "https://users.roblox.com/v1/users/$robloxID");
                                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                                                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                                            $resultX = json_decode(curl_exec($ch), 1);
                                                            curl_close($ch);

                                                            if($resultX["description"] == $verifyCode){

                                                                if(checkRobloxPayment($robloxUsername) == 0){
                    
                                                                    $monthlyID = "14563043";
                                                                    $threeMonthsID = "14563046";
                    
                                                                    if($membership == "monthly"){
                            
                                                                        $ch = curl_init();
                                                                        curl_setopt($ch, CURLOPT_URL, "https://inventory.roblox.com/v1/users/$robloxID/items/GamePass/$monthlyID");
                                                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                                                                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                                                        $profile = curl_exec($ch);
                                                                        curl_close($ch);
                                                                        
                                                                        if(strpos($profile, $monthlyID)){
                    
                                                                            $PDOLog = $db -> prepare('INSERT INTO `robux_payments` VALUES(:username, :membership, :date)');
                                                                            $PDOLog -> execute(array(':username' => htmlspecialchars($robloxUsername), ':membership' => 'Monthly', ':date' => date("Y-m-d H:i:s")));
                                                        
                                                                            $license = htmlspecialchars($robloxUsername . '-' . $robloxID . '-' . getID(15));
                                                                            $PDOLog1 = $db -> prepare('INSERT INTO `licenses` VALUES(:license, :type)');
                                                                            $PDOLog1 -> execute(array(':license' => hash('sha256', $license), ':type' => 'Monthly'));
    
                                                                            try{
                                                                                $dm = $discord->user->createDm(['recipient_id' => (int)$rdiscord]);
                                                                                $discord->channel->createMessage(
                                                                                    [
                                                                                        'channel.id' => $dm->id,
                                                                                        'embed'      => [
                                                                                            'timestamp' => date("c"),
                                                                                            "color" => hexdec("#15ff00"),
                                                                                            'title' => ':partying_face: Thanks for purchasing IXWare :partying_face:',
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
                                                                                                    "name" => "License",
                                                                                                    "value" => $license
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Membership",
                                                                                                    "value" => "Monthly"
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Next step",
                                                                                                    "value" => "Visit https://ixwhere.online/ix-licenses and enter your license, after you did that press on 'Activate'"
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Role on discord",
                                                                                                    "value" => "You should automatically get your roles after activating your license."
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Support",
                                                                                                    "value" => "We would appreciate if you don't message any administrator, just create a ticket on https://ixwhere.online/support and be patient."
                                                                                                ],
                                                                                            ]
                                                                                        ],
                                                                                    ]
                                                                                );
                                                                            }catch(Exception $e){
        
                                                                                $PDODelete = $db -> prepare('DELETE FROM `robux_payments` WHERE `robloxusername` = :username');
                                                                                $PDODelete -> execute(array(':username' => $robloxUsername));
        
                                                                                $PDODelete1 = $db -> prepare('DELETE FROM `licenses` WHERE `license` = :license');
                                                                                $PDODelete1 -> execute(array(':license' => $license));
        
                                                                                $error = "An error occured, make sure your direct messages are enabled.";
                                                                            }
                                                                            $success = "Check your discord direct messages.";
                                                                        }else{
                                                                            $error = "You didn't buy the 'Monthly' gamepass.";
                                                                        }
                                                                    }elseif($membership == "3months"){
                            
                                                                        $ch = curl_init();
                                                                        curl_setopt($ch, CURLOPT_URL, "https://inventory.roblox.com/v1/users/$robloxID/items/GamePass/$threeMonthsID");
                                                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                                                                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                                                        $profile = curl_exec($ch);
                    
                                                                        if(strpos($profile, $threeMonthsID)){
                                                                            
                                                                            $PDOLog = $db -> prepare('INSERT INTO `robux_payments` VALUES(:username, :membership, :date)');
                                                                            $PDOLog -> execute(array(':username' => $robloxUsername, ':membership' => '3Months', ':date' => date("Y-m-d H:i:s")));
                    
                                                                            $license = htmlspecialchars($robloxUsername . '-' . $robloxID . '-' . getID(15));
                                                                            $PDOLog1 = $db -> prepare('INSERT INTO `licenses` VALUES(:license, :type)');
                                                                            $PDOLog1 -> execute(array(':license' => hash('sha256', $license), ':type' => '3Months'));
    
                                                                            try{
                                                                                $dm = $discord->user->createDm(['recipient_id' => (int)$rdiscord]);
                                                                                $discord->channel->createMessage(
                                                                                    [
                                                                                        'channel.id' => $dm->id,
                                                                                        'embed'      => [
                                                                                            'timestamp' => date("c"),
                                                                                            "color" => hexdec("#15ff00"),
                                                                                            'title' => ':partying_face: Thanks for purchasing IXWare :partying_face:',
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
                                                                                                    "name" => "License",
                                                                                                    "value" => $license
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Membership",
                                                                                                    "value" => "Monthly"
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Next step",
                                                                                                    "value" => "Visit https://ixwhere.online/ix-licenses and enter your license, after you did that press on 'Activate'"
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Role on discord",
                                                                                                    "value" => "You should automatically get your roles after activating your license."
                                                                                                ],
                                                                                                [
                                                                                                    "name" => "Support",
                                                                                                    "value" => "We would appreciate if you don't message any administrator, just create a ticket on https://ixwhere.online/support and be patient."
                                                                                                ],
                                                                                            ]
                                                                                        ],
                                                                                    ]
                                                                                );
                                                                            }catch(Exception $e){
        
                                                                                $PDODelete = $db -> prepare('DELETE FROM `robux_payments` WHERE `robloxusername` = :username');
                                                                                $PDODelete -> execute(array(':username' => $robloxUsername));
        
                                                                                $PDODelete1 = $db -> prepare('DELETE FROM `licenses` WHERE `license` = :license');
                                                                                $PDODelete1 -> execute(array(':license' => $license));
        
                                                                                $error = "An error occured, make sure your direct messages are enabled.";
                                                                            }
                                                                            $success = "Check your discord direct messages.";
                                                                        }else{
                                                                            $error = "You didn't buy the '3Months' gamepass.";
                                                                        }
                                                                    }
                                                                }else{
                                                                    $error = "Roblox User already verified his payment.";
                                                                }
                                                            }else{
                                                                $error = "Make sure to follow the instructions.";
                                                            }
                                                        }else{
                                                            $error = "You need to make your inventory public.";
                                                        }
                                                    }else{
                                                        $error = "Roblox Username doesn't exist.";
                                                    }
                                                                        
                                                }else{
                                                    $error = "Please enter your roblox username.";
                                                }
                                            }else{
                                                header("HTTP/1.1 401 Unauthorized");
                                                echo file_get_contents('includes/layout/error/401.php');
                                                die();
                                            }
                                        }else{
                                            $error = "Make sure you are in the IXWare discord server.";
                                        }
                                    }
                                }catch(Exception $e){
                                    $error = "An error occured, make sure you are in the discord server.";
                                }
                            }
                        }else{
                            header("HTTP/1.1 401 Unauthorized");
                            echo file_get_contents('includes/layout/error/401.php');
                            die();
                        }
                    }else{
                        $error = "Please select the membership which you've bought via robux.";
                    }
                }
            }else{
                header("HTTP/1.1 401 Unauthorized");
                echo file_get_contents('includes/layout/error/401.php');
                die();
            }
        }
    }
?>

<body>

    <!--Main Navigation-->
    <nav class="navbar navbar-expand-lg bg-transparent navbar-dark fixed-top scrolling-navbar">
      <div class="logo-wrapper waves-light mt-1 ml-3 d-none d-sm-block" style="width:100%">
        <a class="navbar-brand" href="registration">ixwhere.online</a>
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
                                    <button type='button' data-toggle="modal" data-target="#view-modal" style="border: none; outline: none; background: none; cursor: pointer; color: #fff; padding: 0; text-decoration: underline; font-family: inherit; font-size: inherit;">Instructions</button>
                                </div>
                                <h4 class="text-center text-white mt-5">IXWare</h4>
                                <p class="text-center text-white">Verify your robux payment</p>
                                <p class="text-center text-white p-margin">Make sure to read the instructions, otherwise your verification will fail.</p>
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
                                            <!-- Material Select -->
                                            <p class="text-darkwhite mt-3 p-margin">Which membership did you buy?</p>
                                            <select class="select" name="membership" >
                                                <option value="monthly" class="text-white" selected>Monthly</option>
                                                <option value="3months" class="text-white">3 Months</option>
                                            </select>
                                            <input type="text" class="form-control text-darkwhite mb-3 mt-3" value="<?php if (isset($_POST['rUsername'])){ echo htmlspecialchars($_POST['rUsername']); }else{ echo ''; } ?>" autocomplete="off" name="rUsername" placeholder="Roblox Username" required />
                                            <input type="text" class="form-control text-darkwhite mb-3 mt-3" value="<?php if (isset($_POST['rdiscord'])){ echo htmlspecialchars($_POST['rdiscord']); }else{ echo ''; } ?>" autocomplete="off" name="rdiscord" placeholder="Discord ID" required />
                                            <div class="g-recaptcha mt-2" data-sitekey="6LdqlroZAAAAAAC0xghvzhAO2giUnX42otLmOetF" required></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                    <input type="hidden" name="descVerify" value="<?= htmlspecialchars($descVerify) ?>"/>
                                    <div class="text-center mb-5">
                                        <button name="ixConfirm" value="confirm" type="submit" class="btn btn-outline-purple btn-sm text-darkwhite w-75 btn-login"><i class="fas fa-badge-check fa-lg mr-2"></i>Confirm</button>
                                    </div>
                                </form>
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
        <h5 class="modal-title w-100" id="myModalLabel">How to verify your payment</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="modalButton">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-dark text-darkwhite">
        <h5>First Step</h5>
        Make sure you are in the IXWare discord server
        <br><br><br>
        <h5>Second Step</h5>
        Make sure that your roblox inventory is public.
        <br><br><br>
        <h5>Third Step</h5>
        Change your roblox description/bio to: <strong class="text-lightred"><?php echo htmlspecialchars($descVerify); ?></strong>
        <br><br><br><br>
        <p class="text-lightred">The verification code will change on every page-refresh.</p>
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

  <script type="text/javascript">
    $(window).on('load',function(){
        $('#view-modal').modal('show');
    });
  </script>

</body>
</html>
