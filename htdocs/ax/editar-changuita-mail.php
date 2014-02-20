<?php
include_once("../includes/config.php");
include_once("../class/seguridad.php");
include_once("../includes/class.phpmailer.php");
$s = new Seguridad();
$s->permitir(0);
if(!isset($_POST["id"]))
	$s->salir();
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
include_once("../class/mails.php");
$mail = new Mails();
$mail->nuevaChanguita($id);
?>