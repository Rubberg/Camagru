<?PHP
	session_start();
	unset($_SESSION['login']);
	unset($_SESSION['alert']);
	unset($_SESSION['userid']);
	unset($_SESSION['email']);
	header('Location: index.php');
?>