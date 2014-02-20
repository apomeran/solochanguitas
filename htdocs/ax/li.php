<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$data["estado"] = "";
$userid = $_POST["res"]["values"][0]["id"];
$nombre = $_POST["res"]["values"][0]["firstName"];
$apellido = $_POST["res"]["values"][0]["lastName"];
$mail = $_POST["res"]["values"][0]["emailAddress"];
// si existe en la bbdd: logueo
$sql = "select id, activo from usuarios where li_id = '$userid'";
$res = $bd->query($sql);
if($res->num_rows == 1) {
	$fila = $res->fetch_assoc();
	if($fila["activo"] == "2") {
		$_SESSION[SesionId] = $fila["id"];
		$_SESSION[SesionNivel] = 0;
		$_SESSION[SesionExterno] = 1;
		$data["estado"] = "ok";
	}
	else if($fila["activo"] == "1") {
		$_SESSION[SesionTmp] = "ex".$fila["id"];
		$data["estado"] = "activar";
		$data["id"] = $fila["id"];
	}
	else
		$data["estado"] = "error";
}
else {
	$sql = "select id, dni, activo from usuarios where mail = '$mail' and activo != '0'";
	$res = $bd->query($sql);
	if($res->num_rows == 1) {
		// mail ya registrado (por login comun o fb)
		$fila = $res->fetch_assoc();
		$nid = $fila["id"];
		$sql = "update usuarios set li_id = '$userid' where id = $nid";
		$res = $bd->query($sql);
		if($fila["dni"] != "" && $fila["activo"] == "1") {
        	$sql = "update usuarios set activo = '2' where id = $nid";
        	$res = $bd->query($sql);
        	// login
	        $_SESSION[SesionId] = $nid;
			$_SESSION[SesionNivel] = 0;
			$_SESSION[SesionExterno] = 1;
			$data["estado"] = "ok";
        	if(isset($_SESSION[SesionTmp]))
				unset($_SESSION[SesionTmp]);
        }
        else if($fila["dni"] == "") {
        	$_SESSION[SesionTmp] = "ex".$nid;
			$data["estado"] = "activar";
			$data["id"] = $nid;
        }
	}
	else {
		$sql = "insert into usuarios (nombre, apellido, mail, li_id, fecha, aviso, aviso_np, aviso_rech, aviso_ca, aviso_cal, aviso_pr, aviso_res, aviso_pv, aviso_ve, aviso_inv, aviso_bal, activo) values ('$nombre', '$apellido', '$mail', '$userid', '".date("Y-m-d H:i:s")."', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1')";
		if($res = $bd->query($sql)) {
			$nid = $bd->insert_id;
			// calificacion y confianza
			$sql = "insert into calificacion (usuario, calificacion, n) values ($nid, 0, 0)";
			$bd->query($sql);
			$sql = "insert into confianza (usuario, confianza) values ($nid, 0)";
			$bd->query($sql);
			//
			$_SESSION[SesionTmp] = "ex".$nid;
			$data["estado"] = "activar";
			$data["id"] = $nid;
		}
		else
			$data["estado"] = "error";
	}
}
echo json_encode($data);
?>