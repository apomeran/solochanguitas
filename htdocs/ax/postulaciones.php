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
$_SESSION["postulaciones-filtros"] = $tipo;
$data["html"] = "<table class='tabla-vista table table-bordered table-hover'><thead><tr><th class='fecha'>Fecha de postulaci&oacute;n</th><th class='estado'>Estado</th><th>Changuita</th><th class='botones'></th></tr></thead><tbody>";
$filtro = "";
switch($tipo) {
	case 1:	//pendientes
		$filtro = "and ch.estado = '0'";
		break;
	case 2:	//en curso
		$filtro = "and ch.estado = '1' and ch.contratado = ".$_SESSION[SesionId];
		break;
	case 3: //finalizadas
		$filtro = "and (ch.estado = '2' or ch.estado = '3') and ch.contratado = ".$_SESSION[SesionId];
		break;
	case 4:	//rechazadas
		$filtro = "and ch.estado != '0' and ch.contratado != ".$_SESSION[SesionId];
		break;
}
$sql = "select pos.changuita, pos.fecha, ch.contratado, ch.titulo, cat.categoria, subcat.subcategoria, ch.estado, ch.vencida from postulaciones as pos left join changuitas as ch on pos.changuita = ch.id left join categorias as cat on ch.categoria = cat.id left join subcategorias as subcat on ch.subcategoria = subcat.id where pos.usuario = ".$_SESSION[SesionId]." and ch.activo = '1' $filtro order by pos.fecha desc";
$res = $bd->query($sql);
if($res->num_rows == 0)
	$data["html"] .= "<tr><td colspan='0'>No hay ninguna</td></tr>";
else {
	while($fila = $res->fetch_assoc()) {
		$estado = "";
		$estadoDesc = "";
		$classFila = "";
		$botAnular = "";
		if($fila["estado"] == 0 && $fila["vencida"] == 0) {
			$estado = "Postulaci&oacute;n pendiente";
			$estadoDesc = "El usuario que public&oacute; la changuita todav&iacute;a no eligi&oacute; a ning&uacute;n postulante.";
			$botAnular = "<button class='btn btn-block btn-warning btn-anular-postulacion' data-changuita-id='".$fila["changuita"]."'>Anular postulaci&oacute;n<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button>";
		}
		else if($fila["estado"] == 1 && $fila["contratado"] == $_SESSION[SesionId]) {
			$estado = "Changuita en curso";
			$classFila = "info";
		}
		else if(($fila["estado"] == 2 || $fila["estado"] == 3) && $fila["contratado"] == $_SESSION[SesionId]) {
			$estado = "Changuita realizada";
			$classFila = "success";
		}
		else {
			$estado = "Postulaci&oacute;n rechazada";
			$estadoDesc = "El usuario que public&oacute; la changuita eligi&oacute; a otro postulante, o no eligi&oacute; a nadie.";
			$classFila = "error";
		}
		$ayuda = "";
		if($estadoDesc != "")
			$ayuda = "<a class='ayuda' title='$estadoDesc'><i class='icon-question-sign'></i></a>";
		$data["html"] .= "<tr class='$classFila'><td>".$f->convertirMuestra($fila["fecha"], "fecha")."</td><td>$estado $ayuda</td><td><h6>".$fila["categoria"]." &gt; ".$fila["subcategoria"]."</h6><p class='ch'>".$fila["titulo"]."</p></td><td class='botones'><button class='btn btn-block btn-success btn-vista-ver' data-changuita-id='".$fila["changuita"]."'>Ver changuita</button>$botAnular</td></tr>";
	}
}
$data["html"] .= "</tbody></table>";
echo json_encode($data);
?>