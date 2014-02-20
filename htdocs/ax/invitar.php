<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../includes/class.phpmailer.php");
$data["estado"] = "";
$data["error"] = "";
if(!isset($_POST["invitado"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$invitados = array_unique($_POST["invitado"]);
$bd = conectar();
$sql = "select mail from invitados where usuario = ".$_SESSION[SesionId];
$res = $bd->query($sql);
$yaInvitados = array();
while($fila = $res->fetch_assoc())
    $yaInvitados[] = $fila["mail"];
$sql = "select nombre, apellido, mail from usuarios where id = ".$_SESSION[SesionId];
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
$m = new PHPMailer;
$m->isHTML(true);
$m->Timeout = 30;
$m->CharSet = "UTF-8";
$m->From = $fila["mail"];
$m->FromName = $fila["nombre"]." ".$fila["apellido"];
$m->Subject = "Invitación a SoloChanguitas";
$m->Body = "<table style='width:800px;margin:10px auto;padding:10px 0;'><tr><td style='padding:0 0 10px;margin:0;'><a href='".Sitio."'><img src='".Sitio."/img/logo-mail.jpg' alt='SoloChanguitas' /></a></td></tr><tr><td style='padding:0 0 10px;margin:0;'><p style='padding:0 5px;margin:3px 0;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;'>Te invito a conocer <strong><a href='".Sitio."'>SoloChanguitas</a></strong>, la red donde vas a encontrar los trabajos por los que andás preguntando. Podés contratar a alguien para que te instale programas en una computadora nueva, una estudiante de peluquería a domicilio o ¡postularte para trabajar de extra!</p><h4 style='padding:0 5px;margin:6px 0 3px;font-family:Helvetica, Arial, sans-serif;'>¿Por qué podés confiar en la red?</h4><p style='padding:0 5px;margin:3px 0;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;'>Además de las calificaciones, cada usuario se encuentra linkeado a otros a través de la invitación al sitio desde su libreta de contactos de e-mail. Esto significa que antes de acordar un intercambio con otra persona, vas a poder ver cuántos usuarios conocidos posee en el sitio, y hasta ver si tienen alguien en común.</p><p style='padding:0 5px;margin:3px 0;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;'>Para generar confianza, necesito tener todos los usuarios conocidos que pueda en la red y, por este motivo, te pido si para ayudarme podrías registrarte haciendo click <a href='".Sitio."/#/editar-usuario'>aquí</a>. ¡Lleva menos de un minuto y ya voy a quedar como contacto tuyo en la red para cuando necesites contratar o trabajar!</p><p style='padding:0 5px;margin:3px 0;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;'>Muchas gracias,<br/>".$fila["nombre"]." ".$fila["apellido"]."</td></tr><tr><td style='margin:5px 0;padding:5px;border-top:1px solid #ccc;'></td></tr></table>";
foreach($invitados as $v) {
    $v = trim($bd->real_escape_string($v));
    if($v == "" || $v == $fila["mail"])
        continue;
    $m->ClearAddresses();
    $m->AddAddress($v);
    $m->Send();
    if(in_array($v, $yaInvitados))
        continue;
    $sql = "insert into invitados (mail, usuario) values ('$v', ".$_SESSION[SesionId].")";
    $bd->query($sql);
}
$data["estado"] = "ok";
include_once("invitados-map.php");
echo json_encode($data);
?>