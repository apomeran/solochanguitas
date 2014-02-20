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
$sql = "update changuitas set estado = '2', fecha_fin = '".date("Y-m-d H:i:s")."' where id = $id and activo = '1' and estado = '1'";
$res = $bd->query($sql);
if($res && $bd->affected_rows == 1) {
	$sql = "select usuario, contratado, titulo from changuitas where id = $id";
	$res = $bd->query($sql);
	$fila = $res->fetch_assoc();
	$titCh = $fila["titulo"];
	$tipo = 4;
	$sql = "insert into mensajes (usuario, changuita, tipo, fecha) values (".$fila["contratado"].", $id, $tipo, '".date("Y-m-d H:i:s")."')";
	$bd->query($sql);
	$sql = "insert into mensajes (usuario, changuita, tipo, fecha) values (".$fila["usuario"].", $id, $tipo, '".date("Y-m-d H:i:s")."')";
	$bd->query($sql);
    // mail a los 2
    $enviarMail = array();
    $sql = "select nombre, mail from usuarios where id = ".$fila["usuario"];
    $res = $bd->query($sql);
    $fila2 = $res->fetch_assoc();
    $enviarMail[$fila2["mail"]] = $fila2["nombre"];
	$sql = "select nombre, mail from usuarios where id = ".$fila["contratado"];
	$res = $bd->query($sql);
	$fila2 = $res->fetch_assoc();
	$enviarMail[$fila2["mail"]] = $fila2["nombre"];
	$m = new PHPMailer;
    $m->isHTML(true);
    $m->From = MailFrom;
    $m->FromName = MailFromName;
    $m->Subject = "Changuita finalizada";
    $m->Timeout = 30;
    $m->CharSet = "UTF-8";
    foreach ($enviarMail as $k => $v) {
        $m->Body = $mailBodyIni."Estimado/a ".htmlentities($v).":<br/>La changuita <strong>".htmlentities($titCh)."</strong> fue dada por terminada. No te olvides de calificar a la otra parte.".$mailBodyFin;
        $m->ClearAddresses();
        $m->AddAddress($k);
        $m->Send();
    }
}
?>