<?php
include("../../includes/config.php");
$bd = conectar();
$data["estado"] = "";
$data["tabla"] = "";
$data["pag"] = "";
$order = array("", "orden asc", "orden desc", "categoria asc", "categoria desc");
// filtros
$filtrar = array();
// buscar
$highlight = array();
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(categoria like '%$buscar%')";
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
$sql = "select id, categoria, orden from categorias where activo = '1' $filtros";
$res = $bd->query($sql);
$total = $res->num_rows;
$sql .= " $ordenar limit $ini, $limit";
if($res = $bd->query($sql))
    $data["estado"] = "ok";
$sqlO = "select MIN(orden) as mn, MAX(orden) as mx from categorias where activo = '1'";
$resO = $bd->query($sqlO);
$filaO = $resO->fetch_assoc();
$min = $filaO["mn"];
$max = $filaO["mx"];
if($total > 0) {
    while($fila = $res->fetch_assoc()) {
        $data["tabla"] .= "<tr>";
        $data["tabla"] .= "<td class='hl'>".$fila["categoria"]."</td>";
        $data["tabla"] .= "<td>".$fila["orden"]."</td>";
        $sqlS = "select id from subcategorias where categoria = ".$fila["id"]." and activo = '1'";
        $resS = $bd->query($sqlS);
        $nS = $resS->num_rows;
        if($nS == 0)
            $botDel = "<button class='btn btn-borrar tooltip2' title='eliminar' data-id='".$fila["id"]."' data-tabla='categorias'><i class='icon-trash'></i></button>";
        else
            $botDel = "<button class='btn disabled' disabled><i class='icon-trash'></i></button>";
        $botEdit = "<a class='btn tooltip2' title='modificar' href='editar-categoria.php?id=".$fila["id"]."'><i class='icon-pencil'></i></a>";
        if($fila["orden"] > $min)
            $botSubir = "<button class='btn btn-subir tooltip2' title='subir' data-id='".$fila["id"]."' data-tabla='categorias'><i class='icon-chevron-up'></i></button>";
        else
            $botSubir = "<button class='btn disabled' disabled><i class='icon-chevron-up'></i></button>";
        if($fila["orden"] < $max)
            $botBajar = "<button class='btn btn-bajar tooltip2' title='bajar' data-id='".$fila["id"]."' data-tabla='categorias'><i class='icon-chevron-down'></i></button>";
        else
            $botBajar = "<button class='btn disabled' disabled><i class='icon-chevron-down'></i></button>";
        $classS = "";
        $botS = "<button class='btn disabled' disabled><i class='icon-plus-sign'></i></button>";
        if($nS > 0) {
            $botS = "<a href='subcategorias.php?id=".$fila["id"]."' class='btn tooltip2' title='ver subcategorías'><i class='icon-plus-sign'></i></a>";
            $classS = "badge-success";
        }
        $data["tabla"] .= "<td class='acciones subcategorias'><span class='badge $classS'>$nS</span> $botS</td>";
        $data["tabla"] .= "<td class='acciones'>$botEdit$botDel$botSubir$botBajar</td>";
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
    $data["pag"] .= "<p>Categorías: <strong>$total</strong></p>";
}
else
    $data["tabla"] .= "<tr><td colspan='0'>No hay categorías</td></tr>";
echo json_encode($data);
?>