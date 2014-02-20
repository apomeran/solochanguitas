<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
if(!isset($_POST["id"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("../includes/class.phpmailer.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$sql = "update changuitas set activo = '0' where id = $id and activo = '1' and estado = '0'";
$res = $bd->query($sql);
// notificaciones
include_once("../class/notificaciones.php");
$not = new Notificaciones();
$not->reset($id);
//$not->rechazar($id);
// mails
// include_once("../class/mails.php");
// $mail = new Mails();
// $mail->rechazar($id);
?>