<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../class/seguridad.php");
$s = new Seguridad;
$s->permitir(0);
$bd = conectar();
include_once("../class/funciones.php");
$f = new Funciones();
$tipo = 0;
if(isset($_POST["tipo"]))
	$tipo = $bd->real_escape_string($_POST["tipo"]);
$_SESSION["preguntas-filtros"] = $tipo;
$data["html"] = "<table class='tabla-vista tabla-preguntas table table-bordered table-hover'><thead><tr><th>Pregunta</th><th>Respuesta</th><th>Changuita</th><th class='botones'></th></tr></thead><tbody>";
$filtro = "";
switch($tipo) {
	case 1:	//preguntas pendientes
		$filtro = "and pr.respuesta = '' and ch.estado = '0'";
		break;
	case 2:	//respondidas
		$filtro = "and pr.respuesta != ''";
		break;
}
$sql = "select pr.changuita, pr.pregunta, pr.respuesta, pr.pregunta_fecha, pr.respuesta_fecha, pr.usuario, ch.titulo, ch.fecha, cat.categoria, subcat.subcategoria, ch.estado, ch.vencida, usu.nombre, usu.apellido from preguntas as pr left join changuitas as ch on pr.changuita = ch.id left join usuarios as usu on pr.usuario = usu.id left join categorias as cat on ch.categoria = cat.id left join subcategorias as subcat on ch.subcategoria = subcat.id where ch.usuario = ".$_SESSION[SesionId]." and ch.activo = '1' $filtro order by ch.fecha desc";
$res = $bd->query($sql);
if($res->num_rows == 0)
	$data["html"] .= "<tr><td colspan='0'>No hay ninguna</td></tr>";
else {
	while($fila = $res->fetch_assoc()) {
		$classFila = "";
		$txtBot = "Ver changuita";
		$classBot = "success";
		if($fila["estado"] == 0 && $fila["respuesta"] == "" && $fila["vencida"] == 0) {
			$txtBot = "Responder";
			$classBot = "warning";
			$classFila = "error";
		}
		else if($fila["respuesta"] != "")
			$classFila = "success";
		else if($fila["vencida"] == 0) {
			$txtBot = "Ver changuita";
			$classBot = "success";
		}
		$respuesta = "<em>La pregunta qued&oacute; sin responder</em>";
		if($fila["respuesta"] != "")
			$respuesta = $fila["respuesta"]."<br/><span>".$f->convertirMuestra($fila["respuesta_fecha"], "fecha")."</span>";
		else if($fila["estado"] == 0 && $fila["vencida"] == 0)
			$respuesta = "<em>Todav&iacute;a no hay respuesta</em>";
		$data["html"] .= "<tr class='$classFila'><td>".$fila["pregunta"]."<br/><span><strong>".$fila["nombre"]." ".substr($fila["apellido"], 0, 1).".</strong> (".$f->convertirMuestra($fila["pregunta_fecha"], "fecha").")</span></td><td>$respuesta</td><td><h6>".$fila["categoria"]." &gt; ".$fila["subcategoria"]."</h6><p class='ch'>".$fila["titulo"]."</p></td><td class='botones'><button class='btn btn-block btn-$classBot btn-vista-ver' data-changuita-id='".$fila["changuita"]."'>$txtBot</button></td></tr>";
	}
}
$data["html"] .= "</tbody></table>";
echo json_encode($data);
?>