<?php 
      error_reporting(0);
      @session_start();
      $title = 'Registration';
      include_once 'includes/layout/header.php';
      include_once 'includes/functions.php';
      include_once 'includes/email.php';
      include_once __DIR__.'/../vendor/autoload.php';

      $registration = pdoQuery($db, "SELECT `registration` FROM `global`")->fetchColumn();

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
        
        if (isset($_POST['ixRegister'])){

            if (\Volnix\CSRF\CSRF::validate($_POST)) {

                if($registration == '1'){

                    $user = htmlspecialchars($_POST["username"]);
                    $pass = htmlspecialchars($_POST["password"]);
                    $pass2 = htmlspecialchars($_POST["passwordRepeat"]);
                    $email = htmlspecialchars($_POST["email"]);
                    $id = getID(50);                           
                    
                    if(!empty($_POST['ToSCheck'])){

                        if (!empty($user) || !empty($pass) || !empty($pass2) || !empty($email) || !empty($error)){
        
                            if(ctype_alnum($user)){
    
                                if(strlen($user) > 4 && strlen($user) < 15){
    
                                    if(checkUsername($user) == 0){
    
                                        if(checkID($id) == 0){
    
                                            if($pass == $pass2){
    
                                                if (filter_var($email, FILTER_VALIDATE_EMAIL)){
    
                                                    if (checkEmail($email) == 0){
    
                                                        $allowed_mails = array('@gmail', '@hotmail', '@outlook', '@googlemail', '@protonmail', '@yandex', '@ixware', '@icloud', '@yahoo');
                                                        if (preg_match('('.implode('|',$allowed_mails).')', $email)){
    
                                                            $ve = new hbattat\VerifyEmail($email, 'no-reply@ixwhere.online');
                                                            if($ve->verify() == true){
                                                                
                                                                if (checkIP(hash('sha256', realIP())) == 0){
    
                                                                    $options = array(
                                                                        'cost' => 12,
                                                                    );
                                                                    $hashPassword = password_hash($pass, PASSWORD_BCRYPT, $options)."\n";
                                                                    $country = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".realIP())) -> {'geoplugin_countryName'};
                                                                    $hash = getID(50);
                    
                                                                    //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                                    $PDORegister = $db -> prepare('INSERT INTO `users` VALUES(:ID, :user, :password, :email, :country, :ip, :rank, 0, 0, 0, :subscription, :date, 0, 0, :notification, :timestamp, :hash, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)');
                                                                    $PDORegister -> execute(array(':ID' => $id, ':user' => $user, ':password' => $hashPassword, ':email' => $email, ':country' => htmlspecialchars($country), ':ip' => htmlspecialchars(hash('sha256', realIP())), ':rank' => 'User', ':subscription' => 'None', ':date' => htmlspecialchars(date('d-m-Y')), ':notification' => 'resources/audio/notification.ogg', ':timestamp' => time(), 'hash' => $hash));
                                                                
                                                                    sendEmail($email, "Please verify your email address to get full access", "Please click <a href='https://ixwhere.online/verify?token=$hash'>here</a> to activate your IXWare account!<br>Your account will automatically get terminated if you don't verify your account in the next 24 hours!");            
                                                                    logUser($id, $user, "Registered");
                                                                    $success = 'Successfully registered! Redirecting...';
                                                                    header('refresh:2; url=login?success');
                                                                }else{
                                                                    $error = "You already have an account!";
                                                                }
                                                            }else{
                                                                $error = "E-Mail isn't valid!";
                                                            } 
                                                        }else{
                                                            $error = "We currently only allow: '@gmail', '@googlemail', '@hotmail', '@outlook', '@protonmail', '@yahoo' and '@yandex'. If you want to get your mail provider added then contact us!";
                                                        }
                                                    }else{
                                                        $error = "E-Mail is already taken!";
                                                    }
                                                }else{
                                                    $error = "E-Mail isn't validsdasd! $email";
                                                }
                                            }else{
                                                $error = "Passwords doesn't match!";
                                            }
                                        }else{
                                            $error = 'Registration failed, please try again!';
                                        }
                                    }else{
                                        $error = "Username is already taken.";
                                    }
                                }else{
                                    $error = "Username must be 4-15 characters.";
                                }
                            }else{
                                $error = "Username must be alphanumeric.";
                            }
                        }else{
                            $error = 'Please enter all fields!';
                        }
                    }else{
                        $error = 'You need to agree to the Terms of Service!';
                    }
                }else{
                    $error = "Registrations are currently disabled.";
                }
            }else{
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
                                    <a href="" class="hvr-icon-grow float-right ml-2" title="Sign In with Google"><i class="fab fa-google-plus-g text-google fa-lg text-white hvr-icon d-none d-sm-block"></i></a>
                                    <a href="?action=login" class="hvr-icon-grow float-right" title="Sign In with Discord"><i class="fab fa-discord text-discordblue fa-lg text-white hvr-icon d-none d-sm-block"></i></a>
                                    <a href="index" class="hvr-icon-back float-left"><i class="fad fa-arrow-left fa-lg text-white hvr-icon d-none d-sm-block"></i></a>
                                </div>
                                <h4 class="text-center text-white mt-5">IXWare</h4>
                                <p class="text-center text-white">Sign up for a IXWare account</p>
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
                                            <input type="text" class="form-control text-darkwhite mb-3" autocomplete="off" name="username" placeholder="UserName" value="<?php if (isset($_POST['username'])){ echo htmlspecialchars($_POST['username']); }else{ echo ''; } ?>" required>
                                            <input type="password" class="form-control text-darkwhite mb-3" autocomplete="off" name="password" placeholder="Password" value="<?php if (isset($_POST['password'])){ echo htmlspecialchars($_POST['password']); }else{ echo ''; } ?>" required>
                                            <input type="password" class="form-control text-darkwhite mb-3" autocomplete="off" name="passwordRepeat" placeholder="Password Repeat" value="<?php if (isset($_POST['passwordRepeat'])){ echo htmlspecialchars($_POST['passwordRepeat']); }else{ echo ''; } ?>" required>
                                            <input type="email" class="form-control text-darkwhite mb-3" autocomplete="off" name="email" placeholder="E-Mail Address" value="<?php if (isset($_POST['email'])){ echo htmlspecialchars($_POST['email']); }else{ echo ''; } ?>" required>
                                            <div class="g-recaptcha mb-3" data-sitekey="6LdqlroZAAAAAAC0xghvzhAO2giUnX42otLmOetF" required></div>
                                            <!-- Default checkbox -->
                                            <div class="form-check ml-2">
                                              <input class="form-check-input" type="checkbox" value="tos" name="ToSCheck" id="Terms-of-Service" />
                                              <label class="form-check-label mt-1 text-white" for="Terms-of-Service">
                                                I agree to the <a href="terms-of-service" target="_blank" class="text-primary"> Terms of Service</a>
                                              </label>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                    <div class="text-center">
                                        <button name="ixRegister" value="register" type="submit" class="btn btn-outline-light btn-sm text-darkwhite w-75 btn-login"><i class="fad fa-sign-in-alt fa-lg mr-2"></i>Sign Up</button>
                                    </div>
                                    <p class="text-center text-darkwhite mt-3" style="margin-bottom: 0rem !important;">
                                        <b>You are already registered?</b><a href="login" class="text-primary m-l-5"> Sign In</a>
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
