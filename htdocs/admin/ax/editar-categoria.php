<?php
include_once("../../includes/config.php");
$data["estado"] = "";
if(!isset($_SESSION[SesionNivel]) || $_SESSION[SesionNivel] < 1) {
    $data["estado"] = "forbidden";
    echo json_encode($data);
    exit;
}
if(!isset($_POST["id"])) {
    $data["estado"] = "forbidden";
    echo json_encode($data);
    exit;
}
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$categoria = trim($bd->real_escape_string($_POST["categoria"]));
$data["link"] = "categorias.php";
if($id > 0)
    $sql = "update categorias set categoria = '$categoria' where id = $id";
else {
    $sql = "select orden from categorias where activo = '1' order by orden desc";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $nuevoOrden = $fila["orden"] + 1;
    $sql = "insert into categorias (categoria, orden) values ('$categoria', $nuevoOrden)";
}
if($bd->query($sql))
    $data["estado"] = "ok";
echo json_encode($data);
?>