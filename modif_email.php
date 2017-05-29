<?php
	session_start();
	include_once('config/database.php');

	if (isset($_POST['login']) AND isset($_POST['password']) AND isset($_POST['oldemail']) AND isset($_POST['newemail']) AND ($_POST['login'] != '') AND ($_POST['password'] != '') AND ($_POST['oldemail'] != '') AND ($_POST['newemail'] != ''))
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
		$password = htmlspecialchars($_POST['password']);
		$hashPassword = hash("whirlpool", $password);

		if ((filter_var($_POST['oldemail'], FILTER_VALIDATE_EMAIL) == FALSE) OR (filter_var($_POST['newemail'], FILTER_VALIDATE_EMAIL) == FALSE))
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN EMAIL VALIDE";
		else {
			$oldemail = htmlspecialchars($_POST['oldemail']);
			$newemail = htmlspecialchars($_POST['newemail']);

		}
		
		if (isset($hashPassword) AND isset($oldemail) AND isset($newemail) AND isset($login) AND ($login == $_SESSION['login'])) {
			$req = $db->prepare('SELECT * FROM users WHERE login = ?');
			$req->execute(array($login));
			$ret = $req->fetch();
			
			if (($oldemail == $ret['email']) AND ($hashPassword == $ret['password'])) {
				$req2 = $db->prepare('UPDATE users SET email = ? WHERE login = ?');
				$req2->execute(array($newemail, $login));
				$req2->closeCursor();
				$subject = "L'adresse mail de votre compte Camagru a été modifiée!";
				$header = "From: Camagru";
				$message = '
          
        		Bonjour '.$login.'!

        		Suite à votre demande de modification, votre adresse mail de compte a changé.
        		Pensez à utiliser la bonne adresse mail la prochaine fois que vous vous connecterez!
          
          
        		---------------
        		Ceci est un mail automatique, Merci de ne pas y répondre.';
          
          
        		mail($newemail, $subject, $message, $header);
				$_SESSION['alert'] = "VOUS ALLEZ RECEVOIR UN EMAIL CONFIRMANT LA MODIFICATION DE VOTRE ADRESSE MAIL SUR LA NOUVELLE ADRESSE RENSEIGNÉE";
			}
			else if ($ret['email'] != $oldemail) {
				$_SESSION['alert'] = "ADRESSE EMAIL ERRONÉE!";
			}
			else if ($ret['password'] != $hashPassword) {
				$_SESSION['alert'] = "MOT DE PASSE ERRONÉ!";
			}
			$req->closeCursor();
			unset($ret);
		}
		else if ($login != $_SESSION['login']) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER LE BON LOGIN!";
		}
	}
	else {
		$_SESSION['alert'] = "VEUILLEZ REMPLIR TOUS LES CHAMPS!";
	}
	header('Location: account.php');
?>