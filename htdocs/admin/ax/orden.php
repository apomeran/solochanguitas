<?php
include_once("../../includes/config.php");
$data["estado"] = "";
if(!isset($_SESSION[SesionNivel]) || $_SESSION[SesionNivel] < 1) {
    echo json_encode($data);
    exit;
}
if(!isset($_POST["id"]) || !isset($_POST["tabla"])) {
    echo json_encode($data);
    exit;
}
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$tabla = $bd->real_escape_string($_POST["tabla"]);
$dir = $bd->real_escape_string($_POST["d"]);
$tablaOk = array("categorias", "subcategorias");
if(!in_array($tabla, $tablaOk)) {
    echo json_encode($data);
    exit;
}
if($tabla == "categorias") {
    $sql = "select MIN(orden) as mn, MAX(orden) as mx from categorias where activo = '1'";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $min = $fila["mn"];
    $max = $fila["mx"];
    $sql = "select orden from categorias where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $ordenActual = $fila["orden"];
    if(($min == $ordenActual && $dir == 1) || ($max == $ordenActual && $dir == -1)) {
        echo json_encode($data);
        exit;
    }
    $sql = "select id from categorias where activo = '1' order by orden asc";
    $res = $bd->query($sql);
    $o = 1;
    // normalizo
    while($fila = $res->fetch_assoc()) {
        $sql = "update categorias set orden = $o where id = ".$fila["id"];
        $bd->query($sql);
        $o++;
    }
    //
    $sql = "select orden from categorias where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $ordenActual = $fila["orden"];
    if($dir == 1) {
        $ordenCambiar = $ordenActual - 1;
        $signo = "-";
    }
    else {
        $ordenCambiar = $ordenActual + 1;
        $signo = "+";
    }
    $sql = "update categorias set orden = $ordenActual where orden = $ordenCambiar";
    $bd->query($sql);
    $sql = "update categorias set orden = $ordenActual $signo 1 where id = $id";
    $bd->query($sql);
    $data["estado"] = "ok";
}
else if($tabla == "subcategorias") {
    $sql = "select categoria from subcategorias where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $cat = $fila["categoria"];
    $sql = "select MIN(orden) as mn, MAX(orden) as mx from subcategorias where categoria = $cat and activo = '1'";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $min = $fila["mn"];
    $max = $fila["mx"];
    $sql = "select orden from subcategorias where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $ordenActual = $fila["orden"];
    if(($min == $ordenActual && $dir == 1) || ($max == $ordenActual && $dir == -1)) {
        echo json_encode($data);
        exit;
    }
    $sql = "select id from subcategorias where categoria = $cat and activo = '1' order by orden asc";
    $res = $bd->query($sql);
    $o = 1;
    // normalizo
    while($fila = $res->fetch_assoc()) {
        $sql = "update subcategorias set orden = $o where id = ".$fila["id"];
        $bd->query($sql);
        $o++;
    }
    //
    $sql = "select orden from subcategorias where id = $id";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
    $ordenActual = $fila["orden"];
    if($dir == 1) {
        $ordenCambiar = $ordenActual - 1;
        $signo = "-";
    }
    else {
        $ordenCambiar = $ordenActual + 1;
        $signo = "+";
    }
    $sql = "update subcategorias set orden = $ordenActual where categoria = $cat and orden = $ordenCambiar";
    $bd->query($sql);
    $sql = "update subcategorias set orden = $ordenActual $signo 1 where id = $id";
    $bd->query($sql);
    $data["estado"] = "ok";
}
echo json_encode($data);
?>