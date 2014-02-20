<?php
include_once("../../includes/config.php");
$data["html"] = "";
$data["pagar"] = 0;
if(!isset($_POST["id"])) {
    echo json_encode($data);
    exit;
}
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$sql = "select usuario, contratado, pagado, debe, diferencia, fee, fee_debe, estado from changuitas where id = $id";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
if($fila["pagado"] == "1" && $fila["fee"] != "0") {
    $data["html"] .= "<p>Esta changuita no registra ítems impagos</p>";
    echo json_encode($data);
    exit;
}
$items = array();
if($fila["pagado"] == "0") {
    $data["pagar"]++;
    $pagar = $fila["debe"];
    if($fila["diferencia"] > 0)
        $pagar = $fila["diferencia"];
    $sql = "select nombre, apellido, mail from usuarios where id = ".$fila["usuario"];
    $res = $bd->query($sql);
    $filaU = $res->fetch_assoc();
    $items[] = "El usuario <strong>".$filaU["nombre"]." ".$filaU["apellido"]." (".$filaU["mail"].")</strong> debe <span>$".$pagar."</span> por la publicación de la changuita.";
}
if($fila["fee"] == "0" && $fila["estado"] != "0") {
    $data["pagar"]++;
    $pagar = $fila["fee_debe"];
    $sql = "select nombre, apellido, mail from usuarios where id = ".$fila["contratado"];
    $res = $bd->query($sql);
    $filaU = $res->fetch_assoc();
    $items[] = "El usuario <strong>".$filaU["nombre"]." ".$filaU["apellido"]." (".$filaU["mail"].")</strong> debe <span>$".sprintf("%01.2f", $pagar)."</span> por haber sido contratado para hacer la changuita (fee).";
}
if($data["pagar"] > 0) {
    $data["html"] .= "<form><input type='hidden' name='id' value='$id' />";
    if($data["pagar"] == 1)
        $data["html"] .= $items[0];
    else {
        $data["html"] .= "<p>Esta changuita tiene dos ítems impagos. Elegí qué pago estás informando (si querés informar ambos, hacelo de a uno).</p>";
        $i = 1;
        foreach ($items as $v) {
            $data["html"] .= "<label class='radio'><input type='radio' name='item' value='$i' class='radio'> $v</label>";
            $i++;
        }
    }
    $data["html"] .= "</form>";
}
echo json_encode($data);
?>