<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$data["estado"] = "";
$data["tabla"] = "";
$id = $bd->real_escape_string($_POST["id"]);
$sexo = array("", "F", "M");
$activo = array("N", "N", "S");
$educacion = array("", "Primario completo", "Primario incompleto", "Primario en curso", "Secundario completo", "Secundario incompleto", "Secundario en curso", "Terciario completo", "Terciario incompleto", "Terciario en curso", "Universitario completo", "Universitario incompleto", "Universitario en curso");
$sql = "select usu.id, usu.nombre, usu.apellido, usu.sexo, usu.mail, usu.nacimiento, usu.dni, loc.localidad, bar.barrio, usu.celular_area, usu.celular, usu.educacion, usu.institucion, usu.presentacion, usu.perfil_fb, usu.perfil_li, usu.perfil_gp, usu.fecha, usu.activo, usu.balance from usuarios as usu left join localidades as loc on usu.localidad = loc.id left join barrios as bar on usu.barrio = bar.id where usu.id = $id";
$res = $bd->query($sql);
if($res->num_rows == 0) {
    echo json_encode($data);
    exit;
}
$fila = $res->fetch_assoc();
$sql = "select calificacion, n from calificacion where usuario = $id";
$res = $bd->query($sql);
$filaCal = $res->fetch_assoc();
$sql = "select confianza from confianza where usuario = $id";
$res = $bd->query($sql);
$filaCon = $res->fetch_assoc();
$sql = "select id from changuitas where usuario = $id and activo = '1'";
$res = $bd->query($sql);
$nCU = $res->num_rows;
$sql = "select id from changuitas where contratado = $id and activo = '1'";
$res = $bd->query($sql);
$nCC = $res->num_rows;
$sql = "select id from postulaciones where usuario = $id";
$res = $bd->query($sql);
$nP = $res->num_rows;
$sql = "select id from preguntas where usuario = $id and activo = '1'";
$res = $bd->query($sql);
$nPr = $res->num_rows;
$data["estado"] = "ok";
$activoClass = "";
if($fila["activo"] < 2)
    $activoClass = "text-error";
if($fila["sexo"] == "")
    $fila["sexo"] = 0;
if($fila["nacimiento"] == "0000")
    $fila["nacimiento"] = "";
$balClass = "";
if($fila["balance"] < 0)
    $balClass = "text-error";
else if($fila["balance"] > 0)
    $balClass = "text-success";
if($fila["barrio"] != "")
    $fila["localidad"] .= " > ".$fila["barrio"];
if($fila["dni"] == 0)
    $fila["dni"] = "";
$celular = "";
if(trim($fila["celular_area"]) != "")
    $celular .= trim($fila["celular_area"]);
if(trim($fila["celular"]) != "")
    $celular .= " ".trim($fila["celular"]);
$celular = trim($celular);
if($filaCal["n"] > 0)
    $cal = $f->indicador($filaCal["calificacion"], "calificacion")." <small>(".$filaCal["n"]." recibidas)</small>";
else
    $cal = $f->indicador(-1, "calificacion");
if($fila["perfil_fb"] != "")
    $fila["perfil_fb"] = "<a href='http://".$fila["perfil_fb"]."' target='_blank'>http://".$fila["perfil_fb"]."</a>";
if($fila["perfil_li"] != "")
    $fila["perfil_li"] = "<a href='http://".$fila["perfil_li"]."' target='_blank'>http://".$fila["perfil_li"]."</a>";
if($fila["perfil_gp"] != "")
    $fila["perfil_gp"] = "<a href='http://".$fila["perfil_gp"]."' target='_blank'>http://".$fila["perfil_gp"]."</a>";

$data["tabla"] .= "<h4>".$fila["nombre"]." ".$fila["apellido"]."</h4>";
$data["tabla"] .= "<table class='table table-condensed table-hover vista-mas'><tbody>";
$data["tabla"] .= "<tr><td>DNI</td><td class='dato'>".$fila["dni"]."</td></tr>";
$data["tabla"] .= "<tr><td>Sexo</td><td class='dato'>".$sexo[$fila["sexo"]]."</td></tr>";
$data["tabla"] .= "<tr><td>Año de nacimiento</td><td class='dato'>".$fila["nacimiento"]."</td></tr>";
$data["tabla"] .= "<tr><td>Zona</td><td class='dato'>".$fila["localidad"]."</td></tr>";
$data["tabla"] .= "<tr><td>E-mail</td><td class='dato'>".$fila["mail"]."</td></tr>";
$data["tabla"] .= "<tr><td>Celular</td><td class='dato'>".$celular."</td></tr>";
$data["tabla"] .= "<tr><td>Educación</td><td class='dato'>".$educacion[$fila["educacion"]]."</td></tr>";
$data["tabla"] .= "<tr><td>Institución</td><td class='dato'>".$fila["institucion"]."</td></tr>";
$data["tabla"] .= "<tr><td>Presentación</td><td class='dato'>".nl2br($fila["presentacion"])."</td></tr>";
$data["tabla"] .= "<tr><td>Perfil Facebook</td><td class='dato'>".$fila["perfil_fb"]."</td></tr>";
$data["tabla"] .= "<tr><td>Perfil LinkedIn</td><td class='dato'>".$fila["perfil_li"]."</td></tr>";
$data["tabla"] .= "<tr><td>Perfil Google+</td><td class='dato'>".$fila["perfil_gp"]."</td></tr>";
$data["tabla"] .= "<tr><td>Activo</td><td class='dato $activoClass'>".$activo[$fila["activo"]]."</td></tr>";
$data["tabla"] .= "<tr><td>Estado</td><td class='dato $balClass'>$ ".$fila["balance"]."</td></tr>";
$data["tabla"] .= "<tr><td>Fecha de alta</td><td class='dato'>".$f->convertirMuestra($fila["fecha"], "fecha")."</td></tr>";
$data["tabla"] .= "<tr><td>Calificación</td><td class='dato'>".$cal."</td></tr>";
$data["tabla"] .= "<tr><td>Contactos en la red</td><td class='dato'>".$filaCon["confianza"]."</td></tr>";
$data["tabla"] .= "<tr><td>Changuitas publicadas</td><td class='dato'>".$nCU."</td></tr>";
$data["tabla"] .= "<tr><td>Changuitas realizadas*</td><td class='dato'>".$nCC."</td></tr>";
$data["tabla"] .= "<tr><td>Preguntas realizadas</td><td class='dato'>".$nPr."</td></tr>";
$data["tabla"] .= "<tr><td>Postulaciones</td><td class='dato'>".$nP."</td></tr>";
$data["tabla"] .= "<tr><td colspan='2'><small>* Indica la cantidad de changuitas para las que fue contratado, independientemente de si se realizaron o no</small></td></tr>";
$data["tabla"] .= "</tbody></table>";
echo json_encode($data);
?>