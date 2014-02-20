<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$data["estado"] = "";
$data["tabla"] = "";
$data["pag"] = "";
// filtros
$filtrar = array();
if($_POST["changuita"] != -1)
    $filtrar[] = "pr.changuita = ".$bd->real_escape_string($_POST["changuita"]);
//
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
//
$sql = "select pr.id, pr.pregunta, pr.pregunta_fecha, pr.respuesta, pr.respuesta_fecha, pr.changuita, usu.nombre, usu.apellido, ch.titulo from preguntas as pr left join changuitas as ch on ch.id = pr.changuita left join usuarios as usu on pr.usuario = usu.id where pr.activo = '1' $filtros order by pregunta_fecha desc";
if($res = $bd->query($sql)) {
    $data["estado"] = "ok";
    while($fila = $res->fetch_assoc()) {
        $data["tabla"] .= "<tr>";
        $data["tabla"] .= "<td>".$fila["titulo"]."</td>";
        $data["tabla"] .= "<td>".nl2br($fila["pregunta"])."</td>";
        $data["tabla"] .= "<td>".$f->convertirMuestra($fila["pregunta_fecha"], "fecha")."</td>";
        $data["tabla"] .= "<td>".$fila["nombre"]." ".$fila["apellido"]."</td>";
        $botDel = "<button class='btn btn-borrar tooltip2' title='borrar pregunta y respuesta' data-id='".$fila["id"]."' data-tabla='preguntas'><i class='icon-trash'></i></button>";
        $botEdit = "<a href='editar-pregunta.php?id=".$fila["id"]."&ch=".$fila["changuita"]."' class='btn tooltip2' title='modificar pregunta'><i class='icon-pencil'></i></a>";
        $data["tabla"] .= "<td class='acciones'>".$botDel.$botEdit."</td>";
        $data["tabla"] .= "<td>".nl2br($fila["respuesta"])."</td>";
        $data["tabla"] .= "<td>".$f->convertirMuestra($fila["respuesta_fecha"], "fecha")."</td>";
        if($fila["respuesta"] != "") {
            $botDel = "<button class='btn btn-borrar tooltip2' title='borar respuesta' data-id='".$fila["id"]."' data-tabla='respuesta'><i class='icon-trash'></i></button>";
            $botEdit = "<a href='editar-respuesta.php?id=".$fila["id"]."&ch=".$fila["changuita"]."' class='btn tooltip2' title='modificar respuesta'><i class='icon-pencil'></i></a>";
        }
        else {
            $botDel = "<button class='btn disabled'><i class='icon-trash'></i></button>";
            $botEdit = "<button class='btn disabled'><i class='icon-pencil'></i></button>";
        }
        $data["tabla"] .= "<td class='acciones'>".$botDel.$botEdit."</td>";
        $data["tabla"] .= "</tr>";
    }
    // paginacion
    $data["pag"] .= "<p>Preguntas: <strong>".$res->num_rows."</strong></p>";
}
else
    $data["tabla"] .= "<tr><td colspan='0'>No hay changuitas</td></tr>";
echo json_encode($data);
?>