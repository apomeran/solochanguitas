<?php
include_once("../includes/config.php");
include_once("../class/seguridad.php");
include_once("../includes/class.phpmailer.php");
$bd = conectar();
$data["estado"] = "";
$nombre = $bd->real_escape_string($_POST["nombre"]);
$mail = $bd->real_escape_string($_POST["mail"]);
$mensaje = str_replace("\\r\\n", "<br/>", $bd->real_escape_string($_POST["mensaje"]));
$m = new PHPMailer;
$m->isHTML(true);
$m->From = MailFrom;
$m->FromName = MailFromName;
$m->Timeout = 30;
$m->CharSet = "UTF-8";
$m->Subject = "Contacto";
$m->Body = "<table style='width:800px;margin:10px auto;padding:10px 0;'><tr><td style='padding:0 0 10px;margin:0;'><a href='".Sitio."'><img src='".Sitio."/img/logo-mail.jpg' alt='SoloChanguitas' /></a></td></tr><tr><td style='padding:0 0 10px;margin:0;'><p style='padding:0 5px;margin:3px 0;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;'>Nombre: <strong>".$nombre."</strong><br/>E-mail: <strong>".$mail."</strong><br/>Mensaje:</p><p style='font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;padding:5px;margin:0 0 3px 3px;background-color:#dddddd;'>".$mensaje."</p></td></tr><tr><td style='margin:5px 0;padding:5px;border-top:1px solid #ccc'><p style='font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:11px;'>&copy; ".date("Y")." SoloChanguitas - Todos los derechos reservados.</p></td></tr></table>";
$m->AddAddress(MailFrom);
if($m->Send())
    $data["estado"] = "ok";
echo json_encode($data);
?>