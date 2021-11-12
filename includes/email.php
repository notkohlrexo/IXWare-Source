<?php
    if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}');

    require("phpmailer/PHPMailer.php");
    require("phpmailer/SMTP.php");
    require("phpmailer/Exception.php");

    function sendEmail($email, $subject, $body){
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->IsSMTP(); // enable SMTP
    
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtp.dreamhost.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "administration@ixwhere.com";
        $mail->Password = "lol";
        $mail->SetFrom("administration@ixwhere.com");
        $mail->Subject = "$subject";
        $mail->Body = "$body";
        $mail->AddAddress($email);
    
         if($mail->Send()) {
            return "A new verification e-mail has been resent to your e-mail address.";
         }else{
            return "An error occured.";
         }
    }

?>