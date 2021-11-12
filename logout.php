<?php
	include_once 'includes/functions.php';

	session_start();
	session_regenerate_id(true);

    setcookie("login", $_SESSION['username'], time() - 86400);
	unset($_SESSION['username']);
	unset($_SESSION['ID']);
	session_destroy();

    header('location: login');
    exit();
?>