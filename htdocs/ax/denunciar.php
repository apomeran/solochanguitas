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
include_once("../class/funciones.php");
$f = new Funciones();
$id = $bd->real_escape_string($_POST["id"]);
$t = $bd->real_escape_string($_POST["tipo"]);
$comentario = $bd->real_escape_string($f->convertirCarga($_POST["comentario"], ""));
$sql = "insert into denuncias (usuario, tipo, i, comentario, fecha) values (".$_SESSION[SesionId].", '$t', $id, '$comentario', '".date("Y-m-d H:i:s")."')";
$bd->query($sql);
/*
if($bd->query($sql)) {
	switch($t) {
		case "ch":
			$tipo = 3;
			$sql = "select usuario from changuitas where id = $id";
			break;
		case "p":
			$tipo = 4;
			$sql = "select usuario from preguntas where id = $id";
			break;
		case "r":
			$tipo = 5;
			$sql = "select ch.usuario from preguntas as pre left join changuitas as ch on pre.changuita = ch.id where pre.id = $id";
			break;
	}
	$res = $bd->query($sql);
	$fila = $res->fetch_assoc();
	$usuario = $fila["usuario"];
	$sql = "insert into mensajes (usuario, changuita, tipo, fecha) values ($usuario, $id, $tipo, '".date("Y-m-d H:i:s")."')";
	$bd->query($sql);
}
*/
?>