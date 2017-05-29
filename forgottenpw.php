<?php
	session_start();
	include_once('config/database.php');

	if (isset($_POST['login']) AND ($_POST['login'] != '')) {
		try
		{
    		$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
   			echo $e->getMessage();
   		}

   		$req = $db->prepare('SELECT * FROM users WHERE login = ?');
		$req->execute(array($_POST['login']));
		$ret = $req->fetch();
			
		if ($ret) {
			$pw = time();
			$temppw = hash("whirlpool", $pw);
			$req2 = $db->prepare('UPDATE users SET password = ? WHERE login = ?');
			$req2->execute(array($temppw, $_POST['login']));
			$req2->closeCursor();

			$subject = "Le mot de passe temporaire de votre compte Camagru";
			$header = "From: Camagru";
			$message = '
	          
	        Bonjour '.$_POST['login'].'!

	        Suite à votre demande de réinitialisation, voici votre mot de passe temporaire :

	        		'.$pw.'

	        Pensez à utiliser celui-ci la prochaine fois que vous vous connecterez!
	        Vous pourrez alors le personnaliser dans la rubrique Compte une fois connecté.
	          
	          
	        ---------------
	        Ceci est un mail automatique, Merci de ne pas y répondre.';
	          
	          
	        mail($ret['email'], $subject, $message, $header);
			$_SESSION['alert'] = "VOUS ALLEZ RECEVOIR UN EMAIL CONTENANT VOTRE MOT DE PASSE TEMPORAIRE";
		}
		else {
			$_SESSION['alert'] = "CE LOGIN N'EXISTE PAS!";
		}
	}
	else {
		$_SESSION['alert'] = "VEUILLEZ REMPLIR TOUS LES CHAMPS!";
	}
	header('Location: index.php');

?>