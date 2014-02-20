<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$data["estado"] = "";
if(!isset($_POST["ch"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("../includes/class.phpmailer.php");
$bd = conectar();
$ch = $bd->real_escape_string($_POST["ch"]);
$u = $bd->real_escape_string($_POST["u"]);
$sql = "update changuitas set estado = '1', contratado = $u, fecha_contratacion = '".date("Y-m-d H:i:s")."' where id = $ch and estado = '0' and usuario != $u and activo = '1'";
$res = $bd->query($sql);
if($res && $bd->affected_rows == 1) {
	$data["estado"] = "ok";
    include_once("../class/notificaciones.php");
    $not = new Notificaciones();
    $not->contratar($ch, $u);
    //$not->rechazar($ch);
    // mails
    include_once("../class/mails.php");
    $mail = new Mails();
    //$mail->rechazar($ch);
    $mail->contratar($ch);
}
echo json_encode($data);
?>