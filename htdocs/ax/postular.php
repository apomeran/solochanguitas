<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$data["estado"] = "";
if(!isset($_POST["id"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("../includes/class.phpmailer.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$sql = "select id from postulaciones where changuita = $id and usuario = ".$_SESSION[SesionId];
$res = $bd->query($sql);
if($res->num_rows == 0) {
	$sql = "insert into postulaciones (usuario, changuita, fecha) values (".$_SESSION[SesionId].", $id, '".date("Y-m-d H:i:s")."')";
	if($bd->query($sql)) {
		$nid = $bd->insert_id;
		$data["estado"] = "ok";
		include_once("../class/notificaciones.php");
    	$not = new Notificaciones();
    	$not->postular($id, $nid);
    	include_once("../class/mails.php");
    	$mail = new Mails();
    	$mail->postular($id);
	}
}
echo json_encode($data);
?>