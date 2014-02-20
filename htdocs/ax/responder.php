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
include_once("../class/funciones.php");
$f = new Funciones();
$id = $bd->real_escape_string($_POST["id"]);
$respuesta = $f->filtrarTxt($bd->real_escape_string($f->convertirCarga($_POST["respuesta"], "")));
$sql = "update preguntas set respuesta = '$respuesta', respuesta_fecha = '".date("Y-m-d H:i:s")."' where id = $id and respuesta = ''";
$res = $bd->query($sql);
if($res && $bd->affected_rows == 1) {
    $data["estado"] = "ok";
    include_once("../class/notificaciones.php");
    $not = new Notificaciones();
    $not->responder($id);
    include_once("../class/mails.php");
    $mail = new Mails();
    $mail->responder($id);
}
echo json_encode($data);
?>