<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("includes/config.php");
if(!isset($_GET["c"])) {
	header("Location:error.php");
	exit;
}
$bd = conectar();
$c = $bd->real_escape_string($_GET["c"]);
$ex = explode("|", $c, 2);
if(count($ex) != 2) {
	header("Location:error-activacion.php");
	exit;
}
$id = $ex[0];
$hash = $ex[1];
if(!is_numeric($id)) {
	header("Location:error.php");
	exit;
}
$sql = "select fecha, mail, activo from usuarios where id = $id";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
if($res->num_rows == 0) {
	header("Location:error.php");
	exit;
}
if($fila["activo"] == "0") {
	header("Location:error-activacion.php");
	exit;
}
if($fila["activo"] == "2") {
	if($_SESSION[SesionId] == $id)
		header("Location:index.php");
	else
		header("Location:error-activo.php");
	exit;
}
if($hash !== sha1($fila["fecha"].$id.SalAct)) {
	header("Location:error-activacion.php");
	exit;
}
$sql = "update usuarios set activo = '2' where id = $id";
$bd->query($sql);
$_SESSION[SesionId] = $id;
$_SESSION[SesionNivel] = 0;
$_SESSION[SesionExterno] = 0;
unset($_SESSION[SesionTmp]);
// calificacion y confianza
$sql = "insert into calificacion (usuario, calificacion, n) values ($id,0,0)";
$bd->query($sql);
$sql = "insert into confianza (usuario, confianza) values ($id,0)";
$bd->query($sql);
// invitados
include_once("ax/invitados-map.php");

header("Location:index.php");
?>