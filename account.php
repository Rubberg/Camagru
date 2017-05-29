<?php
$pageTitle = "Camagru - Compte";
session_start();

?>

<!DOCTYPE html>
<html>

<?php
	include_once('head.php');
?>

	<body>

	<?php include_once('nav.php'); ?>

		<main class="wrapper">

			<div id="modif">
				<h2>Modifer votre mot de passe: </h2>
				<form class="formm" method="post" action="modif_pw.php">
					<label for="email">Email</label>
					<input type="text" id="email" name="email" value="" placeholder="adresse@email.com" required>
					<label for="login">Login</label>
					<input type="text" id="login" name="login" value="" required>
					<label for="password">Ancien Mot De Passe</label>
					<input type="password" id="password" name="oldpwd" value="" required>
					<label for="password">Nouveau Mot De Passe</label>
					<input type="password" id="password" name="newpwd" value="" required>
					<button type="submit" id="submit" name="submit" value="">Changer de mot de passe</button>
				</form>
			</div>

			<div id="modif">
				<h2>Modifer votre email: </h2>
				<form class="formm" method="post" action="modif_email.php">
					<label for="email">Ancien Email</label>
					<input type="email" id="email" name="oldemail" value="" placeholder="adresse@email.com" required>
					<label for="email">Nouvel Email</label>
					<input type="email" id="email" name="newemail" value="" placeholder="adresse@email.com" required>
					<label for="login">Login</label>
					<input type="text" id="login" name="login" value="" required>
					<label for="password">Mot De Passe</label>
					<input type="password" id="password" name="password" value="" required>
					<button type="submit" id="submit" name="submit" value="">Changer d'adresse email</button>
				</form>
			</div>

			<p id="alert" name="alert"><?php echo $_SESSION['alert'];unset($_SESSION['alert']);?></p>

		</main>

		<?php include_once('footer.php'); ?>
		
	</body>
</html>