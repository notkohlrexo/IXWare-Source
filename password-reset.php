<?php 
    $title = 'Password Reset';
    include_once 'includes/layout/header.php';
    include_once "includes/functions.php";
    include_once "includes/email.php";
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

            if (isset($_POST['email'])){

                if (\Volnix\CSRF\CSRF::validate($_POST)) {
                    $email = htmlspecialchars($_POST["email"]);

                    if (checkEmail($email) == 1){
                        
                        $ve = new hbattat\VerifyEmail($email, 'no-reply@ixwhere.online');
                        if($ve->verify() == true){
                            $username = htmlspecialchars(pdoQuery($db, "SELECT `username` FROM `users` WHERE `email`=?", [htmlspecialchars($email)])->fetchColumn());

                            $hash = hash('sha256', getID(50));
                            $PDORegister = $db -> prepare('INSERT INTO `pw_reset` VALUES(:username, :email, :token, :timestamp, :date)');
                            $PDORegister -> execute(array(':username' => $username, ':email' => $email, ':token' => $hash, ':timestamp' => time(), ':date' => date("Y-m-d H:i:s")));
                            logUser("Guest", $username, "Requested a new password.");
                            sendEmail($email, "IXWare - Password reset", "Hey $username, Please click <a href='https://ixwhere.online/newPassword?token=$hash'>here</a> to reset your IXWare password, the token will expire in 24 hours!"); 
                            $success = "Please follow the instructions which has been sent to your E-Mail Address. It could take up to 5 minutes for the E-Mail to arrive."; 
                        }else{
                            $error = "E-Mail Address isn't valid.";
                        }
                    }else{
                        $error = "E-Mail Address not found in our database.";
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
                                <p class="text-center text-white">Reset your IXWare password</p>
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
                                            <input type="email" class="form-control text-darkwhite mb-3" autocomplete="off" name="email" placeholder="E-Mail Address" value="<?php if (isset($_POST['email'])){ echo htmlspecialchars($_POST['email']); }else{ echo ''; } ?>" required />
                                            <div class="g-recaptcha mt-2" data-sitekey="6LdqlroZAAAAAAC0xghvzhAO2giUnX42otLmOetF" required></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                    <div class="text-center mb-5">
                                        <button name="ixReset" value="reset" type="submit" class="btn btn-outline-purple btn-sm text-darkwhite w-75 btn-login"><i class="fas fa-unlock-alt fa-lg mr-2"></i>Reset</button>
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


  <!-- SCRIPTS -->

  <?php require_once 'includes/layout/footer.php'; ?>
  <!-- Particles -->
  <script src="resources/js/particles.js"></script>
  <script src="resources/js/app.js"></script>

</body>
</html>
