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
$_SESSION["mis-changuitas-filtros"] = $tipo;
$data["html"] = "<table class='tabla-vista table table-bordered table-hover'><thead><tr><th class='fecha2'>Fecha de publicaci&oacute;n</th><th class='estado'>Estado</th><th>Changuita</th><th class='postulantes'><i class='icon-user-blue'></i><a class='ayuda' title='Postulantes'><i class='icon-question-sign'></i></a></th><th class='preguntas'><i class='icon-comment-blue'></i><a class='ayuda' title='Preguntas'><i class='icon-question-sign'></i></a></th><th class='botones'></th></tr></thead><tbody>";
$filtro = "";
switch($tipo) {
	case 1:	//pendientes
		$filtro = "and ch.estado = '0'";
		break;
	case 2:	//en curso
		$filtro = "and ch.estado = '1'";
		break;
	case 3: //finalizadas
		$filtro = "and (ch.estado = '2' or ch.estado = '3')";
		break;
}
$sql = "select ch.id, ch.contratado, ch.titulo, cat.categoria, subcat.subcategoria, ch.fecha, ch.estado, ch.vencida from changuitas as ch left join categorias as cat on ch.categoria = cat.id left join subcategorias as subcat on ch.subcategoria = subcat.id where ch.usuario = ".$_SESSION[SesionId]." and ch.activo = '1' $filtro order by ch.fecha desc";
$res = $bd->query($sql);
if($res->num_rows == 0)
	$data["html"] .= "<tr><td colspan='0'>No hay ninguna</td></tr>";
else {
	while($fila = $res->fetch_assoc()) {
		// postulantes
		$sqlP = "select id from postulaciones where changuita = ".$fila["id"];
		$resP = $bd->query($sqlP);
		$nPostulantes = $resP->num_rows;
		// preguntas
		$sqlPr = "select id from preguntas where activo = '1' and changuita = ".$fila["id"];
		$resPr = $bd->query($sqlPr);
		$nPr = $resPr->num_rows;
		//
		$estado = "";
		$estadoDesc = "";
		$classFila = "";
		$botElegir = "";
		$botFin = "";
		$botBorrar = "";
		$botVer = "<button class='btn btn-block btn-success btn-vista-ver' data-changuita-id='".$fila["id"]."'>Ver changuita</button>";
		switch($fila["estado"]) {
			case 0:
				$estado = "Pendiente";
				$estadoDesc = "Todav&iacute;a no eligiste ning&uacute;n postulante para que haga la changuita.";
				if($nPostulantes > 0)
					$botElegir = "<button class='btn btn-block btn-warning btn-elegir' data-changuita-id='".$fila["id"]."'>Elegir postulante<div class='cargando hide'><img src='img/cargando.gif' alt='cargando'/></div></button>";
				$botBorrar = "<button class='btn btn-block btn-danger btn-borrar-ch' data-changuita-id='".$fila["id"]."'>Borrar changuita<div class='cargando hide'><img src='img/cargando.gif' alt='cargando'/></div></button>";
				if($fila["vencida"] == "1") {
					$botBorrar = "";
					$botElegir = "";
					$estado = "Vencida";
					$estadoDesc = "Se cumpli&oacute; el plazo m&aacute;ximo y la changuita venci&oacute;.";
				}
				break;
			case 1:
				$estado = "Changuita en curso";
				$classFila = "info";
				$botFin = "<button class='btn btn-block btn-primary btn-finalizar' data-changuita-id='".$fila["id"]."'>Finalizar changuita<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button>";
				break;
			case 2:
			case 3:
				$estado = "Changuita realizada";
				$classFila = "success";
				break;
		}
		$ayuda = "";
		if($estadoDesc != "")
			$ayuda = "<a class='ayuda' title='$estadoDesc'><i class='icon-question-sign'></i></a>";
		$data["html"] .= "<tr class='$classFila'><td>".$f->convertirMuestra($fila["fecha"], "fecha")."</td><td>$estado $ayuda</td><td><h6>".$fila["categoria"]." &gt; ".$fila["subcategoria"]."</h6><p class='ch'>".$fila["titulo"]."</p></td><td class='postulantes'>$nPostulantes</td><td class='preguntas'>$nPr</td><td class='botones'>$botVer$botElegir$botFin$botBorrar</td></tr>";
	}
}
$data["html"] .= "</tbody></table>";
echo json_encode($data);
?>