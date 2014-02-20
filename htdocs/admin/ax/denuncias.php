<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$data["estado"] = "";
$data["tabla"] = "";
$data["pag"] = "";
$order = array("", "d.fecha desc", "d.fecha asc");
// filtros
$filtrar = array();
if($_POST["tipo"] != "-1")
    $filtrar[] = "d.tipo = '".$bd->real_escape_string($_POST["tipo"])."'";
if($_POST["visto"] != -1)
    $filtrar[] = "d.activo = '".$bd->real_escape_string($_POST["visto"])."'";
$orden = $bd->real_escape_string($_POST["orden"]);
$ordenar = "order by ".$order[$orden];
if(isset($_POST["ini"]))
    $ini = $bd->real_escape_string($_POST["ini"]);
else $ini = 0;
$limit = $bd->real_escape_string($_POST["limit"]);
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
//
$sql = "select d.id, d.tipo, d.fecha, u.nombre, u.apellido, d.comentario, d.i, d.activo from denuncias as d left join usuarios as u on d.usuario = u.id where d.id > 0 $filtros";
$res = $bd->query($sql);
$total = $res->num_rows;
$sql .= " $ordenar limit $ini, $limit";
if($res = $bd->query($sql))
    $data["estado"] = "ok";
if($total > 0) {
    while($fila = $res->fetch_assoc()) {
        switch ($fila["tipo"]) {
            case 'u':
                $tipo = "Usuario";
                $sql2 = "select id, nombre, apellido from usuarios where id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = $fila2["nombre"]." ".$fila2["apellido"];
                break;
            case 'ch':
                $tipo = "Changuita";
                $sql2 = "select id, titulo from changuitas where id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = $fila2["titulo"];
                break;
            case 'p':
                $tipo = "Pregunta";
                $sql2 = "select ch.id, p.pregunta, ch.titulo from preguntas as p left join changuitas as ch on p.changuita = ch.id where p.id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = "Changuita: <strong>".$fila2["titulo"]."</strong><br/>Pregunta: <em>".$fila2["pregunta"]."</em>";
                break;
            case 'r':
                $tipo = "Respuesta";
                $sql2 = "select ch.id, p.respuesta, ch.titulo from preguntas as p left join changuitas as ch on p.changuita = ch.id where p.id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = "Changuita: <strong>".$fila2["titulo"]."</strong><br/>Respuesta: <em>".$fila2["respuesta"]."</em>";
                break;
        }
        $data["tabla"] .= "<tr>";
        $data["tabla"] .= "<td>".$tipo."</td>";
        $data["tabla"] .= "<td>".$dato."</td>";
        $data["tabla"] .= "<td>".$fila["comentario"]."</td>";
        $data["tabla"] .= "<td>".$fila["nombre"]." ".$fila["apellido"]."</td>";
        $data["tabla"] .= "<td>".$f->convertirMuestra($fila["fecha"], "fecha")."</td>";
        if($fila["activo"] == "1")
            $botDel = "<button class='btn btn-borrar tooltip2' title='marcar como visto' data-id='".$fila["id"]."' data-tabla='denuncias'><i class='icon-check'></i></button>";
        else
            $botDel = "<button class='btn disabled' disabled><i class='icon-check'></i></button>";
        $data["tabla"] .= "<td class='acciones'>$botDel</td>";
        $data["tabla"] .= "</tr>";
    }
    // paginacion
    $pags = ceil($total/$limit);
    $ult = $pags - 1;
    $classPri = "";
    $classUlt = "";
    if($ini == 0)
        $classPri = "disabled";
    if($ini == $ult*$limit)
        $classUlt = "disabled";
    $data["pag"] .= "<a href='#' class='btn pag-pri $classPri'><i class='icon-step-backward'></i></a> ";
    $data["pag"] .= "<a href='#' class='btn pag-ant $classPri'><i class='icon-chevron-left'></i></a> ";
    $data["pag"] .= "<select name='ini' id='ini' class='input-mini'>";
    for($i=0;$i<$pags;$i++) {
        $sel = "";
        if($ini/$limit == $i)
            $sel = "selected";
        $data["pag"] .= "<option value='".($i*$limit)."' $sel>".($i+1)."</option>";
    }
    $data["pag"] .= "</select> ";
    $data["pag"] .= "<a href='#' class='btn pag-sig $classUlt'><i class='icon-chevron-right'></i></a> ";
    $data["pag"] .= "<a href='#' class='btn pag-ult $classUlt'><i class='icon-step-forward'></i></a>";
    if($pags == 1)
        $data["pag"] = "";
    $data["pag"] .= "<p>Denuncias: <strong>$total</strong></p>";
}
else
    $data["tabla"] .= "<tr><td colspan='0'>No hay denuncias</td></tr>";
echo json_encode($data);
?>