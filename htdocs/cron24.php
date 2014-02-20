<?php
include_once("includes/config.php");
include_once("includes/class.phpmailer.php");
include_once("class/notificaciones.php");
$not = new Notificaciones();
include_once("class/mails.php");
$mail = new Mails();
include_once("class/logb.php");
$logb = new LogBalance();
$ahora = new DateTime();
$unMes = new DateTime();
$unMes->modify("-1 month");
$unaSemana = new DateTime();
$unaSemana->modify("+1 week");
$bd = conectar();
// vencidas / por vencer
$sql = "select id, fecha, cuando_fecha, recordatorio_vence from changuitas where estado = '0' and activo = '1' and vencida = '0'";
$res = $bd->query($sql);
$vencida = array();
$porVencer = array();
while($fila = $res->fetch_assoc()) {
    if($fila["fecha"] < $unMes->format("Y-m-d"))
        $vencida[] = $fila["id"];
    else if($fila["cuando_fecha"] != "0000-00-00" && $fila["cuando_fecha"] < $ahora->format("Y-m-d"))
        $vencida[] = $fila["id"];
    else {
        if($fila["cuando_fecha"] != "0000-00-00")
            $fechaPorVencer = $fila["cuando_fecha"];
        else {
            $vencimiento = new DateTime($fila["fecha"]);
            $vencimiento->modify("+1 month");
            $fechaPorVencer = $vencimiento->format("Y-m-d");
        }
        $faltaUnaSemana = new DateTime($fechaPorVencer);
        $faltaUnaSemana->modify("-1 week");
        if($ahora > $faltaUnaSemana && $fila["recordatorio_vence"] == 0)
            $porVencer[] = $fila["id"];
    }
}
foreach($vencida as $v) {
    $sql = "update changuitas set vencida = '1' where id = $v";
    $bd->query($sql);
    // ver si devuelvo la plata
    $devuelvo = 0;
    $sql = "select id from postulaciones where changuita = $v";
    $res = $bd->query($sql);
    if($res->num_rows == 0) {
        $devuelvo = 1;
        $sql = "select debe, pagado, usuario, bonificado from changuitas where id = $v";
        $res = $bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["debe"] > 0 && $fila["bonificado"] == "0") {
            $sql = "update usuarios set balance = balance + ".$fila["debe"]." where id = ".$fila["usuario"];
            $bd->query($sql);
            $logb->log($fila["usuario"], $id, $fila["debe"], 3);
        }
        // else if($fila["bonificado"] == "1") {
        //     $devuelvo = 2;
        //     $sql = "update usuarios set gratis = gratis + 1 where id = ".$fila["usuario"];
        //     $bd->query($sql);
        // }
        else
            $devuelvo = 0;
        if($fila["pagado"] == "0") {
            $sql = "update changuitas set pagado = '1', bonificado = '1' where id = $v";
            $bd->query($sql);
        }
    }
    // ver si opcion republicar en mail
    $republicar = 0;
    $sql = "select cuando from changuitas where id = $v";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    if($fila["cuando"] != 3)
        $republicar = 1;
    //
    $not->reset($v);
    $not->vencio($v, $devuelvo);
    // $not->rechazar($v);
    $mail->vencio($v, $devuelvo, $republicar);
    // $mail->rechazar($v);
}
foreach($porVencer as $v) {
    $sql = "update changuitas set recordatorio_vence = '1' where id = $v";
    $bd->query($sql);
    $not->porVencer($v);
    $mail->porVencer($v);
}
// calificacion pendiente
$sql = "select id, usuario, contratado, fecha_contratacion, recordatorio_calificar from changuitas where (estado = '2' or estado = '1') and activo = '1' and vencida = '0'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc()) {
    $fechaContratacion = new DateTime($fila["fecha_contratacion"]);
    $fechaContratacion->modify("+1 week");
    if($fechaContratacion > $ahora)
        continue;
    $fechaRecordatorio = new DateTime($fila["recordatorio_calificar"]);
    $fechaRecordatorio->modify("+1 week");
    if($fechaRecordatorio < $ahora) {
        $ch = $fila["id"];
        $sql = "select usuario from calificaciones where changuita = $ch";
        $res2 = $bd->query($sql);
        if($res2->num_rows == 0) {
            $not->calificacionPendiente($ch, $fila["usuario"]);
            $not->calificacionPendiente($ch, $fila["contratado"]);
            $mail->calificacionPendiente($ch, $fila["usuario"], $fila["contratado"]);
            $mail->calificacionPendiente($ch, $fila["contratado"], $fila["usuario"]);
        }
        else if($res2->num_rows == 1) {
            $fila2 = $res2->fetch_assoc();
            $califico = $fila["usuario"];
            $faltaCalificar = $fila["contratado"];
            if($fila["usuario"] == $fila2["usuario"]) {
                $califico = $fila["contratado"];
                $faltaCalificar = $fila["usuario"];
            }
            $not->calificacionPendiente($ch, $faltaCalificar, $califico);
            $mail->calificacionPendiente($ch, $faltaCalificar, $califico);
        }
        if($res2->num_rows < 2) {
            $sql = "update changuitas set recordatorio_calificar = '".$ahora->format("Y-m-d")."' where id = $ch";
            $bd->query($sql);
        }
    }
}
// caduca activacion
$sql = "select id, fecha from usuarios where activo = '1'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc()) {
    $fechaCarga = new DateTime($fila["fecha"]);
    $fechaCarga->modify("+1 week");
    if($fechaCarga > $ahora)
        continue;
    $sql = "update usuarios set activo = '0' where id = ".$fila["id"];
    $bd->query($sql);
}
// recuerda activacion
//$sql = "select id, fecha from usuarios where activo = '1' and recordatorio_activacion = '0'";
$sql = "select id, fecha from usuarios where activo = '1'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc()) {
    $fechaCarga = new DateTime($fila["fecha"]);
    $fechaCarga->modify("+1 day");
    if($fechaCarga > $ahora)
        continue;
    $mail->recordatorioActivar($fila["id"]);
    // $sql = "update usuarios set recordatorio_activacion = '1' where id = ".$fila["id"];
    // $bd->query($sql);
}
// deuda
$sql = "select id, recordatorio_deuda from usuarios where balance < 0 and aviso_bal = '1' and activo = '2'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc()) {
    $fechaRecDeuda = new DateTime($fila["recordatorio_deuda"]);
    $fechaRecDeuda->modify("+3 days");
    if($ahora > $fechaRecDeuda) {
        $mail->deuda($fila["id"]);
        $sql = "update usuarios set recordatorio_deuda = '".$ahora->format("Y-m-d")."' where id = ".$fila["id"];
        $bd->query($sql);
    }
}
// resumen diario de ch publicadas en tus categorias
//      ultimo cron (para saber que ch son nuevas)
$sql = "select fecha from cron where tipo = '1' order by id desc limit 0,1";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
$cron = $fila["fecha"];
$sql = "select id from usuarios where aviso = '2' and activo = '2' and nivel = '0'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc()) {
    $nuevas = array();
    $sql = "select categoria from usuarios_categorias where usuario = ".$fila["id"];
    $res2 = $bd->query($sql);
    $categorias = array();
    while($fila2 = $res2->fetch_assoc())
        $categorias[] = $fila2["categoria"];
    $sql = "select barrio from usuarios_barrios where usuario = ".$fila["id"];
    $res2 = $bd->query($sql);
    $barrios = array();
    while($fila2 = $res2->fetch_assoc())
        $barrios[] = $fila2["barrio"];
    $sql = "select id, subcategoria, barrio from changuitas where usuario != ".$fila["id"]." and activo = '1' and vencida = '0' and fecha > '$cron' order by fecha asc";
    $res3 = $bd->query($sql);
    while($fila3 = $res3->fetch_assoc()) {
        if(in_array($fila3["subcategoria"], $categorias) && in_array($fila3["barrio"], $barrios))
            $nuevas[] = $fila3["id"];
    }
    $mail->resumenNuevas($nuevas, $fila["id"], "diario");
}
// guardo fecha fin cron
$sql = "insert into cron (fecha, tipo) values ('".$ahora->format("Y-m-d H:i:s")."', '1')";
$bd->query($sql);
?>