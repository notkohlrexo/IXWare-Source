<?php
	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die('{"errors":[{"code":401,"message":"Unauthorized"}]}');
	
	define('DB_HOST', 'ip');
	define('DB_NAME', 'ixware');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD','fewVx7uBp5ZmP2Dua3phH8ySj43W2T542MZV3GtTh2Egf');
	define('ERROR_MESSAGE', 'Failed to connect to database!');

	try{
		$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USERNAME, DB_PASSWORD);

	}catch( PDOException $Exception ) {

		error_log('ERROR: '.$Exception->getMessage().' - '.$_SERVER['REQUEST_URI'].' at '.date('l jS \of F, Y, h:i:s A')."\n", 3, 'error.log');
		die(ERROR_MESSAGE);
	}

    function error($string){  
		return '<div class="alert alert-dismissible fade show" role="alert" data-color="danger">'.$string.'</div>';
	}
	function success($string) {
		return '<div class="alert alert-dismissible fade show" role="alert" data-color="success">'.$string.'</div>';
	}
?>