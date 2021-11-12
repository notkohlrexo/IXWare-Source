<?php
      if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}');
      set_error_handler("myErrorHandler");
      function myErrorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return;
        }
    
        switch ($errno) {
            case E_USER_ERROR:
            echo "<b>IXWare Error - Report to developer</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Aborting...<br />\n";
            exit(1);
            break;
    
        case E_USER_WARNING:
            echo "<b>IXWare Warning - Report to developer</b> [$errno] $errstr $errline<br />\n";
            break;
    
        case E_USER_NOTICE:
            echo "<b>IXWare Notice - Report to developer</b> [$errno] $errstr $errline<br />\n";
            break;
    
        default:
            echo "Unknown error type - Report to developer: [$errno] $errstr $errline<br />\n";
            break;
        }
    
        /* Don't execute PHP internal error handler */
        return true;
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>IXWare - <?php echo htmlentities($title) ?></title>
    <!-- MDB icon -->
    <link rel="icon" href="resources/img/logo.png" type="image/x-icon" />
    <!-- Fontawesome -->
    <link rel="stylesheet" href="resources/css/all.min.css">
    <link rel="stylesheet" href="resources/css/fontawesome.min.css">
    <!-- Google Fonts Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" />
    <!-- Google Fonts Poppins -->
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <!-- Hover Effect -->
    <link rel="stylesheet" href="resources/css/hover-min.css">
    <!-- MDB -->
    <link rel="stylesheet" href="resources/css/mdb.min.css" />
    <!-- MDBootstrap Datatables  -->
    <link href="resources/css/addons/datatables.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="resources/css/style.css" />
  </head>