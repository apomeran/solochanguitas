<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../includes/PasswordHash.php");
$bd = conectar();
$id = $_SESSION[SesionTmp]*-1;
unset($_SESSION[SesionTmp]);
$clave = $bd->real_escape_string($_POST["nueva-clave"]);
$i = HashCost;
$data["estado"] = "error";
$ahora = date("Y-m-d H:i:s");
$t_hasher = new PasswordHash(HashCost, FALSE);
$sql = "update usuarios set clave = '".$t_hasher->HashPassword($clave)."', recuperacion = '".$ahora."' where id = $id";
if($bd->query($sql)) {
	$_SESSION[SesionId] = $id;
	$_SESSION[SesionNivel] = 0;
	$_SESSION[SesionExterno] = 0;
	$data["estado"] = "ok";
}
else
	$data["estado"] = "error db";
echo json_encode($data);
?>