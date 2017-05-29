<?php
$pageTitle = "Camagru - Accueil";
session_start();

?>

<!DOCTYPE html>
<html>
	<?php
	include_once('head.php');
	include_once('config/setup.php');
	?>

	<body>

		<?php include_once('nav.php'); ?>

	
		<main class="wrapper">

	<?php if (!$_SESSION['login']) {
	?>
			<div id="connect">
				<h2>Se connecter: </h2>
				<form class="formm"  method="post" action="login.php">
					<label for="login">Login</label>
					<input type="text" id="login" name="login" value="" required>
					<label for="password">Mot De Passe</label>
					<input type="password" id="password" name="password" value="" required>
					<button type="submit" id="submit" name="submit" value="">Se connecter</button>
				</form>
			</div>

			<div id="create">
				<h2>Créer un compte: </h2>
				<form class="formm" method="post" action="create.php">
					<label for="email">Email</label>
					<input type="email" id="email" name="email" value="" placeholder="adresse@email.com" required>
					<label for="login">Login</label>
					<input type="text" id="login" name="login" value="" required>
					<label for="password">Mot De Passe</label>
					<input type="password" id="password" name="password" value="" required>
					<button type="submit" id="submit" name="submit" value="">Créer un compte</button>
				</form>
			</div>

			<div id="forgotten">
				<h2>Mot de passe oublié? </h2>
				<form class="formm"  method="post" action="forgottenpw.php">
					<label for="login">Login</label>
					<input type="text" id="login" name="login" value="" required>
					<button type="submit" id="submit" name="submit" value="">Envoyer un mail de réinitialisation de mot de passe</button>
				</form>
			</div>


	<?php
		}
		else {
	?>
			<fieldset class="select">
				<legend>Choisissez un décor : </legend>
					<form class="scarselect" method="post" action="index.php">
				<?php
					$i = 1;
					while ($i < 5) {
				?>		<div class="filter">
							<img class="scar" src="filters/filtre<?php echo $i;?>.png">
							<input id='<?php echo $i;?>' type="radio" name="select" value="" onclick="photoenable();" ></input>
						</div>
				<?php	$i++;
					}
				?>


					</form>
			</fieldset>
					


				

			<fieldset id="vidpic" class='vidpic'>
				<legend>Webcam</legend>
			    	<video id="video" autoplay ></video>
					<img id="filtoncam" src=""/>
					<br />
				<button id="takephoto" onclick="takephoto();" disabled >Prendre une photo</button>
				<form id='upload' method='post' action='img_mix.php' enctype='multipart/form-data'>
					<input type='hidden' name='filter' id='filter' value=''/>
					<input type="hidden" name="effect" id="effect" value='' />
					<p>Pas de webcam?</p>
					<label for="uppic">Fichier PNG de 1 Mo max: </label><br />
     				<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
     				<input type="file" name="uppic" id="uppic" /><br/>
     				<input type="submit" id='sentuppic' name="sentuppic" value="Envoyer" disabled/>
				</form>
			</fieldset>



			<fieldset class='vidpic'>
				<legend>Photo</legend>
				 <?php
				 if (!$_SESSION['picpath']) { ?>
				<canvas id="canvas" width="640" height="480"  ></canvas>
				 <?php
				 }
				 else { ?>
				<img id='insight' src='<?php echo $_SESSION['picpath'];unset($_SESSION['picpath']);?>'/>
				<form class="formm" id="savepic" method="post" action="save_pict.php">
					<input type="submit" id="sentpict" name="sentPict" value="Prendre une autre photo!">
				</form> 
				 <?php
				 } ?>
			</fieldset>

			<fieldset class='minigallery'>
				<legend>Dernières photos:</legend>

				<?php
						try {
							$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
							$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
						}
						catch(PDOException $e) {
	   						echo $e->getMessage();
						}

						$req = $db->prepare('SELECT * FROM photos ORDER BY id DESC LIMIT 12');
						$req->execute();
						while ($data = $req->fetch()) {
				?>		<img class='minipic' src=<?php echo $data['picpath']; ?>>
				<?php 	}

						$req->closeCursor(); ?>
			</fieldset>


			<script type="text/javascript">
				var video = document.getElementById('video');
				var canvas = document.getElementById('canvas');
				var pict = document.getElementById('insight');
				var style = document.getElementById('effect');
				var filtoncam = document.getElementById('filtoncam');

				

				navigator.gUM = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
				navigator.gUM({ video: true},
						function(stream) {
						video.src = window.URL.createObjectURL(stream);
						},
						function(e) {
						console.log("Failed!", e);
						});
				
				var idx = 0;
				var filters = ['grayscale', 'sepia', 'blur', 'invert', ''];

				function changeFilter(e) {
			  		var el = e.target;
			    	el.className = '';
				    filtoncam.className = '';
				  	var effect = filters[idx++ % filters.length];
				    if (effect) {
						el.classList.add(effect);
						style.setAttribute('value', effect);
						filtoncam.classList.add(effect);
					}
				}
				video.addEventListener('click', changeFilter, false);



				function takephoto() {
					var i = 1;
					var check = 0;
					while (i < 5) {
						if (document.getElementById(i).checked != "") {
							check = 1;
							var filter = i;
							if (style.value != '') {
							 	var effect = style.getAttribute('value');
							}
						}
						i++;
					}
					if (check == 1 && canvas) {
						canvas.getContext("2d").drawImage(video, 0, 0, 640, 480);
						var data = canvas.toDataURL('image/png');
						canvas.getContext("2d").setTransform(1, 0, 0, 1, 0, 0);
						canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

						var request = new XMLHttpRequest();
						 request.onreadystatechange = function() {
         					if (request.readyState == 4 && request.status == 200) {
             					window.location.reload();
							}
    		 			};
	   					request.open('POST', 'img_mix.php', true);
	   					request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	   					request.send("img=" + data + "&filter=filters/filtre" + filter + ".png&effect=" + effect +"");
					}
					else if (check == 0) {
						document.getElementById('takephoto').disabled = true;
						document.getElementById('sentuppic').disabled = true;
					}

				}

				function photoenable() {
					var i = 1;
					while (i < 5) {
						if (document.getElementById(i).checked != "") {
							document.getElementById('takephoto').disabled = false;
							document.getElementById('sentuppic').disabled = false;
							document.getElementById('filter').setAttribute("value", "filters/filtre" + i + ".png");
							document.getElementById('filtoncam').setAttribute("src", "filters/filtre" + i + ".png");
							document.getElementById('filtoncam').setAttribute("type", "");

						}
						i++;
					}
				}
			</script>

	<?php } ?>


		</main>

			<p id="alert" name="alert"><?php if (!isset($_SESSION['alert']))
												$_SESSION['alert'] = NULL;
											echo $_SESSION['alert'];unset($_SESSION['alert']);?></p>

		<?php include_once('footer.php');?>


	</body>


</html>
