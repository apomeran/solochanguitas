<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$sql = "select hora from subcategorias where id = $id and activo = '1'";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
if($fila["hora"] > 0)
	echo "Precio m&iacute;nimo sugerido por hora, sin incluir traslados: $".str_replace(".", ",", $fila["hora"]);
else
	echo "";
?>