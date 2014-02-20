<?php
include_once("../includes/config.php");
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$data["html"] = "<input type='hidden' name='source' value='manual' />";
$nInputs = 10;
for($i=0;$i<$nInputs;$i++)
    $data["html"] .= "<div><input type='text' name='invitado[]' value='' class='invitar-manual span4' /></div>";
echo json_encode($data);
?>