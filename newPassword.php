<?php 
    $title = 'Password Reset';
    include_once 'includes/layout/header.php';
    include_once "includes/functions.php";
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
    if (isset($_GET["token"]) && !empty($_GET["token"])){
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $PDOCheckToken = $db -> prepare("SELECT COUNT(*) FROM `pw_reset` WHERE `token` = :token");
        $PDOCheckToken -> execute(array(':token' => htmlspecialchars($_GET["token"])));
        $countToken = $PDOCheckToken -> fetchColumn(0);
        
        if($countToken != 1){
            header("HTTP/1.1 401 Unauthorized");
            echo file_get_contents('includes/layout/error/401.php');
            die();
        }
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

            if (isset($_POST['newPassword'])){

                if(isset($_POST['newPasswordRepeat'])){

                    if (\Volnix\CSRF\CSRF::validate($_POST)) {

                        $password = htmlspecialchars($_POST["newPassword"]);
                        $passwordRepeat = htmlspecialchars($_POST["newPasswordRepeat"]);

                        if($password == $passwordRepeat){

                            if(strlen($password) > 8){

                                if(preg_match('/[A-Z]/', $password)){
                                    
                                    if(preg_match('/\d/', $password)){

                                        if (isset($_GET["token"]) && !empty($_GET["token"])){

                                            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                                            $PDOCheckToken = $db -> prepare("SELECT COUNT(*) FROM `pw_reset` WHERE `token` = :token");
                                            $PDOCheckToken -> execute(array(':token' => htmlspecialchars($_GET["token"])));
                                            $countToken = $PDOCheckToken -> fetchColumn(0);
                                            
                                            if($countToken == 1){
                                                $timestamp = pdoQuery($db, "SELECT `timestamp` FROM `pw_reset` WHERE `token`=?", [htmlspecialchars($_GET["token"])])->fetchColumn();
                                                $username = pdoQuery($db, "SELECT `username` FROM `pw_reset` WHERE `token`=?", [htmlspecialchars($_GET["token"])])->fetchColumn();
                                                $google = pdoQuery($db, "SELECT `googleAPI` FROM `users` WHERE `username`=?", [htmlspecialchars($username)])->fetchColumn();
            
                                                if($google == 0){
            
                                                    if ($timestamp <= strtotime('-24 hours')) {
                                                        $PDODelete = $db -> prepare('DELETE FROM `pw_reset` WHERE `token` = :token');
                                                        $PDODelete -> execute(array(':token' => htmlspecialchars($_GET["token"])));  
                                                        $error = "The Password Reset Token expired. Please request a new password reset.";
                                                    }
                    
                                                    if(empty($error)){
                                                        if(checkUsername($username) == 1){
                                                            $options = array(
                                                                'cost' => 12,
                                                            );
                                                            $hashPassword = password_hash($password, PASSWORD_BCRYPT, $options)."\n";
                                                            $PDOS = $db -> prepare('UPDATE `users` SET `pass` = :password WHERE `username` = :username');
                                                            $PDOS -> execute(array(':password' => $hashPassword, ':username' => htmlspecialchars($username)));
                    
                                                            $PDODelete = $db -> prepare('DELETE FROM `pw_reset` WHERE `token` = :token');
                                                            $PDODelete -> execute(array(':token' => htmlspecialchars($_GET["token"])));  
                                                            logUser("Guest", $username, "Changes his password.");
                                                            header("location: login?pwReset");
                                                            die();
                                                        }else{
                                                            $error = "An error occured.";
                                                        }
                                                    }
                                                }else{
                                                    $error = "You registered your account via Google, you can't reset your password.";
                                                    $PDODelete = $db -> prepare('DELETE FROM `pw_reset` WHERE `token` = :token');
                                                    $PDODelete -> execute(array(':token' => htmlspecialchars($_GET["token"])));  
                                                }
                                            }else{
                                                header("HTTP/1.1 401 Unauthorized");
                                                echo file_get_contents('includes/layout/error/401.php');
                                                die();
                                            }
                                        }else{
                                            $error = "Authorization Token missing.";
                                        }
                                    }else{
                                        $error = "Please add atleast one digit/number to your new password.";
                                    }
                                }else{
                                    $error = "Please add one uppercase letter to your new password.";
                                }
                            }else{
                                $error = "Please make sure that your password is >8 characters long.";
                            }
                        }else{
                            $error = "Passwords doesn't match.";
                        }
                    } else {
                        header("HTTP/1.1 401 Unauthorized");
                        echo file_get_contents('includes/layout/error/401.php');
                        die();
                    }
                }else{
                    $error = "Repeat your new password.";
                }
            }else{
                $error = "Type in your new password.";
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
                                            <input type="password" class="form-control text-darkwhite mb-3" autocomplete="off" name="newPassword" placeholder="Password" value="<?php if (isset($_POST['newPassword'])){ echo htmlspecialchars($_POST['newPassword']); }else{ echo ''; } ?>" required />
                                            <input type="password" class="form-control text-darkwhite mb-3" autocomplete="off" name="newPasswordRepeat" placeholder="Repeat Password" value="<?php if (isset($_POST['newPasswordRepeat'])){ echo htmlspecialchars($_POST['newPasswordRepeat']); }else{ echo ''; } ?>" required />
                                            <div class="g-recaptcha mt-2" data-sitekey="6LdqlroZAAAAAAC0xghvzhAO2giUnX42otLmOetF" required></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                    <div class="text-center mb-5">
                                        <button name="ixReset" value="reset" type="submit" class="btn btn-outline-purple btn-sm text-darkwhite w-75 btn-login"><i class="fas fa-badge-check fa-lg mr-2"></i>Confirm Change</button>
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
