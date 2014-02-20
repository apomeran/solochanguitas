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
$categoria = $bd->real_escape_string($_POST["categoria"]);
$subcategoria = trim($bd->real_escape_string($_POST["subcategoria"]));
$hora = trim($bd->real_escape_string($_POST["hora"]));
$data["link"] = "subcategorias.php?id=$categoria";

if($id > 0) {
    $sql = "select categoria from subcategorias where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    if($fila["categoria"] != $categoria) {
        $sql = "select MAX(orden) as mx from subcategorias where categoria = $categoria";
        $res = $bd->query($sql);
        $fila = $res->fetch_assoc();
        $orden = $fila["mx"] + 1;
        $sql = "update subcategorias set categoria = $categoria, subcategoria = '$subcategoria', hora = '$hora', orden = $orden where id = $id";
    }
    else
        $sql = "update subcategorias set categoria = $categoria, subcategoria = '$subcategoria', hora = '$hora' where id = $id";
}
else {
    $sql = "select orden from subcategorias where categoria = $categoria and activo = '1' order by orden desc";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $nuevoOrden = $fila["orden"] + 1;
    $sql = "insert into subcategorias (subcategoria, categoria, hora, orden) values ('$subcategoria', $categoria, '$hora', $nuevoOrden)";
}
if($bd->query($sql))
    $data["estado"] = "ok";
echo json_encode($data);
?>