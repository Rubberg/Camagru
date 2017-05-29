<?php
	session_start();
	include_once('config/database.php');

	$id = htmlspecialchars($_POST['picid']);
	$path = htmlspecialchars($_POST['picpath']);

	try {
		$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}
	catch(PDOException $e) {
   		echo $e->getMessage();
	}

	$req = $db->prepare('DELETE FROM photos WHERE id = ?');
	$req->execute(array($id));
	$req->closeCursor();
	$req = $db->prepare('DELETE FROM likes WHERE pic_id = ?');
	$req->execute(array($id));
	$req = $db->prepare('DELETE FROM comments WHERE pic_id = ?');
	$req->execute(array($id));
	$req->closeCursor();
	unlink($path);
	header('Location: gallery.php');

?>