<?php 
    @session_start();
    $title = 'Ticket';
    include_once 'includes/layout/header.php';
    include_once 'includes/checks.php';
    include_once 'includes/botconfig.php';
    include_once 'loader.php';
    include_once __DIR__.'/../vendor/autoload.php';

    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

    if(checkMaintenance() == 'Maintenance' && $getRank != "Admin"){
      header('Location: dashboard');
      die();
    }

    if(isset($_SESSION['username'])){
        $username = htmlspecialchars($_SESSION['username']);
      }else{
        $username = "Unknown";
    }

    if(isset($_GET['ID'])){

        if(checkTicket(htmlspecialchars($_GET['ID'])) == 0){

            header("HTTP/1.1 401 Unauthorized");
            echo file_get_contents('includes/layout/error/401.php');
            die();
        }else{

            $ticketOwner = pdoQuery($db, "SELECT `id` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
            if($ticketOwner != htmlspecialchars($_SESSION['ID']) && $getRank != "Admin"){
                header("HTTP/1.1 401 Unauthorized");
                echo file_get_contents('includes/layout/error/401.php');
                die();
            }
        }
    }

    $title = pdoQuery($db, "SELECT `title` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
    $priority = pdoQuery($db, "SELECT `priority` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
    $topic = pdoQuery($db, "SELECT `topic` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
    $closed = pdoQuery($db, "SELECT `closed` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
    $lastUpdate = pdoQuery($db, "SELECT `lastUpdate` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
    $date = pdoQuery($db, "SELECT `date` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
    $description = htmlspecialchars(pdoQuery($db, "SELECT `description` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn());

    $ticketid = htmlspecialchars($_GET['ID']);
    $ticketOwner = pdoQuery($db, "SELECT `id` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();
    $ticketUsername = htmlspecialchars(pdoQuery($db, "SELECT `username` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
    $ticketRank = htmlspecialchars(pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
    $isBanned = htmlspecialchars(pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());

    if($isBanned != "0"){
        $isBanned = "True";
    }elseif($isBanned == "0"){
        $isBanned = "False";
    }
    if($closed == "1"){
        $closed = "True";
    }elseif($closed == "0"){
        $closed = "False";
    }
?>

<?php

if (isset($_POST['submitAnswer'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if (!empty($_POST['ticketAnswer'])){

            if (!empty($_GET['ID'])){

                if($closed == "False"){

                    $answer = htmlspecialchars($_POST['ticketAnswer']);
                    if(strlen($answer) < 1001){
    
                        $id = htmlspecialchars($_GET['ID']);
                        if(checkTicket($id) == 1){
                            
                            $PDOLog = $db -> prepare('INSERT INTO `support_ticket_answer` VALUES(:id, :userid, :description, :date)');
                            $PDOLog -> execute(array(':id' => $id, ':userid' => htmlspecialchars($_SESSION['ID']), ':description' => $answer, ':date' => date("Y-m-d H:i:s")));
            
                            $PDOS = $db -> prepare('UPDATE `support_tickets` SET `lastUpdate` = :last WHERE `ticketID` = :id');
                            $PDOS -> execute(array(':last' => date("Y-m-d H:i:s"), ':id' => $id));
        
                            $PDOS1 = $db -> prepare('UPDATE `support_tickets` SET `lastAnswer` = :last WHERE `ticketID` = :id');
                            $PDOS1 -> execute(array(':last' => htmlspecialchars($_SESSION['username']), ':id' => $id));

                            $getDiscord = htmlspecialchars(pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
                            if($getRank == "Admin"){

                              if($getDiscord != 0){

                                try{
                                  $dm = $discord->user->createDm(['recipient_id' => (int)$getDiscord]);
                                  $discord->channel->createMessage(['content' => "You got a new ticket-answer, check it out now: https://ixwhere.online/viewticket?ID=$ticketid", 'channel.id' => $dm->id]);
                                }catch(Exception $e){
                                }
                              }
                            }
                            try{
                                $discord->channel->createMessage(
                                    [
                                        'channel.id' => $ticketChannel,
                                        'embed'      => [
                                            'timestamp' => date("c"),
                                            "color" => hexdec("#a30a0a"),
                                            'title' => 'Ticket Answer '.date('Y-m-d H:i:s'),
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
                                                    "name" => "Answer",
                                                    "value" => $answer
                                                ]
                                            ]
                                        ],
                                    ]
                                );
                            }catch(Exception $e){
                            }
                        }else{
                            $error = "An error occured, please try again.";
                        }
                    }else{
                        $error = "The character limit for answers is 1000.";
                    }
                }else{
                    $error = "The ticket is closed.";
                }
            }
        }else{
            $error = "You need to submit an answer.";
        }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }

  if (isset($_POST['closeTicket'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if (!empty($_GET['ID'])){

            $id = htmlspecialchars($_GET['ID']);
            if(checkTicket($id) == 1){
                
                $PDOS = $db -> prepare('UPDATE `support_tickets` SET `lastUpdate` = :last WHERE `ticketID` = :id');
                $PDOS -> execute(array(':last' => date("Y-m-d H:i:s"), ':id' => $id));

                $PDOS1 = $db -> prepare('UPDATE `support_tickets` SET `closed` = :one WHERE `ticketID` = :id');
                $PDOS1 -> execute(array(':one' =>'1', ':id' => $id));

                $PDOS2 = $db -> prepare('UPDATE `support_tickets` SET `lastAnswer` = :last WHERE `ticketID` = :id');
                $PDOS2 -> execute(array(':last' => htmlspecialchars($_SESSION['username']), ':id' => $id));

                $getDiscord = htmlspecialchars(pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
                if($getRank == "Admin"){

                  if($getDiscord != 0){

                    try{
                      $dm = $discord->user->createDm(['recipient_id' => (int)$getDiscord]);
                      $discord->channel->createMessage(['content' => "Your ticket was closed by an administrator, check it out now: https://ixwhere.online/viewticket?ID=$ticketid", 'channel.id' => $dm->id]);
                    }catch(Exception $e){
                    }
                  }
                }

                try{
                    $discord->channel->createMessage(
                        [
                            'channel.id' => $ticketChannel,
                            'embed'      => [
                                'timestamp' => date("c"),
                                "color" => hexdec("#a30a0a"),
                                'title' => 'Ticket Closed '.date('Y-m-d H:i:s'),
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
                                        "name" => "By Username",
                                        "value" => htmlspecialchars($_SESSION['username'])
                                    ]
                                ]
                            ],
                        ]
                    );
                }catch(Exception $e){
                }

                $success = "Successfully updated the ticket.";
            }else{
                $error = "An error occured, please try again.";
            }
        }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }

  if (isset($_POST['reopenTicket'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if (!empty($_GET['ID'])){

            if($getRank == "Admin"){

                $id = htmlspecialchars($_GET['ID']);
                if(checkTicket($id) == 1){
                    
                    $PDOS = $db -> prepare('UPDATE `support_tickets` SET `lastUpdate` = :last WHERE `ticketID` = :id');
                    $PDOS -> execute(array(':last' => date("Y-m-d H:i:s"), ':id' => $id));
    
                    $PDOS1 = $db -> prepare('UPDATE `support_tickets` SET `closed` = :zero WHERE `ticketID` = :id');
                    $PDOS1 -> execute(array(':zero' =>'0', ':id' => $id));
    
                    $PDOS2 = $db -> prepare('UPDATE `support_tickets` SET `lastAnswer` = :last WHERE `ticketID` = :id');
                    $PDOS2 -> execute(array(':last' => htmlspecialchars($_SESSION['username']), ':id' => $id));
    
                    $getDiscord = htmlspecialchars(pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
                    if($getRank == "Admin"){
    
                      if($getDiscord != 0){
    
                        try{
                          $dm = $discord->user->createDm(['recipient_id' => (int)$getDiscord]);
                          $discord->channel->createMessage(['content' => "Your ticket was re-opened by an administrator, check it out now: https://ixwhere.online/viewticket?ID=$ticketid", 'channel.id' => $dm->id]);
                        }catch(Exception $e){
                        }
                      }
                    }

                    try{
                        $discord->channel->createMessage(
                            [
                                'channel.id' => $ticketChannel,
                                'embed'      => [
                                    'timestamp' => date("c"),
                                    "color" => hexdec("#a30a0a"),
                                    'title' => 'Ticket Re-Opened '.date('Y-m-d H:i:s'),
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
                                            "name" => "By Username",
                                            "value" => htmlspecialchars($_SESSION['username'])
                                        ]
                                    ]
                                ],
                            ]
                        );
                    }catch(Exception $e){
                    }

                    $success = "Successfully updated the ticket.";
                }else{
                    $error = "An error occured, please try again.";
                }
            }
        }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }

  if (isset($_POST['deleteTicket'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if (!empty($_GET['ID'])){

            if($getRank == "Admin"){

                $id = htmlspecialchars($_GET['ID']);
                if(checkTicket($id) == 1){
                    
                    $PDODelete = $db -> prepare('DELETE FROM `support_tickets` WHERE `ticketID` = :id');
                    $PDODelete -> execute(array(':id' => $id));  
                   
                    try{
                        $discord->channel->createMessage(
                            [
                                'channel.id' => $ticketChannel,
                                'embed'      => [
                                    'timestamp' => date("c"),
                                    "color" => hexdec("#a30a0a"),
                                    'title' => 'Ticket Deleted '.date('Y-m-d H:i:s'),
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
                                            "name" => "By Username",
                                            "value" => htmlspecialchars($_SESSION['username'])
                                        ]
                                    ]
                                ],
                            ]
                        );
                    }catch(Exception $e){
                    }

                    header('Location: support');
                    die();
                }else{
                    $error = "An error occured, please try again.";
                }
            }
        }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }

  if (isset($_POST['unbanUser'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if (!empty($_GET['ID'])){

            if($getRank == "Admin"){

                $id = htmlspecialchars($_GET['ID']);
                if(checkTicket($id) == 1){

                    $PDOS = $db -> prepare('UPDATE `users` SET `banned` = :ban WHERE `id` = :id');
                    $PDOS -> execute(array(':ban' => '0', ':id' => $ticketOwner));
    
                    $PDOS1 = $db -> prepare('UPDATE `users` SET `countryChanges` = :ban WHERE `id` = :id');
                    $PDOS1 -> execute(array(':ban' => '0', ':id' => $ticketOwner));
    

                    $getDiscord = htmlspecialchars(pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
                    if($getRank == "Admin"){
    
                      if($getDiscord != 0){
    
                        try{
                          $dm = $discord->user->createDm(['recipient_id' => (int)$getDiscord]);
                          $discord->channel->createMessage(['content' => "You got unbanned by an administrator via a ticket, check it out now: https://ixwhere.online/viewticket?ID=$ticketid", 'channel.id' => $dm->id]);
                        }catch(Exception $e){
                        }
                      }
                    }

                    try{
                        $discord->channel->createMessage(
                            [
                                'channel.id' => $ticketChannel,
                                'embed'      => [
                                    'timestamp' => date("c"),
                                    "color" => hexdec("#a30a0a"),
                                    'title' => 'Ticket - User unbanned '.date('Y-m-d H:i:s'),
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
                                            "name" => "By Username",
                                            "value" => htmlspecialchars($_SESSION['username'])
                                        ]
                                    ]
                                ],
                            ]
                        );
                    }catch(Exception $e){
                    }

                    $success = "Successfully unbanned the user.";
                }else{
                    $error = "An error occured, please try again.";
                }
            }
        }
    }else{
      header("HTTP/1.1 401 Unauthorized");
      echo file_get_contents('includes/layout/error/401.php');
      die();
    }
  }

  if (isset($_POST['fullBan'])){

    if (\Volnix\CSRF\CSRF::validate($_POST)) {

        if (!empty($_GET['ID'])){

            if($getRank == "Admin"){

                $id = htmlspecialchars($_GET['ID']);
                if(checkTicket($id) == 1){

                    $PDOS = $db -> prepare('UPDATE `users` SET `fullBanned` = :ban WHERE `id` = :id');
                    $PDOS -> execute(array(':ban' => '1', ':id' => $ticketOwner));
    
                    try{
                        $discord->channel->createMessage(
                            [
                                'channel.id' => $ticketChannel,
                                'embed'      => [
                                    'timestamp' => date("c"),
                                    "color" => hexdec("#a30a0a"),
                                    'title' => 'Ticket - User Fully Banned '.date('Y-m-d H:i:s'),
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
                                            "name" => "By Username",
                                            "value" => htmlspecialchars($_SESSION['username'])
                                        ]
                                    ]
                                ],
                            ]
                        );
                    }catch(Exception $e){
                    }

                    $success = "Successfully banned the user.";
                }else{
                    $error = "An error occured, please try again.";
                }
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
            <li class="breadcrumb-item text-white">Ticket-System</li>
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
            <div class="col-xl-3">
                <div class="card bg-dark ticket-answer-card">
                  <div class="card-body d-flex flex-column">
                        <ul class="list-group">
                            <li class="list-group-item text-darkwhite" style="background-color: transparent !important;border-color: #2e2e2e;"><p class="font-weight-bolder text-left text-darkwhite">Title</p> <?= htmlspecialchars($title) ?></li>
                            <li class="list-group-item text-darkwhite" style="background-color: transparent !important;border-color: #2e2e2e;"><p class="font-weight-bolder text-left text-darkwhite">Priority</p> <?= htmlspecialchars($priority) ?></li>
                            <li class="list-group-item text-darkwhite" style="background-color: transparent !important;border-color: #2e2e2e;"><p class="font-weight-bolder text-left text-darkwhite">Topic</p> <?= htmlspecialchars($topic) ?></li>
                            <li class="list-group-item text-darkwhite" style="background-color: transparent !important;border-color: #2e2e2e;"><p class="font-weight-bolder text-left text-darkwhite">Is Banned</p> <?= htmlspecialchars($isBanned) ?></li>
                            <?php
                              if($isBanned == "True"){
                                $banReason = htmlspecialchars(pdoQuery($db, "SELECT `banReason` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
                                $banExpire = htmlspecialchars(pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());

                                if($banExpire != 0){

                                  $datex = new DateTime($banExpire);
                                  $endDate = htmlspecialchars($datex->format("D M d, Y G:i"));
                                  echo "<li class='list-group-item text-darkwhite' style='background-color: transparent !important;border-color: #2e2e2e;'><p class='font-weight-bolder text-left text-darkwhite'>Ban Reason</p> $banReason</li>";
                                  echo "<li class='list-group-item text-darkwhite' style='background-color: transparent !important;border-color: #2e2e2e;'><p class='font-weight-bolder text-left text-darkwhite'>Banned Until</p> $endDate</li>";
                                }
                              }
                            ?>
                            <li class="list-group-item text-darkwhite" style="background-color: transparent !important;border-color: #2e2e2e;"><p class="font-weight-bolder text-left text-darkwhite">Closed</p> <?= htmlspecialchars($closed) ?></li>
                            <li class="list-group-item text-darkwhite" style="background-color: transparent !important;border-color: #2e2e2e;"><p class="font-weight-bolder text-left text-darkwhite">Last Update</p> <?= htmlspecialchars($lastUpdate) ?></li>
                            <li class="list-group-item text-darkwhite" style="background-color: transparent !important;border-color: #2e2e2e;"><p class="font-weight-bolder text-left text-darkwhite">Creation Date</p> <?= htmlspecialchars($date) ?></li>
                        </ul>
                  </div>
                </div>
                <?php
                    $closed = pdoQuery($db, "SELECT `closed` FROM `support_tickets` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])])->fetchColumn();

                    if($closed == "0"){

                        if($getRank != "Admin"){

                            echo "<form method='POST'><input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/><button type='submit' name='closeTicket' value='ticket' class='btn btn-outline-warning waves-effect center-button mt-2' style='width: 100%;'>Close Ticket</button></form>";
                        
                        }elseif($getRank == "Admin"){

                            echo "<form method='POST'><input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/><button type='submit' name='closeTicket' value='ticket' class='btn btn-outline-warning waves-effect center-button mt-2' style='width: 100%;'>Close Ticket</button></form>";
                        }

                    }elseif($closed == "1"){

                        if($getRank == "Admin"){

                            echo "<form method='POST'><input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/><button type='submit' name='reopenTicket' value='ticket' class='btn btn-outline-success waves-effect center-button mt-2' style='width: 100%;'>Re-Open Ticket</button></form>";
                        }
                    }

                    if($getRank == "Admin"){

                        echo "<form method='POST'><input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/><button type='submit' name='deleteTicket' value='ticket' class='btn btn-outline-danger waves-effect center-button mt-2' style='width: 100%;'>Delete Ticket</button></form>";
                        echo "<form method='POST'><input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/><button type='submit' name='fullBan' value='ticket' class='btn btn-outline-danger waves-effect center-button mt-2' style='width: 100%;'>Fully Ban User</button></form>";

                        if($isBanned == "True"){

                            $banExpire = htmlspecialchars(pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($ticketOwner)])->fetchColumn());
                            
                            if($banExpire != 0){

                              echo "<form method='POST'><input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/><button type='submit' name='unbanUser' value='ticket' class='btn btn-outline-danger waves-effect center-button mt-2' style='width: 100%;'>Unban User</button></form>";
                            }
                        }
                    }
                ?>
            </div>
            <div class="col-xl-8">
                <?php
                    if(!empty($error)){
                        echo '<div class="animated fadeIn">'.error(htmlspecialchars($error)).'</div>';
                    }
                    if(!empty($success)){
                        echo '<div class="animated fadeIn">'.success(htmlspecialchars($success)).'</div>';
                    }
                ?>
                <div class="card bg-dark ticket-answer-card">
                  <div class="card-body d-flex flex-column">
                    <?php
                        if($ticketRank == "Premium"){
                            echo "<p class='p-margin ml-2 mr-2 text-darkwhite' style='text-align:left;'>
                            <span class='badge bg-warning text-dark'>Premium</span> - $ticketUsername
                            <span style='float:right;'>
                            <strong class='text-muted'>$date</strong>
                            </span>
                            </p>";
                        }elseif($ticketRank == "User"){
                            echo "<p class='p-margin ml-2 mr-2 text-darkwhite' style='text-align:left;'>
                            <span class='badge bg-light text-dark'>Default</span> - $ticketUsername
                            <span style='float:right;'>
                            <strong class='text-muted'>$date</strong>
                            </span>
                            </p>";
                        }elseif($ticketRank == "Admin"){
                            echo "<p class='p-margin ml-2 mr-2 text-darkwhite' style='text-align:left;'>
                            <span class='badge bg-danger'>Administrator</span> - $ticketUsername
                            <span style='float:right;'>
                            <strong class='text-muted'>$date</strong>
                            </span>
                            </p>";
                        }
                    ?>
                    <hr style="margin-top: 7px !important;">
                    <p class="text-darkwhite p-margin"><?= htmlspecialchars($description) ?></p>
                  </div>
                </div>
                <?php
                $get = pdoQuery($db, "SELECT * FROM `support_ticket_answer` WHERE `ticketID`=?", [htmlspecialchars($_GET['ID'])]);
                $results = $get->fetchAll(PDO::FETCH_ASSOC);
        
                  if ($get->rowCount() > 0) {

                    foreach($results as $result){

                        $id = htmlspecialchars($result['ownerID']);
                        $description = htmlspecialchars($result['description']);
                        $date = htmlspecialchars($result['date']);
                        $username = pdoQuery($db, "SELECT `username` FROM `users` WHERE `id`=?", [htmlspecialchars($id)])->fetchColumn();
                        $rank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($id)])->fetchColumn();

                        echo "<div class='card bg-dark ticket-answer-card'>
                        <div class='card-body d-flex flex-column'>";
                        
                        if($rank == "Premium"){
                            echo "<p class='p-margin ml-2 mr-2 text-darkwhite' style='text-align:left;'>
                            <span class='badge bg-warning text-dark'>Premium</span> - $username
                            <span style='float:right;'>
                            <strong class='text-muted'>$date</strong>
                            </span>
                            </p>";
                        }elseif($rank == "User"){
                            echo "<p class='p-margin ml-2 mr-2 text-darkwhite' style='text-align:left;'>
                            <span class='badge bg-light text-dark'>Default</span> - $username
                            <span style='float:right;'>
                            <strong class='text-muted'>$date</strong>
                            </span>
                            </p>";
                        }elseif($rank == "Admin"){
                            echo "<p class='p-margin ml-2 mr-2 text-darkwhite' style='text-align:left;'>
                            <span class='badge bg-danger'>Administrator</span> - $username
                            <span style='float:right;'>
                            <strong class='text-muted'>$date</strong>
                            </span>
                            </p>";
                        }

                          echo "<hr style='margin-top: 7px !important;'>
                          <p class='text-darkwhite p-margin'>$description</p>
                        </div>
                      </div>";

                    }
                  }
                ?>
                <div class="card bg-dark ticket-answer-card mb-3">
                  <div class="card-body d-flex flex-column">
                      <?php
                        if($closed == "0"){

                            echo "<form method='POST' class='md-form md-form-resized'>
                            <div class='form-outline mt-1'>
                                <textarea class='form-control text-darkwhite' id='ticketAnswer' name='ticketAnswer' style='margin-top: 0px; margin-bottom: 0px; height: 94px;' rows='10'></textarea>
                                <label class='form-label' for='ticketAnswer'>Answer to the ticket</label>
                            </div>
                            <button type='submit' name='submitAnswer' value='answer' class='btn btn-outline-primary waves-effect mt-2 center-button settings-button' style='width: 100%;'>Answer</button>
                            <input type='hidden' name='" . \Volnix\CSRF\CSRF::TOKEN_NAME . "' value='" . \Volnix\CSRF\CSRF::getToken() . "'/>
                            </form>";
                        }elseif($closed == "1"){
                            
                            echo "<p class='font-weight-bolder text-left text-darkwhite'>Ticket is closed.</p>";
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

  <script>
    window.scrollTo(0,document.body.scrollHeight);
  </script>