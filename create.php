<?php
	session_start();
	include_once('config/database.php');

	if (isset($_POST['login']) AND isset($_POST['email']) AND isset($_POST['password']) AND ($_POST['login'] != '') AND ($_POST['email'] != '') AND ($_POST['password'] != ''))
	{
		try
		{
    		$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
   			echo $e->getMessage();
   		}

   		if ((strlen($_POST['login']) <= 10) AND (strlen($_POST['login']) >= 5)) {
			$login = htmlspecialchars($_POST['login']);
		}
		else if (strlen($_POST['login']) > 10) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN LOGIN DE MOINS DE 10 CARACTÈRES";
		}
		else if (strlen($_POST['login']) < 5) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN LOGIN DE PLUS DE 5 CARACTÈRES";
		}

		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == FALSE)
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN EMAIL VALIDE";
		else {
			$email = htmlspecialchars($_POST['email']);
		}

		if ((strlen($_POST['password']) <= 10) AND (strlen($_POST['password']) >= 5)) {
			$password = htmlspecialchars($_POST['password']);
			if (preg_match("/[0-9]/", $password))
				$hashPassword = hash("whirlpool", $password);
			else
				$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE CONTENANT AU MOINS UN CHIFFRE";
		}
		else if (strlen($_POST['password']) > 10) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE DE MOINS DE 10 CARACTÈRES";
		}
		else if (strlen($_POST['password']) < 5) {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE DE PLUS DE 5 CARACTÈRES";
		}

		if (isset($hashPassword) AND isset($email) AND isset($login)) {
			$req = $db->prepare('SELECT * FROM users WHERE login = ?');
			$req->execute(array($login));
			$ret = $req->fetch();
			$req2 = $db->prepare('SELECT * FROM users WHERE email = ?');
			$req2->execute(array($email));
			$ret2 = $req2->fetch();

			if (($ret == FALSE) AND ($ret2 == FALSE)) {
				$key = md5(microtime(TRUE)*100000);
				$req3 = $db->prepare('INSERT INTO users(login, email, password, `key`) VALUES (?, ?, ?, ?)');
				$req3->execute(array($login, $email, $hashPassword, $key));
				$req3->closeCursor();
				$subject = "Activez votre compte Camagru!";
				$header = "From: Camagru";
				$message = '
          
        		Bonjour '.$login.'!

        		Pour valider votre compte Camagru, veuillez cliquer sur le lien ci dessous
        		ou le copier/coller dans votre navigateur internet.
          
        		http://localhost:8080/Camagru/validate.php?login='.urlencode($login).'&key='.urlencode($key).'
          
          
        		---------------
        		Ceci est un mail automatique, Merci de ne pas y répondre.';
          
          
        		mail($email, $subject, $message, $header);
				$_SESSION['alert'] = "UN EMAIL VIENT DE VOUS ÊTRE ENVOYÉ POUR ACTIVER VOTRE COMPTE";
			}
			else if ($ret) {
				$req->closeCursor();
				$_SESSION['alert'] = "LOGIN DÉJÀ UTILISÉ!";
				unset($ret);
			}
			else if ($ret2) {
				$req2->closeCursor();
				$_SESSION['alert'] = "EMAIL DÉJÀ UTILISÉ!";
				unset($ret2);
			}
		}
	}
	else {
		$_SESSION['alert'] = "VEUILLEZ REMPLIR TOUS LES CHAMPS!";
	}
	header('Location: index.php');
?>