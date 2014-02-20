<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$data["html"] = "";
$data["nombre"] = "";
if(!isset($_POST["id"]))
	exit;
include_once("../class/funciones.php");
$f = new Funciones();
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);

$sql = "select nombre, apellido from usuarios where id = $id";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();

$data["nombre"] .= $fila["nombre"]." ".substr($fila["apellido"], 0, 1).".";
$sql = "select calificacion, comentario from calificaciones where activo = '1' and usuario = $id order by fecha desc";
$res = $bd->query($sql);
$total = $res->num_rows;
$data["html"] .= "<p>Calificaciones recibidas: <strong>$total</strong></p>";
while($fila = $res->fetch_assoc())
	$data["html"] .= "<div class='detalle-calificaciones'><div class='indicador'>".$f->indicador($fila["calificacion"], "calificacion")."</div><div class='comentario'>".$fila["comentario"]."</div><div class='clearfix'></div></div>";
echo json_encode($data);
?>