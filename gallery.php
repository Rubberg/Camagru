<?php
session_start();
$pageTitle = "Camagru - Gallerie Photos";
include_once('config/database.php');
?>

<!DOCTYPE html>
<html>
	<?php
	include_once('head.php');
	include_once('config/setup.php');
	?>

	<body>

		<?php if ($_SESSION['login'])
			include_once('nav.php');
		else {
		?>
		<nav id="nolog">
			<div class="index"><a href="index.php">CAMAGRU</a></div>
		</nav>
		<?php } ?>

		<main class="wrapper">

			<div class="gallery">
				
				<?php
					try {
						$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
						$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					}
					catch(PDOException $e) {
   						echo $e->getMessage();
					}

					$picsperpage = 10;
					$req0 = $db->prepare('SELECT COUNT(*) AS totalpics FROM photos');
					$req0->execute();
					$data0 = $req0->fetch();
					$totalpics = $data0['totalpics'];
					$totalpages = ceil($totalpics / $picsperpage);

					echo '<p id="pages">PAGE : ';
					$i = 1;
					while ($i <= $totalpages) {
						echo ' <a href="gallery.php?page=' .$i. '">' .$i. '</a> ';
						if ($i != $totalpages)
							echo '-';
						if ($i == $totalpages)
							echo '</p>';
						$i++;
					}
					echo '<br/>';

					if (isset($_GET['page']) AND preg_match("/^[0-9]+$/", $_GET['page'])) {
						$page = $_GET['page'];
					}
					else {
						$_GET['page'] = 1;
						$page = 1;
					}

					$firstpic = ($page - 1) * $picsperpage;
					$req = $db->prepare('SELECT * FROM photos ORDER BY id DESC LIMIT ' .$firstpic. ',' .$picsperpage. '');
					$req->execute();
					
					while ($data = $req->fetch()) {
					$req3 = $db->prepare('SELECT COUNT(*) AS nbr FROM likes WHERE pic_id = ?');
					$req3->execute(array($data['id']));
					$nbr = $req3->fetch();
					$req4 = $db->prepare('SELECT COUNT(*) AS ilike FROM likes WHERE pic_id = ? AND login = ?');
					$req4->execute(array($data['id'], $_SESSION['login']));
					$ilike = $req4->fetch();
				?>		<fieldset class="galleryelem">
							<legend id="pictitle" name="pictitle"><?php echo "Photo de " .$data['login'];?></legend>
							<img class='gallerypic' src=<?php echo $data['picpath']; ?>>

				<?php if ($_SESSION['login']) { ?>

							<form id='like' method='post' action="like.php">
								<input type='hidden' name="picid" value=<?php echo $data['id'];?>></input>
				
				<?php
					if ((($nbr['nbr'] == 0) OR ($nbr['nbr'] == '')) AND (($ilike['ilike'] == 0) OR ($ilike['ilike'] == ''))) {
				?>

								<input id="coeur" type="image" src="filters/coeur.png" border=0 name="submit" value="submit">Aimez-vous cette photo?</input>
				<?php
					}
					else if ($nbr['nbr'] > 0 AND $ilike['ilike'] == 0) {
				?>
								<input id="coeur" type="image" src="filters/coeur.png" border=0 name="submit" value="submit"><?php echo $nbr['nbr']?> personne(s) aime(nt) cette photo, et vous?</input>


				<?php
					}
					else if ((($nbr['nbr'] == 0) || ($nbr['nbr'] == 1) OR ($nbr['nbr'] == '')) AND ($ilike['ilike'] != 0)) {
				?>
								<input id="coeur" type="image" src="filters/coeur.png" border=0 name="submit" value="submit">Vous aimez cette photo!</input>
				<?php
					}
					else if (($nbr['nbr'] > 1) AND ($ilike['ilike'] != 0)) {
						$nbr['nbr']--; 
				?>
								<input id="coeur" type="image" src="filters/coeur.png" border=0 name="submit" value="submit">Vous et <?php echo $nbr['nbr']?> personne(s) aimez cette photo!</input>
				<?php
					}
				?>
							</form>
				<?php
				if ($_SESSION['login'] == $data['login']) {
				?>			
							<form id="delpicform" class="formm" method='post' action="delpic.php">
								<input type='hidden' name="picid" value=<?php echo $data['id'];?>></input>				
								<input type='hidden' name="picpath" value=<?php echo $data['picpath'];?>></input>

								<input id="delpic" type="submit" name="submit" value="Supprimer la photo"></input>
							</form>
				
				<?php
					}
				?>

							<form id="comment" class="formm" method='post' action="comment.php">
								<input type='hidden' name="picid" value=<?php echo $data['id'];?>></input>
								<input class="comtextinput" type='text' name="comment" placeholder="Commentez cette photo!" value=""></input>
								<input id="sendcom" type="submit" name="submit" value="Valider le commentaire"></input>
							</form>

							<div class="comlist">
								<?php
									
									$req2 = $db->prepare('SELECT * FROM comments WHERE pic_id = ?');
									$req2->execute(array($data['id']));

									while ($data2 = $req2->fetch()) {
								?>		<hr/>	
										<p>Commentaire de <?php echo $data2['login']; ?>:</p>
										<p class="comtext"> <?php echo $data2['text']; ?> </p>
							<?php  	}
									$req2->closeCursor();
								?>

							</div>

				<?php
				}
				?>


						</fieldset> 
			<?php
					$req3->closeCursor();
					$req4->closeCursor();
				}	$req->closeCursor();
				?>

			</div>

		</main>

		<p id="alert" name="alert"><?php if (!isset($_SESSION['alert']))
											$_SESSION['alert'] = NULL;
										echo $_SESSION['alert'];unset($_SESSION['alert']);?></p>
		<?php include_once('footer.php'); ?>


	</body>


</html>
