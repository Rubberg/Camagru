<?php
session_start();
include_once('config/database.php');


if (isset($_POST['picid']) AND isset($_POST['comment']) AND ($_POST['picid'] != '') AND ($_POST['comment']) != '') {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = htmlspecialchars($value);
	}

	try {
		$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}
	catch(PDOException $e) {
	   	echo $e->getMessage();
	}

	$req = $db->prepare('INSERT INTO comments(login, user_id, `text`, pic_id) VALUES (?, ?, ?, ?)');
	$req->execute(array($_SESSION['login'], $_SESSION['userid'], $_POST['comment'], $_POST['picid']));
	$req->closeCursor();

	$req = $db->prepare('SELECT * FROM photos WHERE id = ?');
	$req->execute(array($_POST['picid']));
	$data = $req->fetch();
	$req->closeCursor();

	$req2 = $db->prepare('SELECT * FROM users WHERE id = ?');
	$req2->execute(array($data['user_id']));
	$data2 = $req2->fetch();
	$req2->closeCursor();


	$subject = "Une de vos photos a été commentée!";
	$header = "From: Camagru";
	$message = '
          
        		Bonjour '.$data2['login'].'!

        		Un utilisateur de Camagru a commenté une de vos photos!

        		En voici le contenu : '.$_POST['comment'].'

        		Connectez vous vite pour lui répondre!
          
        		---------------
        		Ceci est un mail automatique, Merci de ne pas y répondre.';
          
          
    mail($data2['email'] , $subject, $message, $header);
}
else {
	$_SESSION['alert'] = "VOTRE COMMENTAIRE EST VIDE!";
}
header('location: gallery.php');
?>