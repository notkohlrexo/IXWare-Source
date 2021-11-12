<?php 
    $title = 'Webhook';
    include_once 'includes/layout/header.php';
    include_once "includes/functions.php";
    include_once "includes/botconfig.php";
    include_once __DIR__.'/../vendor/autoload.php';

    $get = pdoQuery($db, "SELECT * FROM `spammed_hooks`");
    $row = $get->rowCount();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $secretKey = ' x';
        $captcha = $_POST['g-recaptcha-response'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
        $responseKeys = json_decode($response,true);

        if(intval($responseKeys["success"]) !== 1) {
            $error = 'You need to check the captcha to proceed!';
        } else {
            if(isset($_POST['spam'])){

                if(!empty($_POST['wLink'])){

                    $link = htmlspecialchars($_POST['wLink']);

                    if(isset($_POST['wRemoveOnly'])){

                        $curl = curl_init($link);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_exec($curl);
                        curl_close($curl);
    
                        $success = "Successfully removed the webhook.";
                    }else{

                        if(!empty($_POST['wMsg'])){
                            if(!empty($_POST['wAuthor'])){
                                if(!empty($_POST['wTimes'])){
            
                                    $msg = htmlspecialchars($_POST['wMsg']);
                                    $author = htmlspecialchars($_POST['wAuthor']);
                                    $times = htmlspecialchars($_POST['wTimes']);
                    
                                    $returned_content = get_data($link);
                                    if(strpos($returned_content, "Unknown")){
                                        $error = $returned_content;
                                    }else{
                                        if(strpos($returned_content, '"channel_id":')){
    
                                            if(is_numeric($times)){
            
                                                for ($i = 0; $i < $times; $i++) {
                                                    $hookObject = json_encode([
                                                        "embeds" => [
                                                            [
                                                                "type" => "rich",
                                                                "timestamp" => date("c"),
                                                                "color" => hexdec("#a30a0a"),
                                                                "thumbnail" => [
                                                                    "url" => "https://ixwhere.online/resources/img/logo.png"
                                                                ],
                                                                "author" => [
                                                                    "name" => "ixwhere.online/WEBHOOK",
                                                                    "url" => "https://www.ixwhere.online/webhook"
                                                                ],
                                                                "footer" => [
                                                                    "text" => "Webhook fucked by $author",
                                                                    "icon_url" => "https://ixwhere.online/resources/img/logo.png"
                                                                ],
                                                                "fields" => [
                                                                    [
                                                                        "name" => "Information",
                                                                        "value" => $msg
                                                                    ],
                                                                ]
                                                            ]
                                                        ]
                                                    
                                                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                                
                                                    $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                                                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                                                    curl_setopt_array($ch, [
                                                        CURLOPT_URL => $link,
                                                        CURLOPT_POST => true,
                                                        CURLOPT_POSTFIELDS => $hookObject,
                                                        CURLOPT_HTTPHEADER => [
                                                            "Content-Type: application/json"
                                                        ]
                                                    ]);
                                                    
                                                    curl_exec($ch);
                                                    curl_close($ch);
                                                }
                                                if(isset($_POST["wRemove"])){
                                                    $curl = curl_init($link);
                                                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
                                                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                                                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                                                    curl_exec($curl);
                                                    curl_close($curl);
                
                                                    $success = "Successfully spammed and removed the webhook.";
                                                }else{
                                                    $success = "Successfully spammed the webhook.";
                                                }
                                                $PDOLog = $db -> prepare('INSERT INTO `spammed_hooks` VALUES(:wh, :msg, :author, :times, :date)');
                                                $PDOLog -> execute(array(':wh' => $link, ':msg' => $msg, ':author' => $author, ':times' => $times, ':date' => date("Y-m-d H:i:s")));
                                            }else{
                                                $error = "Given times ain't numeric";
                                            }
                                        }else{
                                            $error = "Webhook invalid.";
                                        }
                                    }
                                }else{
                                    $error = "Enter a number";
                                }
                            }else{
                                $error = "Enter an author";
                            }
                        }else{
                            $error = "Enter a message";
                        }
                    }
                }else{
                    $error = "Enter a webhook link";
                }
            }
            if(isset($_POST['test'])){
                if(!empty($_POST['wLink'])){
                    $url = htmlspecialchars($_POST['wLink']);
        
                    $returned_content = get_data($url);
                    if(strpos($returned_content, "Unknown")){

                        $error = $returned_content;
                    }elseif(strpos($returned_content, '"channel_id":')){

                        $success = $returned_content;
                    }
                }else{
                    echo "Enter a webhook link";
                }
            }
        }
    }

    function get_data($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
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
                                    <a href="index" class="hvr-icon-back float-left"><i class="fad fa-arrow-left fa-lg text-white hvr-icon d-none d-sm-block"></i></a>
                                </div>
                                <h4 class="text-center text-white mt-5">IXWare</h4>
                                <p class="text-center text-white">Spam a webhook</p>
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
                                            <input type="text" class="form-control text-darkwhite mb-3" autocomplete="off" name="wLink" placeholder="Webhook Link" value="<?php if (isset($_POST['wLink'])){ echo htmlspecialchars($_POST['wLink']); }else{ echo ''; } ?>" required />
                                            <input type="text" class="form-control text-darkwhite mb-3" autocomplete="off" name="wMsg" placeholder="Message" value="<?php if (isset($_POST['wMsg'])){ echo htmlspecialchars($_POST['wMsg']); }else{ echo ''; } ?>" />
                                            <input type="text" class="form-control text-darkwhite mb-3" autocomplete="off" name="wAuthor" placeholder="Author" value="<?php if (isset($_POST['wAuthor'])){ echo htmlspecialchars($_POST['wAuthor']); }else{ echo ''; } ?>" />
                                            <input type="text" class="form-control text-darkwhite mb-3" autocomplete="off" name="wTimes" placeholder="Amount of times to spam" value="<?php if (isset($_POST['wTimes'])){ echo htmlspecialchars($_POST['wTimes']); }else{ echo ''; } ?>" />
                                            <!-- Default checkbox -->
                                            <div class="form-check ml-2">
                                              <input class="form-check-input" type="checkbox" value="yeet" id="wRemove" name="wRemove" />
                                              <label class="form-check-label mt-1 text-white" for="wRemove">
                                                Remove Webhook
                                              </label>
                                            </div>
                                            <!-- Default checkbox -->
                                            <div class="form-check ml-2">
                                              <input class="form-check-input" type="checkbox" value="yeet" id="wRemoveOnly" name="wRemoveOnly" />
                                              <label class="form-check-label mt-1 text-white" for="wRemoveOnly">
                                                Remove Webhook Only
                                              </label>
                                            </div>
                                            <div class="g-recaptcha mt-2" data-sitekey="6LdqlroZAAAAAAC0xghvzhAO2giUnX42otLmOetF" required></div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button name="spam" type="submit" type="submit" class="btn btn-outline-light btn-sm text-darkwhite w-75 btn-login"><i class="fas fa-bomb fa-lg mr-2"></i>Submit</button>
                                        <button name="test" type="submit" type="submit" class="btn btn-outline-light btn-sm text-darkwhite w-75 btn-login mt-2"><i class="fas fa-check-square fa-lg mr-2"></i>Test Webhook</button>
                                    </div>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                    <p class="text-center text-darkwhite mt-2" style="margin-top: 0px;">
                                        <b>Webhooks Spammed: </b><strong> <?= htmlspecialchars($row); ?></a>
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
