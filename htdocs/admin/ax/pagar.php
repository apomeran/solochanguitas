<?php
include_once("../../includes/config.php");
$data["estado"] = "";
if(!isset($_POST["id"])) {
    echo json_encode($data);
    exit;
}
include_once("../../class/logb.php");
$logb = new LogBalance();
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$item = $bd->real_escape_string($_POST["item"]);
$sql = "select usuario, contratado, pagado, debe, diferencia, fee, fee_debe, estado from changuitas where id = $id";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
if($item == 0) {
    if($fila["pagado"] == "1" && $fila["fee"] != "0") {
        echo json_encode($data);
        exit;
    }
    if($fila["pagado"] == "0")
        $item = 1;
    else if($fila["fee"] == "0" && $fila["estado"] != "0")
        $item = 2;
    else {
        echo json_encode($data);
        exit;
    }
}
if($item == 1) {
    $pagar = $fila["debe"];
    if($fila["diferencia"] > 0)
        $pagar = $fila["diferencia"];
    $u = $fila["usuario"];
    $tipo = 4;
    $sql = "update changuitas set pagado = '1', diferencia = 0 where id = $id";
    $bd->query($sql);
}
else if($item == 2) {
    $pagar = $fila["fee_debe"];
    $u = $fila["contratado"];
    $tipo = 5;
    $sql = "update changuitas set fee = '1' where id = $id";
    $bd->query($sql);
}
else {
    echo json_encode($data);
    exit;
}
$sql = "update usuarios set balance = balance + $pagar where id = $u";
$bd->query($sql);
$logb->log($fila["usuario"], $id, $pagar, $tipo);
$data["estado"] = "ok";
echo json_encode($data);
?>