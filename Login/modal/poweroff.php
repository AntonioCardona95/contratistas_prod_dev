 <?php
 	ini_set('date.timezone','America/Monterrey'); 
	session_start();
	$_SESSION = array();
	session_destroy();
    unset($_SESSION);
    header("location: ../../index.html");
?> 