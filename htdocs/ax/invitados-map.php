<?php
include_once("../includes/config.php");
$bd = conectar();
// set ok
$sql = "select i.id, i.usuario, i.mail from invitados as i left join usuarios as u on i.usuario = u.id where i.ok = '0' and u.activo = '2'";
$res = $bd->query($sql);
$ok = array();
while($fila = $res->fetch_assoc()) {
	if(in_array($fila["mail"], $ok)) {
		$sql = "update invitados set ok = '1' where id = ".$fila["id"];
		$bd->query($sql);
	}
	else {
		$sql2 = "select id from usuarios where mail = '".$fila["mail"]."' and activo = '2'";
		$res2 = $bd->query($sql2);
		if($res2->num_rows > 0) {
			$ok[] = $fila["mail"];
			$sql = "update invitados set ok = '1' where id = ".$fila["id"];
			$bd->query($sql);
		}
	}
}
// set confianza y map
$sql = "select distinct i.usuario from invitados as i left join usuarios as u on i.usuario = u.id where i.ok = '1' and i.map = '0' and u.activo = '2'";
$res = $bd->query($sql);
$usuarios = array();
while($fila = $res->fetch_assoc())
	$usuarios[] = $fila["usuario"];
foreach ($usuarios as $u) {
	$sql = "select id from invitados where usuario = $u and ok = '1'";
	$res = $bd->query($sql);
	$n = $res->num_rows;
	$sql = "update confianza set confianza = $n where usuario = $u";
	$bd->query($sql);
	$sql = "update invitados set map = '1' where usuario = $u and ok = '1'";
	$bd->query($sql);
}
?>