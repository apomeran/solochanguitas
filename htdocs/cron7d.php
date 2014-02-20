<?php
include_once("includes/config.php");
include_once("includes/class.phpmailer.php");
include_once("class/mails.php");
$mail = new Mails();
$ahora = new DateTime();
$bd = conectar();
// resumen semanal de ch publicadas en tus categorias
//      ultimo cron (para saber que ch son nuevas)
$sql = "select fecha from cron where tipo = '2' order by id desc limit 0,1";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
$cron = $fila["fecha"];
$sql = "select id from usuarios where aviso = '3' and activo = '2' and nivel = '0'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc()) {
    $nuevas = array();
    $sql = "select categoria from usuarios_categorias where usuario = ".$fila["id"];
    $res2 = $bd->query($sql);
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
    $mail->resumenNuevas($nuevas, $fila["id"], "semanal");
}
// guardo fecha fin cron
$sql = "insert into cron (fecha, tipo) values ('".$ahora->format("Y-m-d H:i:s")."', '2')";
$bd->query($sql);
?>