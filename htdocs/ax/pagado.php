<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
if(!isset($_POST["id"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
include_once("../class/logb.php");
$logb = new LogBalance();
$s->permitir(0);
$bd = conectar();
if(!is_array($_POST["id"]))
    $ids[] = $_POST["id"];
else
    $ids = $_POST["id"];
if(!is_array($_POST["fee"]))
    $fees[] = $_POST["fee"];
else
    $fees = $_POST["fee"];
foreach ($ids as $v) {
    $id = $bd->real_escape_string($v);
    $sql = "select debe, diferencia from changuitas where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $monto = $fila["debe"];
    if($fila["diferencia"] > 0)
        $monto = $fila["diferencia"];
    $sql = "update usuarios set balance = balance + $monto where id = ".$_SESSION[SesionId];
    $bd->query($sql);
    $sql = "update changuitas set pagado = '1', diferencia = 0 where id = $id";
    $res = $bd->query($sql);
    $logb->log($_SESSION[SesionId], $id, $monto, 4);
}
foreach ($fees as $v) {
    $id = $bd->real_escape_string($v);
    $sql = "select fee_debe from changuitas where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $monto = $fila["fee_debe"];
    $sql = "update usuarios set balance = balance + $monto where id = ".$_SESSION[SesionId];
    $bd->query($sql);
    $sql = "update changuitas set fee = '1' where id = $id";
    $res = $bd->query($sql);
    $logb->log($_SESSION[SesionId], $id, $monto, 5);
}
?>