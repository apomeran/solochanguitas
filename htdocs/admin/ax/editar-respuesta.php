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
$ch = $bd->real_escape_string($_POST["ch"]);
$respuesta = trim($bd->real_escape_string($_POST["respuesta"]));
$data["link"] = "preguntas.php?id=$ch";
$sql = "update preguntas set respuesta = '$respuesta' where id = $id";
if($bd->query($sql))
    $data["estado"] = "ok";
echo json_encode($data);
?>