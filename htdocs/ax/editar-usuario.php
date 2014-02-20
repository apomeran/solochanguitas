<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../class/seguridad.php");
include_once("../includes/PasswordHash.php");
$s = new Seguridad();
if(!isset($_POST["id"]))
	$s->salir();
if(!isset($_SESSION[SesionExterno]) || $_SESSION[SesionExterno] == 0) {
	if(!isset($_SESSION[SesionId]) && $_POST["id"] != 0)
		$s->salir();
	else if($_POST["id"] != 0 && $_POST["id"] != $_SESSION[SesionId] && $_SESSION[SesionNivel] == "0")
		$s->salir();
}
include_once("../class/funciones.php");
$f = new Funciones();
$columnas = array("mail", "clave", "nombre", "apellido", "sexo", "nacimiento", "localidad", "barrio", "celular_area", "celular", "educacion", "institucion", "presentacion", "aviso", "aviso_np", "aviso_rech", "aviso_ca", "aviso_pr", "aviso_res", "aviso_pv", "aviso_ve", "aviso_inv", "aviso_cal", "aviso_bal", "dni", "perfil_fb", "perfil_li", "perfil_gp");
$convertir = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
$avisosCheck = array("aviso_np", "aviso_rech", "aviso_ca", "aviso_pr", "aviso_res", "aviso_pv", "aviso_ve", "aviso_inv", "aviso_cal", "aviso_bal");
$col = array();
$val = array();
$ok = 0;
$bd = conectar();
foreach($columnas as $k => $v) {
	if(isset($_POST[$v])) {
		if($v == "clave") {
			if($_POST[$v] == "")
				continue;
			$col[] = $v;
			$t_hasher = new PasswordHash(HashCost, FALSE);
			$val[] = "'".$t_hasher->HashPassword($bd->real_escape_string(trim($_POST[$v])))."'";
		}
		else if($v == "perfil_fb" || $v == "perfil_li" || $v == "perfil_gp") {
			$col[] = $v;
			$valUrl = $bd->real_escape_string($_POST[$v]);
			if(strpos($valUrl, "http://") === 0)
				$valUrl = substr($valUrl, 7);
			else if(strpos($valUrl, "https://") === 0)
				$valUrl = substr($valUrl, 8);
			$val[] = "'".$valUrl."'";
		}
		else {
			$col[] = $v;
			$val[] = "'".$bd->real_escape_string($f->convertirCarga($_POST[$v], $convertir[$k]))."'";
		}
	}
	// else if($v == "nacimiento") {
	// 	if(isset($_POST["nacimiento_d"]) && isset($_POST["nacimiento_m"]) && isset($_POST["nacimiento_a"])) {
	// 		$col[] = $v;
	// 		$val[] = "'".$bd->real_escape_string($_POST["nacimiento_a"]."-".$_POST["nacimiento_m"]."-".$_POST["nacimiento_d"])."'";
	// 	}
	// }
	else if(in_array($v, $avisosCheck)) {
		$col[] = $v;
		$val[] = "'0'";
	}
}
$id = $bd->real_escape_string($_POST["id"]);
$mail = $bd->real_escape_string(trim($_POST["mail"]));
$dni = $bd->real_escape_string(trim($_POST["dni"]));
if($mail == "" || $dni == "") {
	$data["estado"] = "req";
	echo json_encode($data);
	exit;
}
// carga
if($id == 0) {
	$sql = "select id from usuarios where activo != '0' and mail = '$mail'";
	$res = $bd->query($sql);
	if($res->num_rows > 0)
		$data["estado"] = "existeMail";
	else {
		$sql = "select id from usuarios where activo != '0' and dni = $dni";
		$res = $bd->query($sql);
		if($res->num_rows > 0)
			$data["estado"] = "existeDNI";
		else {
			$ahora = date("Y-m-d H:i:s");
			$col[] = "fecha";
			$val[] = "'".$ahora."'";
			// $col[] = "recuperacion";
			// $val[] = "'".$ahora."'";
			$sql = "insert into usuarios (".implode(",", $col).") values (".implode(",", $val).")";
			if($bd->query($sql)) {
				$id = $bd->insert_id;
				$_SESSION[SesionTmp] = $id;
				$data["estado"] = "ok";
				// mail activacion
				include_once("../includes/class.phpmailer.php");
				include_once("../class/mails.php");
	    		$mailC = new Mails();
	    		$mailC->activar($id);
				$ok = 1;
			}
			else
				$data["estado"] = "error insert";
		}
	}
}
else {
	$sql = "select id from usuarios where activo != '0' and mail = '".$mail."' and id != $id";
	$res = $bd->query($sql);
	if($res->num_rows == 0) {
		$sql = "select id from usuarios where activo != '0' and dni = $dni and id != $id";
		$res = $bd->query($sql);
		if($res->num_rows == 0) {
			$upd = array();
			foreach($col as $k => $v)
				$upd[] = $v." = ".$val[$k];
			$sql = "update usuarios set ".implode(",", $upd)." where id = $id";
			if($bd->query($sql)) {
				$data["estado"] = "ok";
				$ok = 1;
			}
			else
				$data["estado"] = "error update";
		}
		else
			$data["estado"] = "existeDNI";
	}
	else
		$data["estado"] = "existeMail";
}
if($ok == 1) {
	if(isset($_POST["categoria"])) {
		$sql = "select categoria from usuarios_categorias where usuario = $id";
		$res = $bd->query($sql);
		$categorias = array();
		if($res->num_rows > 0) {
			while($fila = $res->fetch_assoc())
				$categorias[] = $fila["categoria"];
		}
		$ins = array();
		$del = array_diff($categorias, $_POST["categoria"]);
		foreach($_POST["categoria"] as $v) {
			if(!in_array($v, $categorias))
				$ins[] = "($id, $v)";
		}
		if(count($ins) > 0) {
			$sql = "insert into usuarios_categorias (usuario, categoria) values ".implode(",", $ins);
			$bd->query($sql);
		}
		foreach($del as $v) {
			$sql = "delete from usuarios_categorias where usuario = $id and categoria = $v limit 1";
			$bd->query($sql);
		}
	}
	else {
		$sql = "delete from usuarios_categorias where usuario = $id";
		$bd->query($sql);
	}
	if(isset($_POST["barrioAviso"])) {
		$sql = "select barrio from usuarios_barrios where usuario = $id";
		$res = $bd->query($sql);
		$barrios = array();
		if($res->num_rows > 0) {
			while($fila = $res->fetch_assoc())
				$barrios[] = $fila["barrio"];
		}
		$ins = array();
		$del = array_diff($barrios, $_POST["barrioAviso"]);
		foreach($_POST["barrioAviso"] as $v) {
			if(!in_array($v, $barrios))
				$ins[] = "($id, $v)";
		}
		if(count($ins) > 0) {
			$sql = "insert into usuarios_barrios (usuario, barrio) values ".implode(",", $ins);
			$bd->query($sql);
		}
		foreach($del as $v) {
			$sql = "delete from usuarios_barrios where usuario = $id and barrio = $v limit 1";
			$bd->query($sql);
		}
	}
	else {
		$sql = "delete from usuarios_barrios where usuario = $id";
		$bd->query($sql);
	}
	if(isset($_SESSION[SesionExterno]) && $_SESSION[SesionExterno] == 1) {
		$sql = "update usuarios set activo = '2' where id = $id";
		$bd->query($sql);
		$_SESSION[SesionId] = $id;
		$_SESSION[SesionNivel] = 0;
		if(isset($_SESSION[SesionTmp]))
			unset($_SESSION[SesionTmp]);
		$data["col"] = 1;
	}
}
echo json_encode($data);
?>