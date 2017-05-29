<?php
	session_start();
	include_once('config/database.php');

	if (isset($_POST['picid'])) {
		try {
			$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
   			echo $e->getMessage();
		}

		$req = $db->prepare("SELECT COUNT(*) AS nbr FROM likes WHERE pic_id = ? AND login = ?");
		$req->execute(array($_POST['picid'], $_SESSION['login']));
		$nbr = $req->fetch();

		if (($nbr['nbr'] == 0) OR ($nbr['nbr'] == '') ) {
			$req2 = $db->prepare("INSERT INTO likes(login, user_id, pic_id) VALUES(?, ?, ?)");
			$req2->execute(array($_SESSION['login'], $_SESSION['userid'], $_POST['picid']));
			$req2->closeCursor();

		}
	}
	else {
		$_SESSION['alert'] = "UNE ERREUR EST SURVENUE!";
	}
	$req->closeCursor();
	header('Location: gallery.php');
?>