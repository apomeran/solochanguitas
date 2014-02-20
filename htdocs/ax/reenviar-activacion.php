<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../includes/class.phpmailer.php");
$bd = conectar();
$id = $bd->real_escape_string($_SESSION[SesionTmp]);
// mail activacion
include_once("../class/mails.php");
$mail = new Mails();
$mail->reactivar($id);
unset($_SESSION[SesionTmp]);
?>