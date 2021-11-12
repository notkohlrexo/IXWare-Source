<?php 
    @session_start();
    $title = 'Licenses';
    include_once 'includes/layout/header.php';
    include_once 'includes/checks.php';
    include_once 'includes/botconfig.php';
    include_once 'loader.php';
    include_once __DIR__.'/../vendor/autoload.php';

    Loader::register('lib','RobThree\\Auth');
    use \RobThree\Auth\TwoFactorAuth;
       
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $activated = pdoQuery($db, "SELECT `expireActivate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
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
    if (isset($_GET["id"])){
      $IDerror = "Please update your discord ID!";
    }
    if (isset($_GET["success"])){
      $IDsuccess = "Successfully updated your discord ID!";
    }
    if (isset($_GET["failed"])){
      $IDerror = "The discord ID is already linked to an other account, contact a IXWare Administrator!";
    }
     

    if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
    }
?>

<?php
   if (isset($_POST['activateLicense'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){
        $license = htmlspecialchars($_POST["license"]);
        $license = hash('sha256', $license);
    
        $getLicense = pdoQuery($db, "SELECT `license` FROM `licenses` WHERE `license`=?", [htmlspecialchars($license)])->fetchColumn();
        $licenseType = pdoQuery($db, "SELECT `type` FROM `licenses` WHERE `license`=?", [htmlspecialchars($license)])->fetchColumn();
        $subEndDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
  
        if (!empty($license)){
    
          if ($getLicense == $license){
            
            if ($licenseType == "Monthly" || $licenseType == "3Months"){
  
              if(!empty($_POST['referrer'])){
  
                $endDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($_POST['referrer'])])->fetchColumn();
                $user = pdoQuery($db, "SELECT `username` FROM `users` WHERE `id`=?", [htmlspecialchars($_POST['referrer'])])->fetchColumn();
                $rank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_POST['referrer'])])->fetchColumn();
                $sub = pdoQuery($db, "SELECT `subscription` FROM `users` WHERE `id`=?", [htmlspecialchars($_POST['referrer'])])->fetchColumn();
  
                if($user != $_SESSION['username']){
  
                  if($rank == "Premium" || $rank == "Admin"){
  
                    if($subscription != "Lifetime"){
  
                      if($licenseType == "Monthly"){
                        $result = date('d-m-Y', strtotime($endDate. ' +3 days'));
                      }elseif($licenseType == "3Months"){
                        $result = date('d-m-Y', strtotime($endDate. ' +6 days'));
                      }
                      $PDOUpdate = $db -> prepare('UPDATE `users` SET `subEndDate` = :add WHERE `id` = :id');
                      $PDOUpdate -> execute(array(':add' => $result, ':id' => htmlspecialchars($_POST['referrer'])));
                      $successfully = $user;
                    }else{
                      $error = "User has lifetime and can't get more rewards.";
                    }
                  }else{
                    $error = "You can't select a User, the User you selected must have an active membership.";
                  }
                }else{
                  $error = "You can't select yourself.";
                }
              }
              if(empty($error)){
                
                $PDODelete = $db -> prepare('DELETE FROM `licenses` WHERE `license` = :license');
                $PDODelete -> execute(array(':license' => $license));
        
                $PDORegister = $db -> prepare('INSERT INTO `used_licenses` VALUES(:id, :username, :license, :type, :expire, :date, :active)');
                $PDORegister -> execute(array(':id' => htmlspecialchars($_SESSION['ID']), ':username' => htmlspecialchars($_SESSION['username']), ':license' => htmlspecialchars($_POST['license']), ':type' => $licenseType, ':expire' => subscriptionEndDate($licenseType), ':date' => date('d-m-Y'), 'active' => '1'));
                
  
                // Apply License to database
                if ($licenseType == "Monthly"){
                  $PDOUpdate = $db -> prepare('UPDATE `users` SET `subscription` = :sub WHERE `id` = :id');
                  $PDOUpdate -> execute(array(':sub' => 'Monthly', ':id' => htmlspecialchars($_SESSION['ID'])));
  
                  if ($subEndDate == '0'){
                    $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
                    $PDOUpdate1 -> execute(array(':sub' => subscriptionEndDate('Monthly'), ':id' => htmlspecialchars($_SESSION['ID'])));
                  }else{
                    // Add a month if user has already an active license
                    $date1 = new DateTime($subEndDate);
                    $date1->modify('+1 month');
                    $date1 = $date1->format('d-m-Y');
  
                    $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
                    $PDOUpdate1 -> execute(array(':sub' => $date1, ':id' => htmlspecialchars($_SESSION['ID'])));
                  }
                }elseif($licenseType == "3Months"){
                  $PDOUpdate = $db -> prepare('UPDATE `users` SET `subscription` = :sub WHERE `id` = :id');
                  $PDOUpdate -> execute(array(':sub' => '3Months', ':id' => htmlspecialchars($_SESSION['ID'])));
  
                  if ($subEndDate == '0'){
                    $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
                    $PDOUpdate1 -> execute(array(':sub' => subscriptionEndDate('3Months'), ':id' => htmlspecialchars($_SESSION['ID'])));
                  }else{
                    // Add a three months if user has already an active license
                    $date1 = new DateTime($subEndDate);
                    $date1->modify('+3 month');
                    $date1 = $date1->format('d-m-Y');
  
                    $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
                    $PDOUpdate1 -> execute(array(':sub' => $date1, ':id' => htmlspecialchars($_SESSION['ID'])));
                  }
                }
                // Apply end
  
                $PDOUpdate = $db -> prepare('UPDATE `users` SET `rank` = :rank WHERE `id` = :id');
                $PDOUpdate -> execute(array(':rank' => 'Premium', ':id' => htmlspecialchars($_SESSION['ID'])));

                if(!empty($successfully)){
                  $referreduser = htmlspecialchars($_POST['referrer']);
                  logUser($_SESSION['ID'], $_SESSION['username'], "Activated a valid license and referred $successfully.");
                  $success = "Successfully activated your license! And extended $successfully's membership.";
                }else{
                  logUser($_SESSION['ID'], $_SESSION['username'], "Activated a valid license.");
                  $success = "Successfully activated your license!";
                }

                try{
                  if($discordID != 0){
                    $discord->guild->addGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$discordID, 'role.id' => $membershipID]);
                    $dm = $discord->user->createDm(['recipient_id' => (int)$discordID]);
                    $discord->channel->createMessage(['content' => 'Successfully verified you, thanks for using IXWare!', 'channel.id' => $dm->id]);
                  }
                }catch(Exception $e){
                  logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN LICENSES. $e");
                }
              }
            }else{
              $error = "Error L-#02 - Contact the Web-Administrator.";
            }
  
          }else{
            $license = htmlspecialchars($_POST["license"]);
            logUser($_SESSION['ID'], $_SESSION['username'], "Tried to activate an invalid license key - $license");
            $error = "Invalid license key";
          }
    
        }else{
          $error = "Fill everything";
        }
      }else{
        $error = "You need to activate your account first!";
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
          <a class="sidenav-link active" href="ix-licenses">
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
            <li class="breadcrumb-item text-white">Profile Settings</li>
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
        <!-- Tabs navs -->
        <ul class="nav nav-tabs nav-fill mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active text-darkwhite" id="ex2-tab-1" data-toggle="tab" href="#ex2-tabs-1" role="tab" aria-controls="ex2-tabs-1" aria-selected="true"><i class="fab fa-keycdn pr-2"></i>Activate License</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-darkwhite" id="ex2-tab-2" data-toggle="tab" href="#ex2-tabs-2" role="tab" aria-controls="ex2-tabs-2" aria-selected="false"><i class="fas fa-chart-network pr-2"></i>Active Licenses</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-darkwhite" id="ex2-tab-3" data-toggle="tab" href="#ex2-tabs-3" role="tab" aria-controls="ex2-tabs-3" aria-selected="false"><i class="fas fa-list pr-2"></i>License History</a>
            </li>
        </ul>
        <!-- Tabs navs -->

        <!-- Tabs content -->
        <div class="tab-content" id="ex2-content">
            <div class="tab-pane fade show active" id="ex2-tabs-1" role="tabpanel" aria-labelledby="ex2-tab-1" >
                <form method="POST" class="md-form md-form-resized">
                    <div class="row">
                        <div class="col-xl-12">
                            <?php
                                if(!empty($error)){
                                    echo '<div class="animated fadeIn ml-3 mr-3">'.error(htmlspecialchars($error)).'</div>';
                                }
                                if(!empty($success)){
                                    echo '<div class="animated fadeIn ml-3 mr-3">'.success(htmlspecialchars($success)).'</div>';
                                }
                            ?>
                            <div class="card new-license-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                    <p class="font-weight-bolder text-left text-white">Activate a License</p>
                                    <!-- Material input -->
                                    <div class="md-form mt-2">
                                        <input type="text" name="license" autocomplete="off" placeholder="XXXXXXXXXXXXXXXX_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX_X-XX-XXXX" class="form-control text-darkwhite" style="background-color: transparent !important;">
                                    </div>
                                    <!-- Material Select -->
                                    <p class="text-darkwhite mt-5">Who referred you? *Optional</p>
                                    <select class="select" name="referrer" data-filter="true">
                                        <option value="" class="text-white" disabled selected>Select a user</option>
                                        <?php
                                            $get = pdoQuery($db, "SELECT * FROM `users`");
                                            $results = $get->fetchAll(PDO::FETCH_ASSOC);

                                            foreach($results as $result){
                                              $id = htmlspecialchars($result['id']);
                                              $username = htmlspecialchars($result['username']);
                                              $rank = htmlspecialchars($result['rank']);
                                              $sub = htmlspecialchars($result['subscription']);

                                              if($rank == "Premium" && $rank != "Admin" && $sub != "Lifetime"){
                                                  echo "<option value='$id'>$username</option>";
                                              }
                                            }
                                        ?>
                                    </select>
                                    <button type="submit" name="activateLicense" value="license" class="btn btn-outline-primary waves-effect center-button settings-button mt-auto" style="width: 100%;">Activate</button>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                    <p class="text-left mt-auto p-margin text-darkwhite">Purchase a license from<a href="purchase"><strong> here</strong></a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="ex2-tabs-2" role="tabpanel" aria-labelledby="ex2-tab-2">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card new-license-card bg-dark table" style="display:table;">
                            <div class="card-body d-flex flex-column">
                                <?php
                                $get = pdoQuery($db, "SELECT * FROM `used_licenses` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])]);

                                $results = $get->fetchAll(PDO::FETCH_ASSOC);
                                echo "<div class='table-responsive'>";
                                    echo "<table id='table-dataTable-lActive' class='table table-hover text-darkwhite'>";
                                        echo "<thead class='text-nowrap'>
                                        <tr>
                                            <th scope='col' class='font-weight-bolder'>Username</th>
                                            <th scope='col' class='font-weight-bolder'>License Key</th>
                                            <th scope='col' class='font-weight-bolder'>Plan Type</th>
                                            <th scope='col' class='font-weight-bolder'>Activation Date</th>
                                            <th scope='col' class='font-weight-bolder'>Expire Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>";
                                        foreach($results as $result){
                                            $tUser = htmlspecialchars($result['username']);
                                            $tLicense = htmlspecialchars($result['license']);
                                            $tType = htmlspecialchars($result['type']);
                                            $tExpire = htmlspecialchars($result['expire']);
                                            $tDate = htmlspecialchars($result['date']);
                                            $tActive = htmlspecialchars($result['active']);

                                            if ($tType == "3Months"){
                                            $tType = "3 Months";
                                            }
                                            if ($tActive == '1'){
                                            echo "<tr>
                                                <td scope=\"row\" style='word-break:break-all;'>$tUser</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$tLicense</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$tType</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$tDate</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$tExpire</td>
                                            </tr>
                                            </tbody>";
                                            }
                                        }
                                    echo "</table>";
                                ?>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="ex2-tabs-3" role="tabpanel" aria-labelledby="ex2-tab-3">
                <div class="row">
                    <div class="col-xl-12">
                      <div class="card new-license-card bg-dark table" style="display: table;">
                          <div class="card-body d-flex flex-column">
                            <?php
                            $get = pdoQuery($db, "SELECT * FROM `used_licenses` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])]);

                            $results = $get->fetchAll(PDO::FETCH_ASSOC);
                            echo "<div class='table-responsive'>";
                                echo "<table id='table-dataTable-lHistory' class='table table-hover text-darkwhite'>";
                                    echo "<thead class='text-nowrap'>
                                    <tr>
                                        <th scope='col' class='font-weight-bolder'>Username</th>
                                        <th scope='col' class='font-weight-bolder'>License Key</th>
                                        <th scope='col' class='font-weight-bolder'>Plan Type</th>
                                        <th scope='col' class='font-weight-bolder'>Activation Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>";
                                    foreach($results as $result){
                                        $tUser = htmlspecialchars($result['username']);
                                        $tLicense = htmlspecialchars($result['license']);
                                        $tType = htmlspecialchars($result['type']);
                                        $tDate = htmlspecialchars($result['date']);
                                        $tActive = htmlspecialchars($result['active']);

                                        if ($tActive == '0'){
                                        echo "<tr>
                                            <td scope=\"row\" style='word-break:break-all;'>$tUser</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$tLicense</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$tType</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$tDate</td>
                                        </tr>
                                        </tbody>";
                                        }
                                    }
                                echo "</table>";
                            ?>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabs content -->        
    </div>
  </main>
  <!--Main layout-->


  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>