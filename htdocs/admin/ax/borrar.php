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
$tablaOk = array("changuitas", "usuarios", "sugerencias", "denuncias", "preguntas", "respuesta", "categorias", "subcategorias");
if(!in_array($tabla, $tablaOk)) {
    echo json_encode($data);
    exit;
}
if($tabla == "respuesta")
    $sql = "update preguntas set respuesta = '', respuesta_fecha = '0000-00-00 00:00:00' where id = $id";
else
    $sql = "update $tabla set activo = '0' where id = $id";
if($bd->query($sql))
    $data["estado"] = "ok";
echo json_encode($data);
?>