<?php
    include_once 'includes/functions.php';
    include_once 'includes/checks.php';
     
    header('Content-type: application/json; charset=UTF-8');
    
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

    if($getRank != "Premium"){
        if($getRank != "Admin"){
          header('Location: dashboard.php');
          die();
        }
      }
     

     if (isset($_POST['id']) && !empty($_POST['id'])) {

        if($banned == 0){

          $get = pdoQuery($db, "SELECT * FROM `bots` WHERE `botID`=?", [htmlspecialchars($_POST['id'])]);
          $results = $get->fetchAll(PDO::FETCH_ASSOC);

          foreach($results as $result){
              $country = htmlspecialchars($result['country']);
              $botname = htmlspecialchars($result['botname']);
              $os = htmlspecialchars($result['os']);
              $ip = htmlspecialchars($result['ip']);
              $active = htmlspecialchars($result['active']);
              $last = htmlspecialchars($result['lastactivity']);

              echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
              <div class='p-2 bd-highlight><i class='fas fa-desktop mr-1'></i> MachineName</div>
              <div class='p-2 bd-highlight'>$botname</div>
              </div>";

              echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
              <div class='p-2 bd-highlight'><i class='fas fa-user mr-1'></i> UserName</div>
              <div class='p-2 bd-highlight'>$botname</div>
              </div>";

              echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
              <div class='p-2 bd-highlight'><i class='fab fa-windows mr-1'></i> OSVersion</div>
              <div class='p-2 bd-highlight'>$os</div>
              </div>";

              echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
              <div class='p-2 bd-highlight'><i class='fas fa-plug mr-1'></i> Active</div>
              <div class='p-2 bd-highlight'>$active</div>
              </div>";

              echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
              <div class='p-2 bd-highlight'><i class='fas fa-history mr-1'></i> Latest Activity</div>
              <div class='p-2 bd-highlight'>$last</div>
              </div>";

              echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
              <div class='p-2 bd-highlight'><i class='fas fa-globe-europe mr-1'></i> Country</div>
              <div class='p-2 bd-highlight'>$country</div>
              </div>";

              echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
              <div class='p-2 bd-highlight'><i class='fas fa-chart-network mr-1'></i> IP Address</div>
              <div class='p-2 bd-highlight'>$ip</div>
              </div>";

          }
        }else{
          echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-lightred'>
          <div class='p-2 bd-highlight'>You are banned. :'D</div>
          </div>";
        }
     }else{
       echo "<h1 class='text-black'>wattafackbro</h1>";
     }
?>