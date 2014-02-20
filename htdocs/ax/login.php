<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../includes/PasswordHash.php");
$bd = conectar();
$user = $bd->real_escape_string($_POST["usuario"]);
$clave = $bd->real_escape_string($_POST["clave"]);
$i = HashCost;
$data["estado"] = "error";
$sql = "select id, clave, nivel from usuarios where activo = '2' and mail = '$user'";
$res = $bd->query($sql);
if($res->num_rows == 1) {
	$fila = $res->fetch_assoc();
	if($fila["nivel"] == '1')
		$i = HashCost*2;
	$t_hasher = new PasswordHash(HashCost, FALSE);
	$check = $t_hasher->CheckPassword($clave, $fila["clave"]);
	if($check) {
		$_SESSION[SesionId] = $fila["id"];
		$_SESSION[SesionNivel] = $fila["nivel"];
		$_SESSION[SesionExterno] = 0;
		if(isset($_SESSION[SesionTmp]))
			unset($_SESSION[SesionTmp]);
		$data["estado"] = "ok";
	}
	else
		$data["estado"] = "error clave";
}
else
	$data["estado"] = "no existe";
echo json_encode($data);
?>