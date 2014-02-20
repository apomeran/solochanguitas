<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$data["estado"] = "";
if(isset($_SESSION[SesionId])) {
   $data["estado"] = "ok";
   if(isset($_POST["bloqueado"]) && isset($_SESSION[SesionBloqueado]) && $_SESSION[SesionBloqueado] == 1)
        $data["estado"] = "bloqueado";
}
else
    $data["estado"] = "no";
echo json_encode($data);
?>