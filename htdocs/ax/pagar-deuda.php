<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$data["estado"] = "";
if((!isset($_POST["id"]) || count($_POST["id"]) == 0) && (!isset($_POST["fee"]) || count($_POST["fee"]) == 0))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("../includes/mercadopago.php");
$bd = conectar();
$mp = new MP($clientId, $clientSecret);
$ok = 0;
$nId = 0;
$nFee = 0;
$deuda = 0;
$desc = array();
$preference_data = array();
if(isset($_POST["id"]) && count($_POST["id"]) > 0) {
    $nId = count($_POST["id"]);
    foreach ($_POST["id"] as $v) {
        $ch = $bd->real_escape_string($v);
        $sql = "select ch.debe, ch.diferencia, ch.titulo, u.mail from changuitas as ch left join usuarios as u on ch.usuario = u.id where ch.id = $ch and ch.pagado = '0' and ch.debe > 0";
        $res = $bd->query($sql);
        if($res->num_rows == 1)
            $ok++;
        $fila = $res->fetch_assoc();
        $monto = $fila["debe"];
        if($fila["diferencia"] > 0)
            $monto = $fila["diferencia"];
        $deuda += $monto;
        $desc[] = $fila["titulo"];
    }
}
if(isset($_POST["fee"]) && count($_POST["fee"]) > 0) {
    $nFee = count($_POST["fee"]);
    foreach ($_POST["fee"] as $v) {
        $ch = $bd->real_escape_string($v);
        $sql = "select ch.fee_debe, ch.titulo, u.mail from changuitas as ch left join usuarios as u on ch.usuario = u.id where ch.id = $ch and ch.fee = '0' and ch.fee_debe > 0";
        $res = $bd->query($sql);
        if($res->num_rows == 1)
            $ok++;
        $fila = $res->fetch_assoc();
        $monto = $fila["fee_debe"];
        $deuda += $monto;
        $desc[] = $fila["titulo"]." (fee)";
    }
}
if($ok == $nId + $nFee)
    $data["estado"] = "ok";
$preference_data = array(
    "items" => array(
        array(
            "title"       => "Deuda con SoloChanguitas",
            "quantity"    => 1,
            "currency_id" => "ARS",
            "unit_price"  => (float)sprintf("%01.2f", $deuda),
            "description" => "Changuita/s a pagar: ".implode(", ", $desc),
            "picture_url" => Sitio."/img/logo.png"
       )
    ),
    "payer" => array(
        "email" => $fila["mail"]
    )
);
$preference = $mp->create_preference ($preference_data);
$data["preferencia"] = $preference;
echo json_encode($data);
?>