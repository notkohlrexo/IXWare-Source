<?php 
    @session_start();
    $title = 'Support';
    include_once 'includes/layout/header.php';
    include_once 'includes/checks.php';
    include_once 'includes/botconfig.php';
    include_once 'loader.php';
    include_once __DIR__.'/../vendor/autoload.php';

    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $activated = pdoQuery($db, "SELECT `expireActivate` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $checkBan = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

    if(checkMaintenance() == 'Maintenance' && $getRank != "Admin"){
      header('Location: dashboard');
      die();
    }

    if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
    }

    if($checkBan != 0){
      $date = new DateTime($checkBan);
      $endDate = htmlspecialchars($date->format("D M d, Y G:i"));

      $currentDiscord = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      if($currentDiscord != 0){
  
        try{
          $discord->guild->removeGuildMemberRole(['guild.id' => $guildid, 'user.id' => (int)$currentDiscord, 'role.id' => $regularID]);   

          $PDODelete = $db -> prepare('DELETE FROM `discord_verified` WHERE `id` = :id');
          $PDODelete -> execute(array(':id' => $currentDiscord));
        }catch(Exception $e){}
      }

      $banReason = htmlspecialchars(pdoQuery($db, "SELECT `banReason` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn());
      echo "<div class='modal fade' id='showPW' tabdashboard='-1' role='dialog' aria-labelledby='myModalLabel'
      aria-hidden='true'>
      <!-- Change class .modal-sm to change the size of the modal -->
      <div class='modal-dialog modal-lg' role='document'>
        <div class='modal-content'>
          <div class='modal-header bg-dark text-darkwhite'>
            <h5 class='modal-title w-100' id='myModalLabel'>Banned Until: $endDate</h5>
            <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close' id='modalButton'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>
          <div class='modal-body bg-dark text-darkwhite'>
            Ban Reason: <strong class='text-lightred'>$banReason</strong>
            <br><br><br><br>
            <p class='text-lightred'>Contact the support to appeal for your ban</p>
          </div>
        </div>
      </div>
    </div>";
    }
?>



<?php
if (isset($_POST['createTicket'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

      if ($activated == 0){

        if (!empty($_POST['title'])){

            if (!empty($_POST['ticketContent'])){

                if(strlen($_POST['ticketContent']) < 1001){

                    if (!empty($_POST['priority'])){

                        if (!empty($_POST['topic'])){
    
                            $priority = htmlspecialchars($_POST['priority']);
                            $topic = htmlspecialchars($_POST['topic']);
    
                            if($priority == "High" || $priority == "Mid" || $priority == "Low"){
    
                                if($topic == "Account" || $topic == "Ban" || $topic == "Technical" || $topic == "Other"){
    
                                    $id = getID(50);
                                    if(checkTicket($id) == 0){

                                        $PDOLog = $db -> prepare('INSERT INTO `support_tickets` VALUES(:id, :ticketID, :title, :description, :priority, :topic, :closed, :lastAnswer, :lastUpdate, :date)');
                                        $PDOLog -> execute(array(':id' => htmlspecialchars($_SESSION['ID']), ':ticketID' =>  $id, ':title' => htmlspecialchars($_POST['title']), ':description' => htmlspecialchars($_POST['ticketContent']), ':priority' => $priority, ':topic' => $topic, ':closed' => '0', ':lastAnswer' => htmlspecialchars($_SESSION['username']), ':lastUpdate' => date("Y-m-d H:i:s"), ':date' => date("Y-m-d H:i:s")));
    
                                        try{
                                            $discord->channel->createMessage(
                                                [
                                                    'channel.id' => $ticketChannel,
                                                    'embed'      => [
                                                        'timestamp' => date("c"),
                                                        "color" => hexdec("#a30a0a"),
                                                        'title' => 'New Ticket created '.date('Y-m-d H:i:s'),
                                                        "thumbnail" => [
                                                            "url" => ""
                                                        ],
                                                        "image" => [
                                                            "url" => "https://ixwhere.online/resources/img/IXWARE.gif"
                                                        ],
                                                        "author" => [
                                                            "name" => "Click to visit ticket",
                                                            "url" => "https://www.ixwhere.online/viewticket?ID=$id"
                                                        ],
                                                        "footer" => [
                                                            "text" => "Powered by ixwhere.online",
                                                            "icon_url" => ""
                                                        ],
                                                        "fields" => [
                                                            [
                                                                "name" => "Username",
                                                                "value" => htmlspecialchars($_SESSION['username'])
                                                            ],
                                                            [
                                                                "name" => "Ticket Title",
                                                                "value" => htmlspecialchars($_POST['title'])
                                                            ],
                                                            [
                                                                "name" => "Description",
                                                                "value" => htmlspecialchars($_POST['ticketContent'])
                                                            ],
                                                            [
                                                                "name" => "Priority",
                                                                "value" => $priority
                                                            ],
                                                            [
                                                                "name" => "Topic",
                                                                "value" => $topic
                                                            ]
                                                        ]
                                                    ],
                                                ]
                                            );
                                        }catch(Exception $e){
                                            logUser("DISCORD API", "DISCORD API", "ISSUE DETECTED IN LOGIN. $e");
                                        }

                                        $success = "Successfully created a ticket, it could take up to 24 hours to receive an answer, check your e-mail for notifications.";
                                    }else{
                                        $error = "An error occured, please try again.";
                                    }
                                }else{
                                    header("HTTP/1.1 401 Unauthorized");
                                    echo file_get_contents('includes/layout/error/401.php');
                                    die();
                                }
                            }else{
                                header("HTTP/1.1 401 Unauthorized");
                                echo file_get_contents('includes/layout/error/401.php');
                                die();
                            }
                        }else{
                            $error = "You need to select a ticket topic.";
                        }
                    }else{
                        $error = "You need to select a ticket priority.";
                    }
                }else{
                    $error = "The character limit for the ticket description is 1000.";
                }
            }else{
                $error = "You need to tell us your problem.";
            }
        }else{
            $error = "You need to provide a title.";
        }
      }else{
        $error = "You need to activate your account.";
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
          <a class="sidenav-link active" href="support">
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
                <a class="nav-link active text-darkwhite" id="ex2-tab-1" data-toggle="tab" href="#ex2-tabs-1" role="tab" aria-controls="ex2-tabs-1" aria-selected="true"><i class="fas fa-user-headset pr-2"></i>Create a ticket</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-darkwhite" id="ex2-tab-2" data-toggle="tab" href="#ex2-tabs-2" role="tab" aria-controls="ex2-tabs-2" aria-selected="false"><i class="fas fa-ticket-alt pr-2"></i>My Tickets</a>
            </li>
            <?php
                if($getRank == "Admin"){
                    echo "<li class='nav-item' role='presentation'>
                    <a class='nav-link text-darkwhite' id='ex2-tab-3' data-toggle='tab' href='#ex2-tabs-3' role='tab' aria-controls='ex2-tabs-3' aria-selected='false'><i class='fas fa-clipboard-list pr-2'></i>All Tickets</a>
                    </li>";
                }
            ?>
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

                            <div class="card new-ticket-card bg-dark">
                                <div class="card-body d-flex flex-column">
                                    <!-- Material input -->
                                    <div class="md-form mt-3">
                                        <input type="text" id="title" placeholder="Ticket Title" name="title" class="form-control text-darkwhite" style="background-color: transparent !important;">
                                    </div>
                                    <div class="form-outline mt-3">
                                        <textarea class="form-control text-darkwhite" id="ticketContent" name="ticketContent" style="margin-top: 0px; margin-bottom: 0px; height: 94px;" rows="10"></textarea>
                                        <label class="form-label" for="ticketContent">Tell us your problem</label>
                                     </div>
                                    <!-- Material Select -->
                                    <p class="text-darkwhite mt-3">Priority</p>
                                    <select class="select" name="priority">
                                        <option value="High" class="text-white">High</option>
                                        <option value="Mid" class="text-white">Middle</option>
                                        <option value="Low" class="text-white" selected>Low</option>
                                    </select>
                                    <p class="text-darkwhite mt-3">Ticket Topic</p>
                                    <select class="select mb-5" name="topic">
                                        <option value="Account" class="text-white" selected>Account Issues</option>
                                        <option value="Ban" class="text-white">Ban Appeal</option>
                                        <option value="Technical" class="text-white">Technical Issues</option>
                                        <option value="Other" class="text-white">Other...</option>
                                    </select>
                                    <button type="submit" name="createTicket" value="ticket" class="btn btn-outline-primary waves-effect center-button settings-button mt-auto" style="width: 100%;">Create</button>
                                    <input type="hidden" name="<?= \Volnix\CSRF\CSRF::TOKEN_NAME ?>" value="<?= \Volnix\CSRF\CSRF::getToken() ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="ex2-tabs-2" role="tabpanel" aria-labelledby="ex2-tab-2">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card tickets-table bg-dark table" style="display:table;">
                            <div class="card-body d-flex flex-column">
                                <?php
                                $get = pdoQuery($db, "SELECT * FROM `support_tickets` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])]);

                                $results = $get->fetchAll(PDO::FETCH_ASSOC);
                                echo "<div class='table-responsive'>";
                                    echo "<table class='table table-hover text-darkwhite'>";
                                        echo "<thead class='text-nowrap'>
                                        <tr>
                                            <th scope='col' class='font-weight-bolder'>Title</th>
                                            <th scope='col' class='font-weight-bolder'>Priority</th>
                                            <th scope='col' class='font-weight-bolder'>Section</th>
                                            <th scope='col' class='font-weight-bolder'>Last Updated</th>
                                            <th scope='col' class='font-weight-bolder'>Last Answer</th>
                                            <th scope='col' class='font-weight-bolder'>Date</th>
                                            <th scope='col' class='font-weight-bolder'>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>";
                                        foreach($results as $result){
                                            $title = htmlspecialchars($result['title']);
                                            $priority = htmlspecialchars($result['priority']);
                                            $section = htmlspecialchars($result['topic']);
                                            $lastUpdate = htmlspecialchars($result['lastUpdate']);
                                            $lastAnswer = htmlspecialchars($result['lastAnswer']);
                                            $date = htmlspecialchars($result['date']);
                                            $ticketID = htmlspecialchars($result['ticketID']);

                                            echo "<tr>
                                                <td scope=\"row\" style='word-break:break-all;'>$title</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$priority</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$section</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$lastUpdate</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$lastAnswer</td>
                                                <td scope=\"row\" style='word-break:break-all;'>$date</td>
                                                <form method='POST'>
                                                <td scope=\"row\" style='word-break:break-all;'>
                                                  <div class='btn-group-vertical' role='group' aria-label='Vertical button group'>
                                                    <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' onclick=\"window.open('viewticket?ID=$ticketID')\" data-ripple-color='dark'><i class='fas fa-external-link mr-2'></i>View Ticket</button>
                                                  </div>
                                                </td>
                                                </form>
                                            </tr>
                                            </tbody>";
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
                      <div class="card tickets-table bg-dark table" style="display: table;">
                          <div class="card-body d-flex flex-column">
                            <p class="text-darkwhite font-weight-bolder text-muted">Current tickets</p>
                            <?php
                            if($getRank == "Admin"){
                                
                                $get = pdoQuery($db, "SELECT * FROM `support_tickets` WHERE `closed`=?", [htmlspecialchars('0')]);
                                $results = $get->fetchAll(PDO::FETCH_ASSOC);
    
                                echo "<div class='table-responsive'>";
                                    echo "<table id='table-dataTable-allTickets' class='table table-hover text-darkwhite'>";
                                        echo "<thead class='text-nowrap'>
                                        <tr>
                                            <th scope='col' class='font-weight-bolder'>Ticket Owner</th>
                                            <th scope='col' class='font-weight-bolder'>Title</th>
                                            <th scope='col' class='font-weight-bolder'>Priority</th>
                                            <th scope='col' class='font-weight-bolder'>Section</th>
                                            <th scope='col' class='font-weight-bolder'>Last Updated</th>
                                            <th scope='col' class='font-weight-bolder'>Last Answer</th>
                                            <th scope='col' class='font-weight-bolder'>Date</th>
                                            <th scope='col' class='font-weight-bolder'>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>";
                                        foreach($results as $result){
                                            $ticketownerid = htmlspecialchars($result['id']);
                                            $title = htmlspecialchars($result['title']);
                                            $priority = htmlspecialchars($result['priority']);
                                            $section = htmlspecialchars($result['topic']);
                                            $lastUpdate = htmlspecialchars($result['lastUpdate']);
                                            $lastAnswer = htmlspecialchars($result['lastAnswer']);
                                            $date = htmlspecialchars($result['date']);
                                            $ticketID = htmlspecialchars($result['ticketID']);
    
                                            $ticketOwner = htmlspecialchars(pdoQuery($db, "SELECT `username` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketownerid)])->fetchColumn());
                                            echo "<tr>
                                            <td scope=\"row\" style='word-break:break-all;'>$ticketOwner</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$title</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$priority</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$section</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$lastUpdate</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$lastAnswer</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$date</td>
                                            <form method='POST'>
                                            <td scope=\"row\" style='word-break:break-all;'>
                                              <div class='btn-group-vertical' role='group' aria-label='Vertical button group'>
                                                <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' onclick=\"window.open('viewticket?ID=$ticketID')\" data-ripple-color='dark'><i class='fas fa-external-link mr-2'></i>View Ticket</button>
                                              </div>
                                            </td>
                                            </form>
                                        </tr>";
                                        }
                                    echo "</tbody></table>";
                            }
                            ?>
                          </div>
                      </div>
                    </div>
                    <div class="col-xl-12">
                      <div class="card tickets-table bg-dark table" style="display: table;">
                          <div class="card-body d-flex flex-column">
                            <p class="text-darkwhite font-weight-bolder text-muted">Closed Tickets</p>
                            <?php
                            if($getRank == "Admin"){
                                
                                $get = pdoQuery($db, "SELECT * FROM `support_tickets` WHERE `closed`=?", [htmlspecialchars('1')]);
                                $results = $get->fetchAll(PDO::FETCH_ASSOC);
    
                                echo "<div class='table-responsive'>";
                                    echo "<table id='table-dataTable-allTicketsClosed' class='table table-hover text-darkwhite'>";
                                        echo "<thead class='text-nowrap'>
                                        <tr>
                                            <th scope='col' class='font-weight-bolder'>Ticket Owner</th>
                                            <th scope='col' class='font-weight-bolder'>Title</th>
                                            <th scope='col' class='font-weight-bolder'>Priority</th>
                                            <th scope='col' class='font-weight-bolder'>Section</th>
                                            <th scope='col' class='font-weight-bolder'>Last Updated</th>
                                            <th scope='col' class='font-weight-bolder'>Last Answer</th>
                                            <th scope='col' class='font-weight-bolder'>Date</th>
                                            <th scope='col' class='font-weight-bolder'>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>";
                                        foreach($results as $result){
                                            $ticketownerid = htmlspecialchars($result['id']);
                                            $title = htmlspecialchars($result['title']);
                                            $priority = htmlspecialchars($result['priority']);
                                            $section = htmlspecialchars($result['topic']);
                                            $lastUpdate = htmlspecialchars($result['lastUpdate']);
                                            $lastAnswer = htmlspecialchars($result['lastAnswer']);
                                            $date = htmlspecialchars($result['date']);
                                            $ticketID = htmlspecialchars($result['ticketID']);
    
                                            $ticketOwner = htmlspecialchars(pdoQuery($db, "SELECT `username` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketownerid)])->fetchColumn());
                                            echo "<tr>
                                            <td scope=\"row\" style='word-break:break-all;'>$ticketOwner</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$title</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$priority</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$section</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$lastUpdate</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$lastAnswer</td>
                                            <td scope=\"row\" style='word-break:break-all;'>$date</td>
                                            <form method='POST'>
                                            <td scope=\"row\" style='word-break:break-all;'>
                                              <div class='btn-group-vertical' role='group' aria-label='Vertical button group'>
                                                <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' onclick=\"window.open('viewticket?ID=$ticketID')\" data-ripple-color='dark'><i class='fas fa-external-link mr-2'></i>View Ticket</button>
                                              </div>
                                            </td>
                                            </form>
                                        </tr>";
                                        }
                                    echo "</tbody></table>";
                            }
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

    <!-- Central Modal Small -->
<div class="modal fade" id="view-modal" tabdashboard="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">

  <!-- Change class .modal-sm to change the size of the modal -->
  <div class="modal-dialog modal-lg" role="document">


    <div class="modal-content">
      <div class="modal-header bg-dark text-darkwhite">
        <h5 class="modal-title w-100" id="myModalLabel">How to verify your payment</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="modalButton">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-dark text-darkwhite">
        <h5>First Step</h5>
        Make sure you are in the IXWare discord server
        <br><br><br>
        <h5>Second Step</h5>
        Make sure that your roblox inventory is public.
        <br><br><br>
        <h5>Third Step</h5>
        Change your roblox description/bio to: <strong class="text-lightred">hi</strong>
        <br><br><br><br>
        <p class="text-lightred">The verification code will change on every page-refresh.</p>
      </div>
    </div>
  </div>
</div>
<!-- Central Modal Small -->


  <?php require_once 'includes/layout/footer.php'; require_once 'includes/realtime.php';?>