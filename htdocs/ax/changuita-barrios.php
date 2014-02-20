<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$data["estado"] = "ok";
$data["html"] = "<option value='0'>--- elegir ---</option>";
$sql = "select id, barrio from barrios where localidad = $id and activo = '1' order by barrio asc";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc())
	$data["html"] .= "<option value='".$fila["id"]."'>".$fila["barrio"]."</option>";
echo json_encode($data);
?>