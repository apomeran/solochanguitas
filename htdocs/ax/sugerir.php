<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
if(!isset($_POST["s"]))
	exit;
$bd = conectar();
include_once("../class/funciones.php");
$f = new Funciones();
$sugerencia = $bd->real_escape_string($f->convertirCarga($_POST["s"], ""));
$sql = "insert into sugerencias (usuario, sugerencia, fecha) values (".$_SESSION[SesionId].", '$sugerencia', '".date("Y-m-d H:i:s")."')";
$bd->query($sql);
?>