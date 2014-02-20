<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$data["estado"] = "ok";
$data["html"] = "";
$sql = "select id, subcategoria from subcategorias where categoria = $id and activo = '1' order by orden asc, subcategoria asc";
$res = $bd->query($sql);
if($res->num_rows > 0) {
	while($fila = $res->fetch_assoc())
		$data["html"] .= "<a data-subcat-id='".$fila["id"]."' href='#'>".$fila["subcategoria"]."</a>";
}
else
	$data["html"] = "No hay subcategor&iacute;as";
echo json_encode($data);
?>