<?php
	session_start();
	include_once('config/database.php');

	if (isset($_GET['login']) AND isset($_GET['key']) AND ($_GET['login'] != '') AND ($_GET['key'] != '')) {
		try
		{
    		$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
   			echo $e->getMessage();
   		}
		$req = $db->prepare('SELECT * FROM users WHERE login = :login');
		$req->execute(array('login'=>$_GET['login']));

		$data = $req->fetch();
		if (($data['login'] = $_GET['login']) AND ($data['key'] = $_GET['key'])) {
			$key = "validate";
			$req->closeCursor();
			$req = $db->prepare('UPDATE users SET `key` = ? WHERE login = ?');
			$req->execute(array($key, $_GET['login']));
			$_SESSION['alert'] = "VOTRE COMPTE EST MAINTENANT ACTIVÉ, VOUS POUVEZ VOUS CONNECTER!";
		}
	}
	else {
		$_SESSION['alert'] = "UNE ERREUR S'EST PRODUITE, VEUILLEZ UITILISER À NOUVEAU LE MAIL QUI VOUS A ÉTÉ ENVOYÉ OU RECOMMENCER VOTRE INSCRIPTION";
	}
	header('Location: index.php');

?>