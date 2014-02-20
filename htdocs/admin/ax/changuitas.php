<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$data["estado"] = "";
$data["tabla"] = "";
$data["pag"] = "";
$order = array("", "ch.fecha desc", "ch.fecha asc");
$planes = array();
$estados = array("Publicada", "En curso", "Realizada", "Realizada y calificada", "Borrada", "Vencida");
$sql = "select id, plan from planes where activo = '1'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc())
    $planes[$fila["id"]] = $fila["plan"];
// filtros
$filtrar = array();
if($_POST["categoria"] != -1) {
    $filtrar[] = "ch.categoria = ".$bd->real_escape_string($_POST["categoria"]);
    if(isset($_POST["subcategoria"]) && $_POST["subcategoria"] != -1)
        $filtrar[] = "ch.subcategoria = ".$bd->real_escape_string($_POST["subcategoria"]);
}
if($_POST["localidad"] != -1) {
    $filtrar[] = "ch.localidad = ".$bd->real_escape_string($_POST["localidad"]);
    if(isset($_POST["barrio"]) && $_POST["barrio"] != -1)
        $filtrar[] = "ch.barrio = ".$bd->real_escape_string($_POST["barrio"]);
}
if($_POST["estado"] != -1) {
    if($_POST["estado"] < 4)
        $filtrar[] = "(ch.estado = '".$bd->real_escape_string($_POST["estado"])."' and ch.activo = '1')";
    else
        $filtrar[] = "ch.activo = '0'";
}
if($_POST["plan"] != -1)
    $filtrar[] = "ch.plan = '".$bd->real_escape_string($_POST["plan"])."'";
if($_POST["pagado"] != -1)
    $filtrar[] = "ch.pagado = '".$bd->real_escape_string($_POST["pagado"])."'";
if($_POST["fee"] != -1) {
    if($_POST["fee"] == "0")
        $filtrar[] = "(ch.fee = '0' and (ch.estado = '2' || ch.estado = '3'))";
    else
        $filtrar[] = "ch.fee = '".$bd->real_escape_string($_POST["fee"])."'";
}
if($_POST["vencida"] != -1)
    $filtrar[] = "ch.vencida = '".$bd->real_escape_string($_POST["vencida"])."'";
// buscar
$highlight = array();
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(ch.titulo like '%$buscar%' or ch.descripcion like '%$buscar%' or usu.nombre like '%$buscar%' or usu.apellido like '%$buscar%')";
    $highlight[] = "'".$buscar."'";
}
//
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
$sql = "select ch.id, usu.nombre, usu.apellido, loc.localidad, ch.fecha, ch.titulo, cat.categoria, scat.subcategoria, bar.barrio, ch.precio, ch.plan, ch.vencida, ch.pagado, ch.fee, ch.estado, ch.activo from changuitas as ch left join usuarios as usu on ch.usuario = usu.id left join localidades as loc on ch.localidad = loc.id left join barrios as bar on ch.barrio = bar.id left join categorias as cat on ch.categoria = cat.id left join subcategorias as scat on ch.subcategoria = scat.id where ch.id != 0 $filtros";
$res = $bd->query($sql);
$total = $res->num_rows;
$sql .= " $ordenar limit $ini, $limit";
if($res = $bd->query($sql))
    $data["estado"] = "ok";
if($total > 0) {
    while($fila = $res->fetch_assoc()) {
        $trClass = "";
        if($fila["pagado"] == "0")
            $trClass = "error";
        else if($fila["vencida"] == "1")
            $trClass = "gris";
        else if($fila["fee"] == "0" && $fila["estado"] > 1)
            $trClass = "warning";
        if($fila["activo"] == "0") {
            $trClass .= " deleted";
            $fila["estado"] = 4;
        }
        if($fila["vencida"] == "1")
            $fila["estado"] = 5;
        $sqlPr = "select id from preguntas where changuita = ".$fila["id"]." and activo = '1'";
        $resPr = $bd->query($sqlPr);
        $nPr = $resPr->num_rows;
        $data["tabla"] .= "<tr class='$trClass'>";
        $data["tabla"] .= "<td class='hl'>".$fila["titulo"]."</td>";
        $data["tabla"] .= "<td>".$fila["categoria"]."</td>";
        $data["tabla"] .= "<td>".$fila["subcategoria"]."</td>";
        $data["tabla"] .= "<td>".$estados[$fila["estado"]]."</td>";
        $data["tabla"] .= "<td class='hl'>".$fila["nombre"]." ".$fila["apellido"]."</td>";
        $data["tabla"] .= "<td>$".sprintf("%01.2f", $fila["precio"])."</td>";
        $data["tabla"] .= "<td>".$planes[$fila["plan"]]."</td>";
        $data["tabla"] .= "<td>".$f->convertirMuestra($fila["fecha"], "fecha")."</td>";
        $classPr = "";
        $botPr = "<button class='btn disabled' disabled><i class='icon-plus-sign'></i></button>";
        if($nPr > 0) {
            $botPr = "<a href='preguntas.php?id=".$fila["id"]."' class='btn tooltip2' title='ver preguntas y respuestas'><i class='icon-plus-sign'></i></a>";
            $classPr = "badge-success";
        }
        $data["tabla"] .= "<td class='acciones preguntas'><span class='badge $classPr'>$nPr</span> $botPr</td>";
        if(/*($fila["estado"] == "0" || $fila["estado"] == 5) && */$fila["activo"] == "1")
            $botDel = "<button class='btn btn-borrar tooltip2' title='eliminar' data-id='".$fila["id"]."' data-tabla='changuitas'><i class='icon-trash'></i></button>";
        else
            $botDel = "<button class='btn disabled' disabled><i class='icon-trash'></i></button>";
        if(/*$fila["activo"] == "1" && */($fila["pagado"] == "0" || ($fila["fee"] == "0" && $fila["estado"] > 1 && $fila["estado"] < 4)))
            $botInf = "<button class='btn btn-pagar tooltip2' title='informar pago' data-id='".$fila["id"]."'><i class='icon-info-sign'></i></button>";
        else
            $botInf = "<button class='btn disabled' disabled><i class='icon-info-sign'></i></button>";
        $data["tabla"] .= "<td class='acciones'><button class='btn btn-ver tooltip2' title='ver datos completos' data-id='".$fila["id"]."' data-tabla='changuitas'><i class='icon-eye-open'></i></button>".$botDel.$botInf."</td>";
        $data["tabla"] .= "</tr>";
        $data["tabla"] .= "<script>$('.hl').unhighlight; $('.hl').highlight([".implode(", ", $highlight)."]);</script>";
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
    $data["pag"] .= "<p>Changuitas: <strong>$total</strong></p>";
}
else
    $data["tabla"] .= "<tr><td colspan='0'>No hay changuitas</td></tr>";
echo json_encode($data);
?>