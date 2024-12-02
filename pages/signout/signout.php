<?php
session_start();
ob_start();
$_SESSION = array();

// Set the cookie
setcookie('logged_out', 'true', time() + 3600, '/');

//end session and redirect
session_destroy();
header("Location: ../login/login.php");
exit();
