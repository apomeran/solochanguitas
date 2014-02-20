<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$bd = conectar();
$sql = "select id from mensajes where leido = '0' and usuario = ".$_SESSION[SesionId];
$res = $bd->query($sql);
$nM = $res->num_rows;
$classM = "";
$mensajesTxt = "<i class='icon-bell icon-white'></i>";
if($nM > 0) {
	$classM = "badge-warning";
    $mensajesTxt = $nM;
}
$data["html"] = $mensajesTxt;
$data["estiloSpan"] = $classM;
echo json_encode($data);
?>