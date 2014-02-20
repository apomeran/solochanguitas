<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$data["estado"] = "";
$data["tabla"] = "";
$data["pag"] = "";
$sexo = array("", "F", "M");
$activo = array("N", "N", "S");
$order = array("", "usu.apellido asc, usu.nombre asc, usu.nacimiento asc", "usu.apellido desc, usu.nombre desc, usu.nacimiento desc", "usu.fecha asc", "usu.fecha desc");
// filtros
$filtrar = array();
if($_POST["sexo"] != -1)
    $filtrar[] = "usu.sexo = ".$bd->real_escape_string($_POST["sexo"]);
if($_POST["localidad2"] != -1)
    $filtrar[] = "usu.localidad = ".$bd->real_escape_string($_POST["localidad2"]);
if($_POST["estado"] != -1) {
    if($_POST["estado"] == 0)
        $filtrar[] = "usu.balance < 0";
    else
        $filtrar[] = "usu.balance > 0";
}
if($_POST["activo"] != -1) {
    if($_POST["activo"] < 2)
        $filtrar[] = "(usu.activo = '0' or usu.activo = '1')";
    else
        $filtrar[] = "usu.activo = '".$bd->real_escape_string($_POST["activo"])."'";
}
// buscar
$highlight = array();
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(usu.apellido like '%$buscar%' or usu.nombre like '%$buscar%' or usu.mail like '%$buscar%')";
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
$sql = "select usu.id, usu.nombre, usu.apellido, usu.sexo, usu.mail, usu.nacimiento, loc.localidad, usu.fecha, usu.activo, usu.balance, usu.nivel from usuarios as usu left join localidades as loc on usu.localidad = loc.id where usu.activo != '-1' $filtros";
$res = $bd->query($sql);
$total = $res->num_rows;
$sql .= " $ordenar limit $ini, $limit";

$data["sql"] = $sql;

if($res = $bd->query($sql))
    $data["estado"] = "ok";
if($total > 0) {
    while($fila = $res->fetch_assoc()) {
        $trClass = "";
        if($fila["activo"] < 2)
            $trClass = "error";
        if($fila["sexo"] == "")
            $fila["sexo"] = 0;
        if($fila["nacimiento"] == "0000")
            $fila["nacimiento"] = "";
        $balClass = "";
        if($fila["balance"] < 0)
            $balClass = "text-error";
        else if($fila["balance"] > 0)
            $balClass = "text-success";
        $data["tabla"] .= "<tr class='$trClass'>";
        $data["tabla"] .= "<td class='hl'>".$fila["apellido"]."</td>";
        $data["tabla"] .= "<td class='hl'>".$fila["nombre"]."</td>";
        $data["tabla"] .= "<td class='hl'>".$fila["mail"]."</td>";
        $data["tabla"] .= "<td class='text-center'>".$sexo[$fila["sexo"]]."</td>";
        $data["tabla"] .= "<td class='text-center'>".$fila["nacimiento"]."</td>";
        $data["tabla"] .= "<td>".$fila["localidad"]."</td>";
        $data["tabla"] .= "<td class='text-right $balClass'>$ ".$fila["balance"]."</td>";
        $data["tabla"] .= "<td>".$activo[$fila["activo"]]."</td>";
        $data["tabla"] .= "<td>".$f->convertirMuestra($fila["fecha"], "fecha")."</td>";
        if($fila["activo"] != "0" && $fila["nivel"] == "0")
            $botDel = "<button class='btn btn-borrar tooltip2' title='eliminar' data-id='".$fila["id"]."' data-tabla='usuarios'><i class='icon-trash'></i></button>";
        else
            $botDel = "<button class='btn disabled' disabled><i class='icon-trash'></i></button>";
        if($fila["nivel"] == "0")
            $botEdit = "<a class='btn tooltip2' title='modificar' href='".Sitio."/#/mi-perfil|".$fila["id"]."' target='_blank'><i class='icon-pencil'></i></a>";
        else
            $botEdit = "<button class='btn disabled' disabled><i class='icon-pencil'></i></button>";
        $data["tabla"] .= "<td class='acciones'><button class='btn btn-ver tooltip2' title='ver datos completos' data-id='".$fila["id"]."' data-tabla='usuarios'><i class='icon-eye-open'></i></button>$botEdit$botDel</td>";
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
    $data["pag"] .= "<p>Usuarios: <strong>$total</strong></p>";
}
else
    $data["tabla"] .= "<tr><td colspan='0'>No hay usuarios</td></tr>";
echo json_encode($data);
?>