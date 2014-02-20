<?php
include_once("../includes/config.php");
if(!isset($_POST["id"]))
	exit;
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$plan = $bd->real_escape_string($_POST["plan"]);
$sql = "insert into clicks (plan, changuita, fecha) values ($plan, $id, '".date("Y-m-d H:i:s")."')";
$bd->query($sql);
?>