<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$userid = $bd->real_escape_string($_POST["id"]);
$sql = "select id, activo from usuarios where fb_id = $userid";
$res = $bd->query($sql);
if($res->num_rows == 1) {
	$fila = $res->fetch_assoc();
	// si existe en la bbdd y activo: logueo
	if($fila["activo"] == "2") {
		$_SESSION[SesionId] = $fila["id"];
		$_SESSION[SesionNivel] = 0;
		$_SESSION[SesionExterno] = 1;
		$data["estado"] = "ok";
	}
	// no activo
	else if($fila["activo"] == "1") {
		$_SESSION[SesionTmp] = "ex".$fila["id"];
		$data["estado"] = "activar";
		$data["id"] = $fila["id"];
	}
	else
		$data["estado"] = "error";
}
else {
	include_once("../includes/facebook.php");
	$facebook = new Facebook(array(
		'appId'  => '511297335556303',
		'secret' => '574a1a675f22239be84c587f9dda88e6',
	));
	$user = $facebook->getUser();
	if($user) {
		try {
			$user_profile = $facebook->api('/me', 'GET');
			$fql = "SELECT first_name, last_name, email from user where uid = $user";
	        $ret_obj = $facebook->api(array('method' => 'fql.query', 'query' => $fql));

	        if($ret_obj[0]['email'] == "") {
	        	$data["estado"] = "mail";
	        	echo json_encode($data);
	        	exit;
	        }

 	        $sql = "select id, dni, activo from usuarios where mail = '".$ret_obj[0]['email']."' and activo != '0'";
	        $res = $bd->query($sql);
	        if($res->num_rows == 1) {
	        	// mail ya registrado (por login comun o linkedIn)
	        	$fila = $res->fetch_assoc();
	        	$nid = $fila["id"];
	        	$sql = "update usuarios set fb_id = $user where id = $nid";
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
	        	$sql = "insert into usuarios (nombre, apellido, mail, fb_id, fecha, aviso, aviso_np, aviso_rech, aviso_ca, aviso_cal, aviso_pr, aviso_res, aviso_pv, aviso_ve, aviso_inv, aviso_bal, activo) values ('".$ret_obj[0]['first_name']."', '".$ret_obj[0]['last_name']."', '".$ret_obj[0]['email']."', $user, '".date("Y-m-d H:i:s")."', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1')";
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
		catch (FacebookApiException $e) {
		    $data["result"] = $e->getResult();
		    $data["type"] = $e->getType();
		    $data["estado"] = "error catched";
		}
	}
	else
		$data["estado"] = "error";
}
echo json_encode($data);
?>