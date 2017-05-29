<?php
	session_start();
	include_once('config/database.php');


	if (($_FILES['uppic']['error'] == UPLOAD_ERR_FORM_SIZE)) {
		$_SESSION['alert'] = "LA TAILLE DU FICHIER DE VOTRE PHOTO EST TROP GRANDE (MAX. 1MO)";
		unset($_FILES['uppic']);
		header('Location: index.php');
		return;
	}

	if ($_FILES['uppic']['error'] > 0) {
		$_SESSION['alert'] = "UNE ERREUR EST SURVENUE LORS DU TRANSFERT DE VOTRE PHOTO";
		unset($_FILES['uppic']);
		header('Location: index.php');
		return;
	}

	 if ($_FILES['uppic']) {
		$array = getimagesize($_FILES['uppic']['tmp_name']);
		$type = $array[2];
		if ($type != 3) {
			$_SESSION['alert'] = "VOTRE PHOTO DOIT ÊTRE DE TYPE PNG!";
			header('Location: index.php');
			return;
		}
	}

	if (!file_exists('gallery/'))
		mkdir('gallery/');
	$picname = time();
	$picpath = 'gallery/' .$picname. '.png';

	if ($_POST['img']) {
		$img = htmlspecialchars($_POST['img']);
		$img = str_replace("data:image/png;base64,", "", $img);
		$img = str_replace(" ", "+", $img);
		$file = base64_decode($img);
		file_put_contents($picpath, $file);
	}
	else if ($_FILES) {
		$transfert = move_uploaded_file($_FILES['uppic']['tmp_name'], $picpath);
		if (!$transfert)
			$_SESSION['alert'] = "UN PROBLÈME EST SURVENU LORS DU TRANSFERT DE VOTRE PHOTO";
	}

	$file = imagecreatefrompng($picpath);

	
	$png = htmlspecialchars($_POST['filter']);
	$filter = imagecreatefrompng($png);

	$widthFile = imagesx($file);
	$heightFile = imagesy($file);
	$width_filter = imagesx($filter);
	$height_filter = imagesy($filter);
	$file_x = $widthFile / 2;
	$file_y = $heightFile * (10/100);
	imagecopy($file, $filter, $file_x, $file_y, 0, 0, $width_filter, $height_filter);

	if ($_POST['effect'] != '') {
		$effect = htmlspecialchars($_POST['effect']);
		if ($effect == "grayscale")
			imagefilter($file, IMG_FILTER_GRAYSCALE);
		if ($effect == "invert")
			imagefilter($file, IMG_FILTER_NEGATE);
		if ($effect == "sepia") {
			imagefilter($file, IMG_FILTER_GRAYSCALE);
			imagefilter($file, IMG_FILTER_COLORIZE, 50, 35, 5);
			imagefilter($file, IMG_FILTER_BRIGHTNESS, -30);
		}
		if ($effect == "blur") {
			$i = 0;
			while ($i < 40) {
				imagefilter($file, IMG_FILTER_GAUSSIAN_BLUR);
				$i++;
			}
			imagefilter($file, IMG_FILTER_BRIGHTNESS, 20);
		}
	}

	if (!file_exists('gallery/'))
		mkdir('gallery/');
	imagepng($file, $picpath);
	try {
	$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}
	catch(PDOException $e) {
	   	echo $e->getMessage();
	}

	$req = $db->prepare('INSERT INTO photos(login, user_id, picpath) VALUES (?, ?, ?)');
	$req->execute(array($_SESSION['login'], $_SESSION['userid'], $picpath));
	$req->closeCursor();
	$_SESSION['picpath'] = $picpath;
	header("Refresh:0; url=index.php");
?>  