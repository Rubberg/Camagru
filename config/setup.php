<?php
	
include_once('database.php');

try
{
	$db = new PDO('mysql:host=localhost;charset=utf8', $DB_USER, $DB_PASSWORD);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
catch(Exception $e)
{
	die('Erreur : '.$e->getMessage());
	print_r($e);
}

$dbname = 'camagru';
$dbname = substr($db->quote($dbname), 1, -1);
$req = $db->prepare('CREATE DATABASE IF NOT EXISTS `' .$dbname. '`');
$req->execute(array('camagru' => $dbname));
$req->closeCursor();

try
{
	$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
catch(PDOException $e) {
    echo $e->getMessage();
}


$tabname = 'users';
$tabname = substr($db->quote($tabname), 1, -1);
$tabname1 = 'photos';
$tabname1 = substr($db->quote($tabname1), 1, -1);
$tabname2 = 'comments';
$tabname2 = substr($db->quote($tabname2), 1, -1);
$tabname3 = 'likes';
$tabname3 = substr($db->quote($tabname3), 1, -1);

$q = $db->prepare('CREATE TABLE IF NOT EXISTS `' .$tabname.  '`(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	login VARCHAR(20) NOT NULL,
	email VARCHAR(50) NOT NULL,
	password VARCHAR(256) NOT NULL,
	`key` VARCHAR(256) NOT NULL);');
$q->execute(array('users' => $tabname));

$q1 = $db->prepare('CREATE TABLE IF NOT EXISTS `' .$tabname1.  '`(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	login VARCHAR(20) NOT NULL,
	user_id INT(10) NOT NULL,
	picpath VARCHAR(256) NOT NULL);');
$q1->execute(array('photos' => $tabname1));


$q2 = $db->prepare('CREATE TABLE IF NOT EXISTS `' .$tabname2.  '`(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	login VARCHAR(20) NOT NULL,
	user_id INT(10) NOT NULL,
	text VARCHAR(256) NOT NULL,
	pic_id INT(10) NOT NULL);');
$q2->execute(array('comments' => $tabname2));

$q3 = $db->prepare('CREATE TABLE IF NOT EXISTS `' .$tabname3.  '`(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	login VARCHAR(20) NOT NULL,
	user_id INT(10) NOT NULL,
	pic_id INT(10) NOT NULL);');
$q3->execute(array('likes' => $tabname3));

$q->closeCursor();
$q1->closeCursor();
$q2->closeCursor();
$q3->closeCursor();

?>