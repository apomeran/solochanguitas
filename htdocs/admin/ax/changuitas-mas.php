<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$data["estado"] = "";
$data["tabla"] = "";
$id = $bd->real_escape_string($_POST["id"]);
$sql = "select ch.id, usu.nombre, usu.apellido, loc.localidad, ch.contratado, ch.fecha, ch.titulo, cat.categoria, scat.subcategoria, bar.barrio, ch.descripcion, ch.cuando, ch.cuando_dias, ch.cuando_fecha, ch.cuando_hora_desde, ch.cuando_hora_hasta, ch.precio, ch.plan, ch.vencida, ch.pagado, ch.estado, ch.fecha_contratacion, ch.fee, ch.activo from changuitas as ch left join usuarios as usu on ch.usuario = usu.id left join localidades as loc on ch.localidad = loc.id left join barrios as bar on ch.barrio = bar.id left join categorias as cat on ch.categoria = cat.id left join subcategorias as scat on ch.subcategoria = scat.id where ch.id = $id";
$res = $bd->query($sql);
if($res->num_rows == 0) {
    echo json_encode($data);
    exit;
}
$fila = $res->fetch_assoc();
$data["estado"] = "ok";
$sql = "select id from postulaciones where changuita = $id";
$res = $bd->query($sql);
$nP = $res->num_rows;
$sql = "select id from preguntas where changuita = $id and activo = '1'";
$res = $bd->query($sql);
$nPr = $res->num_rows;
$sql = "select id, hecha from calificaciones where changuita = $id and activo = '1'";
$res = $bd->query($sql);
$noHecha = 0;
while($filaCal = $res->fetch_assoc()) {
    if($filaCal["hecha"] == "1")
        $noHecha++;
}
$noR = array("No", "1 usuario", "Ambos usuarios");
$planes = array();
$sql = "select id, plan from planes where activo = '1'";
$res = $bd->query($sql);
while($filaPl = $res->fetch_assoc())
    $planes[$filaPl["id"]] = $filaPl["plan"];
$palabras = array();
$sql = "select palabra from changuitas_palabras where changuita = $id";
$res = $bd->query($sql);
while($filaPc = $res->fetch_assoc())
    $palabras[] = $filaPc["palabra"];
$pagadoClass = "";
if($fila["pagado"] == "0")
    $pagadoClass = "text-error";
$feeClass = "";
if($fila["fee"] == "0")
    $feeClass = "text-error";
$contratado = "";
if($fila["contratado"] > 0) {
    $sql = "select nombre, apellido from usuarios where id = ".$fila["contratado"];
    $res = $bd->query($sql);
    $filaC = $res->fetch_assoc();
    $contratado = $filaC["nombre"]." ".$filaC["apellido"];
}
if($contratado != "")
    $contratado .= " (".$f->convertirMuestra($fila["fecha_contratacion"], "fecha").")";
if($fila["subcategoria"] != "")
    $fila["categoria"] .= " > ".$fila["subcategoria"];
if($fila["barrio"] != "")
    $fila["localidad"] .= " > ".$fila["barrio"];
if($fila["activo"] == "0")
    $fila["estado"] = "4";
$sn = array("No", "Sí");
$estados = array("Publicada", "En curso", "Realizada", "Realizada y calificada", "Borrada");
$fee = array("No", "Sí", "No se hizo la changuita");
$cuando = "";
if($fila["cuando"] == 1)
    $cuando = "En cualquier momento, a combinar";
else if($fila["cuando"] == 2) {
    $cuandoDias = explode(",", $fila["cuando_dias"]);
    $cuandoDia = array();
    foreach ($cuandoDias as $v)
        $cuandoDia[] = $dias[$v];
    $cuando = implode(", ", $cuandoDia);
}
else if($fila["cuando"] == 3)
    $cuando = $f->convertirMuestra($fila["cuando_fecha"], "fecha");
$horario = "";
if($fila["cuando_hora_desde"] != "00:00:00")
    $horario = "Entre las ".substr($fila["cuando_hora_desde"], 0, 5)." hs y las ".substr($fila["cuando_hora_hasta"], 0, 5)." hs";
$data["tabla"] .= "<h4>".$fila["titulo"]."</h4>";
$data["tabla"] .= "<table class='table table-condensed table-hover vista-mas'><tbody>";
$data["tabla"] .= "<tr><td>Usuario</td><td class='dato'>".$fila["nombre"]." ".$fila["apellido"]."</td></tr>";
$data["tabla"] .= "<tr><td>Contratado</td><td class='dato'>".$contratado."</td></tr>";
$data["tabla"] .= "<tr><td>Categoría</td><td class='dato'>".$fila["categoria"]."</td></tr>";
$data["tabla"] .= "<tr><td>Zona</td><td class='dato'>".$fila["localidad"]."</td></tr>";
$data["tabla"] .= "<tr><td>Descripción</td><td class='dato'>".nl2br($fila["descripcion"])."</td></tr>";
$data["tabla"] .= "<tr><td>Palabras clave</td><td class='dato'>".implode(", ", $palabras)."</td></tr>";
$data["tabla"] .= "<tr><td>A realizar</td><td class='dato'>".$cuando."</td></tr>";
$data["tabla"] .= "<tr><td>Horario</td><td class='dato'>".$horario."</td></tr>";
$data["tabla"] .= "<tr><td>Precio</td><td class='dato'>$".sprintf("%01.2f", $fila["precio"])."</td></tr>";
$data["tabla"] .= "<tr><td>Plan</td><td class='dato'>".$planes[$fila["plan"]]."</td></tr>";
$data["tabla"] .= "<tr><td>Fecha</td><td class='dato'>".$f->convertirMuestra($fila["fecha"], "fecha")."</td></tr>";
$data["tabla"] .= "<tr><td>Estado</td><td class='dato'>".$estados[$fila["estado"]]."</td></tr>";
if($fila["plan"] > 1)
    $data["tabla"] .= "<tr><td>Pagada</td><td class='dato $pagadoClass'>".$sn[$fila["pagado"]]."</td></tr>";
else
    $data["tabla"] .= "<tr><td>Pagada</td><td class='dato $pagadoClass'>Gratis</td></tr>";
if($fila["estado"] != "0" && $fila["estado"] != "4")
    $data["tabla"] .= "<tr><td>Fee pagado</td><td class='dato $feeClass'>".$fee[$fila["fee"]]."</td></tr>";
else if($fila["estado"] == "0")
    $data["tabla"] .= "<tr><td>Vencida</td><td class='dato'>".$sn[$fila["vencida"]]."</td></tr>";
$data["tabla"] .= "<tr><td>Preguntas realizadas</td><td class='dato'>".$nPr."</td></tr>";
$data["tabla"] .= "<tr><td>Postulaciones</td><td class='dato'>".$nP."</td></tr>";
if($fila["estado"] != "0" && $fila["estado"] != "4")
    $data["tabla"] .= "<tr><td>Calificada como no realizada</td><td class='dato'>".$noR[$noHecha]."</td></tr>";
$data["tabla"] .= "</tbody></table>";
echo json_encode($data);
?>