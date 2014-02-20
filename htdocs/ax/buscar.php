<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
$_SESSION["ini"] = 0;
$_SESSION["orden"] = 0;
$campos = array("categoria", "subcategoria", "localidad", "barrio", "palabras");
foreach($campos as $v) {
	$_SESSION[$v] = "";
	if(isset($_POST[$v])) {
		if($v == "barrio") {
			foreach($_POST[$v] as $k => $vv)
				$_POST[$v][$k] = $bd->real_escape_string($vv);
			$_SESSION[$v] = $_POST[$v];
		}
		else
			$_SESSION[$v] = $bd->real_escape_string(trim($_POST[$v]));
	}
}
if(isset($_SESSION["palabras"]) && $_SESSION["palabras"] != "") {
	$sql = "insert into busquedas (busqueda, fecha) values ('".$_SESSION["palabras"]."', '".date("Y-m-d H:i:s")."')";
	$bd->query($sql);
}
?>