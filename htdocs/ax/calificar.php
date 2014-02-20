<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$data["estado"] = "";
if(!isset($_POST["id"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("../includes/class.phpmailer.php");
$bd = conectar();
include_once("../class/funciones.php");
$f = new Funciones();
include_once("../class/logb.php");
$logb = new LogBalance();
$id = $bd->real_escape_string($_POST["id"]);
$valor = $bd->real_escape_string($_POST["valor"]);
$realizo = $bd->real_escape_string($_POST["realizo"]);
$comentario = $bd->real_escape_string($f->convertirCarga($_POST["comentario"], ""));
$sql = "select usuario, contratado, titulo, precio, fee_debe from changuitas where id = $id and activo = '1' and (estado = '2' or estado = '1')";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
$usuario = $fila["usuario"];
$soyU = 0;
if($fila["usuario"] == $_SESSION[SesionId]) {
    $soyU = 1;
	$usuario = $fila["contratado"];
}
$sql = "select id from calificaciones where usuario = $usuario and changuita = $id and activo = '1'";
$res = $bd->query($sql);
if($res->num_rows == 0) {
	$sql = "insert into calificaciones (usuario, changuita, calificacion, hecha, comentario, fecha) values ($usuario, $id, '$valor', '$realizo', '$comentario', '".date("Y-m-d H:i:s")."')";
	$data["estado"] = "ok";
	if($bd->query($sql)) {
        // notificaciones
        include_once("../class/notificaciones.php");
        $not = new Notificaciones();
        $not->calificar($id, $usuario);
        // mails
        include_once("../class/mails.php");
        $mail = new Mails();
        $mail->calificar($id, $usuario);
        if($fila["fee_debe"] == 0) {
            if($soyU == 1 && $realizo == "0") {
                $sql = "update changuitas set fee = '2' where id = $id";
                $bd->query($sql);
            }
            else if($realizo == "1") {
                $sql = "update changuitas set fee = '0' where id = $id";
                $bd->query($sql);
                $sql = "select balance from usuarios where id = ".$fila["contratado"];
                $resB = $bd->query($sql);
                $filaB = $resB->fetch_assoc();
                $balance = $filaB["balance"];
                $fee = $fila["precio"]*Fee;
                $debe = 0;
                if($balance >= $fee) {
                    $sql = "update changuitas set fee = '1' where id = $id";
                    $bd->query($sql);
                }
                else if($balance > 0)
                    $debe = $fee-$balance;
                else
                    $debe = $fee;
                $sql = "update changuitas set fee_debe = $debe where id = $id";
                $bd->query($sql);
                $sql = "update usuarios set balance = balance - $fee where id = ".$fila["contratado"];
                $bd->query($sql);
                $logb->log($fila["contratado"], $id, $fee*-1, 2);
                if($debe > 0) {
                    $mail->fee($id, $fila["contratado"]);
                    $not->fee($id, $fila["contratado"]);
                }
            }
        }
	}
}
$sql = "select id, hecha from calificaciones where changuita = $id and activo = '1'";
$res = $bd->query($sql);
if($res->num_rows == 2) {
	$sql = "update changuitas set estado = '3' where id = $id";
	$bd->query($sql);
	// veo si devuelvo la plata
	$sinHacer = 0;
	while($fila = $res->fetch_assoc()) {
		if($fila["hecha"] == "0")
			$sinHacer++;
	}
	if($sinHacer == 2) {
		$sql = "select debe, pagado from changuitas where id = $id";
        $res = $bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["debe"] > 0) {
        	$sql = "update usuarios set balance = balance + ".$fila["debe"]." where id = $usuario";
        	$bd->query($sql);
        	$not->noHecha($id, $usuario);
        	$mail->noHecha($id, $usuario);
            $logb->log($usuario, $id, $fila["debe"], 3);
       	}
        else
            $mail->noHecha2($id, $usuario);
        if($fila["pagado"] == "0") {
            $sql = "update changuitas set pagado = '1', bonificado = '1' where id = $id";
            $bd->query($sql);
        }
	}
}
else if($res->num_rows == 1) {
    $sql = "update changuitas set estado = '2' where id = $id";
    $bd->query($sql);
}
include_once("calificaciones-map.php");
echo json_encode($data);
?>