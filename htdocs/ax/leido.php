<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$bd = conectar();
$sql = "update mensajes set leido = '1' where usuario = ".$_SESSION[SesionId];
$bd->query($sql);
?>