<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../includes/class.phpmailer.php");
$bd = conectar();
$mail = $bd->real_escape_string(trim($_POST["usuario"]));
$sql = "select id from usuarios where mail = '$mail' and activo = '2'";
$res = $bd->query($sql);
if($res->num_rows == 1) {
	$fila = $res->fetch_assoc();
	include_once("../class/mails.php");
	$mail = new Mails();
	$mail->olvido($fila["id"]);
	$data["estado"] = "ok";
}
else
	$data["estado"] = "error";
echo json_encode($data);
?>