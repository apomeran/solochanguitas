<?php
error_reporting(0);
//error_reporting(E_ALL);
define("Sitio", "http://www.solochanguitas.com.ar");
// BD
define("Servidor", "192.168.0.66");
define("UsuarioBD", "sc_user");
define("ClaveBD", "0T90iSpjUU");
define("Base", "solochanguitas");
function conectar() {
	$mysqli = new mysqli(Servidor, UsuarioBD, ClaveBD, Base);
	if($mysqli->connect_error)
		echo "Error de conexi&oacute;n";
	$mysqli->set_charset("utf8");
	//$mysqli->query("SET NAMES 'utf8'");
	return $mysqli;
}
// sesion
define("SesionId", "sch-u-id");
define("SesionNivel", "sch-u-niv");
define("SesionTime", "sch-tmstp");
define("SesionExterno", "sch-ext");
define("SesionTmp", "sch-tmp");
define("SesionBloqueado", "sch-blq");
session_start();
date_default_timezone_set("America/Buenos_Aires");
$_SESSION[SesionTime] = time();
// slogan
define("Slogan", "Trabajos por los que and&aacute;s preguntando");
// mail
define("MailFrom", "admin@solochanguitas.com.ar");
define("MailFromName", "SoloChanguitas");
define("SalAct", "678wpfoYMXSU09485XXkl"); // activacion de cuenta
define("SalRec", "zoRYVASfsdf3345HdfCFK"); // recuperacion de clave
// otros
define("ChGratis", 2);
define("MaxDeuda", 20);
define("Limit", 15);
define("HashCost", 8);
define("Fee", .1);
$valoresVacios = array("", null, "0000", "0000-00-00", "0000-00-00 00:00:00", "00/00/0000", "00/00/0000 00:00:00");
$dias = array(1=>"Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
// MercadoPago
$clientId = "2529245147649319";
$clientSecret = "NH36PuEN69SVUg3xNUu8cktYgQfmabuZ";
//
require 'jsonwrapper.php';
header("Content-Type: text/html;charset=utf-8");
?>