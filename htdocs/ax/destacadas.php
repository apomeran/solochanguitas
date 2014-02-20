<?php
include_once("../includes/config.php");
include_once("../class/destacadas.php");
$d = new Destacadas();
$p = $_POST["p"];
$c = 0;
if(isset($_POST["c"]))
	$c = $_POST["c"];
echo $d->mostrar($p, $c);
?>