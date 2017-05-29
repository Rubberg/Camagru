<?php
	session_start();
	include_once('config/database.php');

	if (isset($_POST['login']) AND isset($_POST['email']) AND isset($_POST['oldpwd']) AND isset($_POST['newpwd']) AND ($_POST['login'] != '') AND ($_POST['email'] != '') AND ($_POST['oldpwd'] != '') AND ($_POST['newpwd'] != ''))
	{
		try
		{
    		$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
   			echo $e->getMessage();
   		}

		$login = htmlspecialchars($_POST['login']);
		$email = htmlspecialchars($_POST['email']);

		if ((strlen($_POST['newpwd']) >= 5) AND (strlen($_POST['newpwd']) <= 10)) {
			$oldpwd = htmlspecialchars($_POST['oldpwd']);
			$hashOldpwd = hash("whirlpool", $oldpwd);
			$newpwd = htmlspecialchars($_POST['newpwd']);

			if (preg_match("/[0-9]/", $newpwd))
				$hashNewpwd = hash("whirlpool", $newpwd);
			else
				$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE CONTENANT AU MOINS UN CHIFFRE";
		}
		else if (strlen($_POST['newpwd']) > 10) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE DE MOINS DE 10 CARACTÈRES";
		}
		else if (strlen($_POST['newpwd']) < 5) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE DE PLUS DE 5 CARACTÈRES";
		}

		if (isset($hashOldpwd) AND isset($hashNewpwd) AND isset($email) AND isset($login) AND ($login == $_SESSION['login']) AND ($hashOldpwd != $hashNewpwd)) {
			$req = $db->prepare('SELECT * FROM users WHERE login = ?');
			$req->execute(array($login));
			$ret = $req->fetch();
			
			if (($email == $ret['email']) AND ($hashOldpwd == $ret['password'])) {
				$req2 = $db->prepare('UPDATE users SET password = ? WHERE login = ?');
				$req2->execute(array($hashNewpwd, $login));
				$req2->closeCursor();
				$subject = "Le mot de passe de votre compte Camagru a été modifié!";
				$header = "From: Camagru";
				$message = '
          
        		Bonjour '.$login.'!

        		Suite à votre demande de modification, votre mot de passe a changé.
        		Pensez à utiliser le bon mot de passe la prochaine fois que vous vous connecterez!
          
          
        		---------------
        		Ceci est un mail automatique, Merci de ne pas y répondre.';
          
          
        		mail($email, $subject, $message, $header);
				$_SESSION['alert'] = "VOUS ALLEZ RECEVOIR UN EMAIL CONFIRMANT LA MODIFICATION DE VOTRE MOT DE PASSE";
			}
			else if ($ret['email'] != $email) {
				$_SESSION['alert'] = "ADRESSE EMAIL ERRONÉE!";
			}
			else if ($ret['password'] != $hashOldpwd) {
				$_SESSION['alert'] = "MOT DE PASSE ERRONÉ!";
			}
			$req->closeCursor();
			unset($ret);
		}
		else if ($login != $_SESSION['login']) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER LE BON LOGIN!";
		}
		else if ($hashOldpwd == $hashNewpwd)
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE DIFFÉRENT DE L'ANCIEN!";
	}
	else {
		$_SESSION['alert'] = "VEUILLEZ REMPLIR TOUS LES CHAMPS!";
	}
	header('Location: account.php');
?>