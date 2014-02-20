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
	header("Location:error.php");
	exit;
}
$id = $ex[0];
$hash = $ex[1];
if(!is_numeric($id)) {
	header("Location:error.php");
	exit;
}
$sql = "select nombre, mail, recuperacion from usuarios where id = $id and activo = '2'";
$res = $bd->query($sql);
if($res->num_rows == 0) {
	header("Location:error.php");
	exit;
}
$fila = $res->fetch_assoc();
if($hash !== sha1($fila["recuperacion"].$id.SalRec)) {
	header("Location:error.php");
	exit;
}
$_SESSION[SesionTmp] = $id*-1;
header("Location:index.php");
?>