<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$data["estado"] = "ok";
$data["html"] = "<option value='0'>--- elegir ---</option>";
$sql = "select id, subcategoria from subcategorias where categoria = $id and activo = '1' order by orden asc, subcategoria asc";
$res = $bd->query($sql);
if($res->num_rows > 0) {
	while($fila = $res->fetch_assoc())
		$data["html"] .= "<option value='".$fila["id"]."'>".$fila["subcategoria"]."</option>";
}
else
	$data["html"] = "<option value='0'>No hay subcategor&iacute;as</option>";
echo json_encode($data);
?>