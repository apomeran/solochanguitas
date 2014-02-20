<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$data["estado"] = "";
$data["tabla"] = "";
$data["pag"] = "";
$order = array("", "s.fecha desc", "s.fecha asc");
// filtros
$filtrar = array();
if($_POST["visto"] != -1)
    $filtrar[] = "s.activo = '".$bd->real_escape_string($_POST["visto"])."'";
// buscar
$highlight = array();
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(s.sugerencia like '%$buscar%')";
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
$sql = "select s.id, s.sugerencia, s.fecha, s.activo, u.nombre, u.apellido from sugerencias as s left join usuarios as u on s.usuario = u.id where s.id > 0 $filtros";
$res = $bd->query($sql);
$total = $res->num_rows;
$sql .= " $ordenar limit $ini, $limit";
if($res = $bd->query($sql))
    $data["estado"] = "ok";
if($total > 0) {
    while($fila = $res->fetch_assoc()) {
        $trClass = "";
        if($fila["activo"] == "0")
            $trClass = "success";
        $data["tabla"] .= "<tr class='$trClass'>";
        $data["tabla"] .= "<td class='hl'>".$fila["sugerencia"]."</td>";
        $data["tabla"] .= "<td>".$fila["nombre"]." ".$fila["apellido"]."</td>";
        $data["tabla"] .= "<td>".$f->convertirMuestra($fila["fecha"], "fecha")."</td>";
        if($fila["activo"] == "1")
            $botDel = "<button class='btn btn-borrar tooltip2' title='marcar como visto' data-id='".$fila["id"]."' data-tabla='sugerencias'><i class='icon-check'></i></button>";
        else
            $botDel = "<button class='btn disabled' disabled><i class='icon-check'></i></button>";
        $data["tabla"] .= "<td class='acciones'>$botDel</td>";
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
    $data["pag"] .= "<p>Sugerencias: <strong>$total</strong></p>";
}
else
    $data["tabla"] .= "<tr><td colspan='0'>No hay sugerencias</td></tr>";
echo json_encode($data);
?>