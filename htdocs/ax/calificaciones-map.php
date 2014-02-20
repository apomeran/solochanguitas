<?php
include_once("../includes/config.php");
$bd = conectar();
$sql = "select id, usuario from calificaciones where map = '0'";
$res = $bd->query($sql);
$usuario = array();
$ids = array();
while($fila = $res->fetch_assoc()) {
	$usuario[] = $fila["usuario"];
	$ids[] = $fila["id"];
}
foreach ($usuario as $u) {
	$sql = "select calificacion from calificaciones where usuario = $u and activo = '1'";
	$res = $bd->query($sql);
	$calificacion = 0;
	$n = 0;
	while($fila = $res->fetch_assoc()) {
		$calificacion += $fila["calificacion"]-1;
		$n++;
	}
	$valor = round($calificacion/$n);
	if($valor < 0)
		$valor = 0;
	else if($valor > 0)
		$valor = 2;
	else
		$valor = 1;
	$sql = "update calificacion set calificacion = $valor, n = $n where usuario = $u";
	$bd->query($sql);
}
foreach ($ids as $v) {
	$sql = "update calificaciones set map = '1' where id = $v";
	$bd->query($sql);
}
?>