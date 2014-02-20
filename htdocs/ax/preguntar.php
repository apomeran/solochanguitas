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
$pregunta = $f->filtrarTxt($bd->real_escape_string($f->convertirCarga($_POST["pregunta"], "")));
$sql = "insert into preguntas (usuario, changuita, pregunta, pregunta_fecha) values (".$_SESSION[SesionId].", $id, '$pregunta', '".date("Y-m-d H:i:s")."')";
if($bd->query($sql)) {
	$nid = $bd->insert_id;
    $data["estado"] = "ok";
    include_once("../class/notificaciones.php");
    $not = new Notificaciones();
    $not->preguntar($id, $nid);
    include_once("../class/mails.php");
    $mail = new Mails();
    $mail->preguntar($id, $nid);
}
echo json_encode($data);
?>