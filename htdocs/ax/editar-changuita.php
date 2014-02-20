<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
if(!isset($_POST["id"]))
	$s->salir();
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
if($id > 0) {
	$sql = "select id, precio, descripcion, plan, pagado, bonificado from changuitas where id = $id and activo = '1' and estado = '0' and usuario = ".$_SESSION[SesionId];
	$res = $bd->query($sql);
	if($res->num_rows != 1)
		$s->salir();
	$fila = $res->fetch_assoc();
}
$sql = "select balance from usuarios where id = ".$_SESSION[SesionId];
$res = $bd->query($sql);
$filaBalance = $res->fetch_assoc();
$balance = $filaBalance["balance"];
include_once("../class/funciones.php");
$f = new Funciones();
include_once("../class/logb.php");
$logb = new LogBalance();
$data["id"] = 0;
if($id == 0) {
	$columnas = array("titulo", "categoria", "subcategoria", "localidad", "barrio", "descripcion", "precio", "plan", "cuando", "cuando_dias", "cuando_fecha", "desde_hora", "hasta_hora");
	$convertir = array("", "", "", "", "", "", "", "", "", "check", "fecha", "", "");
}
else {
	$columnas = array("descripcion", "precio", "plan");
	$convertir = array("", "", "");
}
$col = array();
$val = array();
// $gratis = 0;
foreach($columnas as $k => $v) {
	if(isset($_POST[$v])) {
		if($v == "descripcion" && $id > 0) {
			if(trim($_POST[$v]) != "") {
				$col[] = $v;
				$val[] = "'".$fila["descripcion"]."\n".$bd->real_escape_string("<span>Agregado el ".date("d/m/Y H:i")." hs:</span> ".$f->filtrarTxt($f->convertirCarga($_POST[$v], $convertir[$k])))."'";
			}
		}
		else if($v == "descripcion") {
			$col[] = $v;
			$val[] = "'".$f->filtrarTxt($bd->real_escape_string($f->convertirCarga($_POST[$v], $convertir[$k])))."'";
		}
		else if($v == "desde_hora") {
			$col[] = "cuando_hora_desde";
			if($_POST["desde_hora"] != -1)
				$val[] = "'".$bd->real_escape_string($_POST["desde_hora"]).$bd->real_escape_string($_POST["desde_minuto"])."00'";
			else
				$val[] = "''";
		}
		else if($v == "hasta_hora") {
			$col[] = "cuando_hora_hasta";
			if($_POST["hasta_hora"] != -1)
				$val[] = "'".$bd->real_escape_string($_POST["hasta_hora"]).$bd->real_escape_string($_POST["hasta_minuto"])."00'";
			else
				$val[] = "''";
		}
		else {
			$col[] = $v;
			$val[] = "'".$bd->real_escape_string($f->convertirCarga($_POST[$v], $convertir[$k]))."'";
		}
	}
}
// carga
if($id == 0) {
	$ahora = date("Y-m-d H:i:s");
	$col[] = "fecha";
	$val[] = "'".$ahora."'";
	$col[] = "usuario";
	$val[] = $_SESSION[SesionId];
	$plan = $bd->real_escape_string($_POST["plan"]);
	if($plan == 1) {
		$col[] = "pagado";
		$val[] = "'1'";
		$col[] = "bonificado";
		$val[] = "'1'";
		// $plan = 1;
		// $gratis++;
	}
	$sql = "select precio from planes where id = $plan";
	$res = $bd->query($sql);
	$filaD = $res->fetch_assoc();
	$monto = $filaD["precio"];
	$col[] = "debe";
	$val[] = $monto;
	$sql = "insert into changuitas (".implode(",", $col).") values (".implode(",", $val).")";
	if($bd->query($sql)) {
		$id = $bd->insert_id;
		// mail y notif de ch nueva
		include_once("../includes/class.phpmailer.php");
		include_once("../class/mails.php");
		$mail = new Mails();
		$mail->nuevaChanguita($id);
		include_once("../class/notificaciones.php");
		$not = new Notificaciones();
		$not->nuevaChanguita($id);
		//
		$data["estado"] = "ok";
		if($plan > 1) {
			$data["estado"] = "pagar";
			if($balance >= $monto) {
				// pago con credito: pagado a 1, bal -monto
				$sql = "update changuitas set pagado = '1' where id = $id";
				$bd->query($sql);
				$data["estado"] = "ok";
			}
			else if($balance > 0) {
				// pago parte: cargo dif, bal -monto
				$sql = "update changuitas set diferencia = ".($monto - $balance)." where id = $id";
				$bd->query($sql);
			}
			$sql = "update usuarios set balance = balance - $monto where id = ".$_SESSION[SesionId];
			$bd->query($sql);
			$logb->log($_SESSION[SesionId], $id, $monto*-1, 1);
		}
	}
	else
		$data["estado"] = "error";
}
else {
	// valida precio: que no haya bajado
	if($_POST["precio"] < $fila["precio"])
		$data["estado"] = "precio";
	else {
		$upd = array();
		if($_POST["plan"] > $fila["plan"]) {
			$sql = "select precio from planes where id = ".$bd->real_escape_string($_POST["plan"]);
			$res = $bd->query($sql);
			$filaPlanNuevo = $res->fetch_assoc();
			$precioN = $filaPlanNuevo["precio"];
			$sql = "select precio from planes where id = ".$fila["plan"];
			$res = $bd->query($sql);
			$filaPlanViejo = $res->fetch_assoc();
			$precioV = $filaPlanViejo["precio"];
			$col[] = "debe";
			$val[] = $precioN;
		}
		foreach($col as $k => $v)
			$upd[] = $v." = ".$val[$k];
		$sql = "update changuitas set ".implode(",", $upd)." where id = $id";
		if($bd->query($sql)) {
			if($_POST["plan"] <= $fila["plan"] && $fila["pagado"] > 0)
				$data["estado"] = "ok";
			else { // cambie, no pague o ambas
				$data["estado"] = "pagar";
				if($fila["pagado"] == '1') {
					// pagado a 0, guardar dif, cobrar dif, balance -dif si no paga
					$cobrar = $precioN - $precioV;
					// 		si vengo de bonificado, cobro entero
					// if($fila["bonificado"] == "1")
					// 	$cobrar = $precioN;
					$sql = "update changuitas set pagado = '0', bonificado = '0', diferencia = $cobrar where id = $id";
					$bd->query($sql);
					$sql = "update usuarios set balance = balance - $cobrar where id = ".$_SESSION[SesionId];
					$bd->query($sql);
					$logb->log($_SESSION[SesionId], $id, $cobrar*-1, 1);
				}
				else {
					if($_POST["plan"] > $fila["plan"]) {
						$cobrar = $precioN - $precioV;
						// bal -dif si no paga
						$sql = "update usuarios set balance = balance - $cobrar where id = ".$_SESSION[SesionId];
						$bd->query($sql);
						$logb->log($_SESSION[SesionId], $id, $cobrar*-1, 1);
					}
				}
			}
		}
		else
			$data["estado"] = "error";
	}
}
// palabras clave
if($data["estado"] != "error" && $data["estado"] != "precio") {
	$sql = "delete from changuitas_palabras where changuita = $id";
	$bd->query($sql);
	$sqlIns = array();
	$pc = array_map('trim', explode(",", $bd->real_escape_string($_POST["palabras"])));
	foreach ($pc as $v) {
		if($v == "")
			continue;
		$sqlIns[] = "($id, '$v')";
	}
	if(count($sqlIns) > 0) {
		$sql = "insert into changuitas_palabras (changuita, palabra) values ".implode(", ", $sqlIns).";";
		$bd->query($sql);
	}
}

$data["id"] = $id;
echo json_encode($data);
?>