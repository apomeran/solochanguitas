<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$data["estado"] = "";
if(!isset($_POST["id"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("../includes/mercadopago.php");
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$mp = new MP($clientId, $clientSecret);
$sql = "select ch.titulo, ch.descripcion, ch.debe, ch.diferencia, u.mail from changuitas as ch left join usuarios as u on ch.usuario = u.id where ch.id = $id and ch.activo = '1' and ch.pagado = '0'";
$res = $bd->query($sql);
if($res->num_rows == 1)
    $data["estado"] = "ok";
$fila = $res->fetch_assoc();
$precio = $fila["debe"];
if($fila["diferencia"] > 0)
    $precio = $fila["diferencia"];
$preference_data = array(
    "items" => array(
        array(
            "id"          => $id,
            "title"       => $fila["titulo"],
            "quantity"    => 1,
            "currency_id" => "ARS",
            "unit_price"  => (int)sprintf("%01.2f", $precio),
            "description" => $fila["descripcion"],
            "picture_url" => Sitio."/img/logo.png"
       )
    ),
    "payer" => array(
        "email" => $fila["mail"]
    )
);
$preference = $mp->create_preference($preference_data);
$data["preferencia"] = $preference;
echo json_encode($data);
?>