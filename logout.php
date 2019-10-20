<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}
else {
	// log the user out by deleting the username and userid from the session
	unset($_SESSION['username'], $_SESSION['userid']);
	session_destroy();
	
	// redirect to the landing page
    header("Location: index.php");
    exit;
}
?>