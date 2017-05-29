<?php
	if (!isset($_SESSION['login']))
		$_SESSION['login'] = NULL;
	if (!isset($_SESSION['alert']))
		$_SESSION['alert'] = NULL;
	if (!isset($_POST['login']))
		$_POST['login'] = NULL;
	if (!isset($_POST['email']))
		$_POST['email'] = NULL;
	if (!isset($_POST['password']))
		$_POST['password'] = NULL;
	if (!isset($_SESSION['picpath']))
		$_SESSION['picpath'] = NULL;
	if (!isset($_POST['password']))
		$_POST['password'] = NULL;
	if (!isset($_POST['password']))
		$_POST['password'] = NULL;
	if ($_SESSION['login']) {
?>
	<nav>
		<div class="index"><a href="index.php">CAMAGRU</a></div>
		<ul class="menu">
			<li class="galery"><a href="gallery.php"> Galerie photos</a></li>
			<li class="account"><a href="account.php"> Compte</a></li>
			<li class="logout"><a href="logout.php"> Se Déconnecter</a></li>
		</ul>
	</nav>
<?php  }
	else { 
?> 	<nav id="nolog">
		<div class="index"><a href="gallery.php">CAMAGRU</a></div>
	</nav>
<?php }  ?>