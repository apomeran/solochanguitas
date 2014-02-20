<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$data["estado"] = "ok";
$data["html"] = "<button class='btn btn-link' id='drop-barrios-todos'>Todos</button> | <button class='btn btn-link' id='drop-barrios-ninguno'>Ninguno</button> | <button class='btn btn-link'><em>Cerrar</em></button>";
$sql = "select id, barrio from barrios where localidad = $id and activo = '1' order by barrio asc";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc())
	$data["html"] .= "<label><input name='barrio[]' type='checkbox' value='".$fila["id"]."'/> ".$fila["barrio"]."</label>";
echo json_encode($data);
?>