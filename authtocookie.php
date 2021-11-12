<?php
    include 'includes/functions.php';

    if (!empty($_GET['auth'])){
        $ticket = htmlspecialchars($_GET['auth']);
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
            echo $cookie;
        }
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
?>