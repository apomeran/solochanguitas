<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
if(!isset($_POST["id"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$sql = "select pos.id from postulaciones as pos left join changuitas as ch on pos.changuita = ch.id where pos.changuita = $id and pos.usuario = ".$_SESSION[SesionId]." and ch.activo = '1' and ch.estado = '0'";
$res = $bd->query($sql);
if($res->num_rows == 1) {
	$fila = $res->fetch_assoc();
	$sql = "delete from postulaciones where id = ".$fila["id"];
	if($bd->query($sql)) {
		$sql = "select usuario from changuitas where id = $id";
		$res = $bd->query($sql);
		$fila2 = $res->fetch_assoc();
		$sql = "delete from mensajes where changuita = $id and usuario = ".$fila2["usuario"]." and tipo = 1 and leido = '0' and extra = ".$fila["id"];
		$bd->query($sql);
	}
}
?>