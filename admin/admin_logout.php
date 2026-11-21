<?php
//start the session
session_start();
//unsettting sessions
$_SESSION = [];

//destroying  the session
session_destroy();

//redirecting to the login page
header('Location: admin_login.php');
exit;

