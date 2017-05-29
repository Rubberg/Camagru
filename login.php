<?php
	session_start();
	include_once('config/database.php');
	if (isset($_POST['login']) AND isset($_POST['password']) AND ($_POST['login'] != '') AND ($_POST['password'] != ''))
    {
		try
		{
    		$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
    		echo $e->getMessage();
		}

		if (strlen($_POST['login']) <= 10) {
			$login = htmlspecialchars($_POST['login']);
		}
		else {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN LOGIN DE MOINS DE 10 CARACTÈRES";
		}

		if (strlen($_POST['password']) <= 10) {
			$password = htmlspecialchars($_POST['password']);
			$hashPassword = hash("whirlpool", $password);
		}
		else {
			$_SESSION['alert'] = "VEUILLEZ RENSEIGNER UN MOT DE PASSE DE MOINS DE 10 CARACTÈRES";
		}

		if (isset($hashPassword) AND isset($login)) {

			$req = $db->prepare('SELECT * FROM users WHERE login = ?');
			$req->execute(array($login));
			$data = $req->fetch();

			if (!$data) {
					$_SESSION['alert'] = "CE LOGIN N'ÉXISTE PAS!";
					header('Location: index.php');
					return;
				}

			$req->closeCursor();

			if (($hashPassword == $data['password']) AND ($login == $data['login']) AND ($data['key'] == 'validate')) {
				$_SESSION['login'] = $data['login'];
				$_SESSION['userid'] = $data['id'];
				$_SESSION['email'] = $data['email'];
				header('Location: index.php');
			}
			else if ($data['key'] !== 'validate') {
				$_SESSION['alert'] = "VEUILLEZ ACTIVER VOTRE COMPTE GRÂCE AU MAIL QUI VOUS A ÉTÉ ENVOYÉ!";
			}
			else if (($hashPassword != $data['password']) AND ($login == $data['login'])) {
				$_SESSION['alert'] = "MAUVAIS MOT DE PASSE!";
			}
			else if ($login != $data['login']) {
				$_SESSION['alert'] = "MAUVAIS LOGIN!";
			}
		}
	}
	else {
		$_SESSION['alert'] = "VEUILLEZ REMPLIR TOUS LES CHAMPS!";
	}
	header('Location: index.php');
?>