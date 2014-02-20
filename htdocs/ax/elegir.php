<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
if(!isset($_POST["id"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("../class/funciones.php");
$f = new Funciones();
$bd = conectar();
$id = $bd->real_escape_string($_POST["id"]);
$data["html"] = "";
$educacion = array(1=>"Primario completo", "Primario incompleto", "Primario en curso", "Secundario completo", "Secundario incompleto", "Secundario en curso", "Terciario completo", "Terciario incompleto", "Terciario en curso", "Universitario completo", "Universitario incompleto", "Universitario en curso");
$sql = "select pos.usuario, usu.nombre, usu.apellido, usu.mail, loc.localidad, bar.barrio, usu.educacion, usu.presentacion, usu.perfil_fb, usu.perfil_li, usu.perfil_gp, cal.calificacion, cal.n, con.confianza from postulaciones as pos left join usuarios as usu on pos.usuario = usu.id left join changuitas as ch on pos.changuita = ch.id left join calificacion as cal on pos.usuario = cal.usuario left join confianza as con on pos.usuario = con.usuario left join localidades as loc on usu.localidad = loc.id left join barrios as bar on usu.barrio = bar.id where pos.changuita = $id and usu.activo = '2' and ch.activo = '1' and ch.estado = '0'";
$res = $bd->query($sql);
if($res->num_rows == 0)
	$data["html"] .= "No hay postulantes";
else {
	$sel = "";
	if($res->num_rows == 1)
		$sel = "checked";
	while($fila = $res->fetch_assoc()) {
		$sqlR = "select id from changuitas where contratado = ".$fila["usuario"]." and activo = '1' and (estado = '2' or estado = '3')";
		$resR = $bd->query($sqlR);
		$nR = $resR->num_rows;
		$sqlP = "select id from changuitas where usuario = ".$fila["usuario"]." and activo = '1'";
		$resP = $bd->query($sqlP);
		$nP = $resP->num_rows;
		$esInv = 0;
		$sqlI = "select id from invitados where mail = ".$fila["mail"]." and usuario = ".$_SESSION[SesionId];
		$resI = $bd->query($sqlI);
		if($resP->num_rows > 0)
			$esInv = 1;
		$data["html"] .= "<div class='postulante'><div class='izq'>";
		$data["html"] .= "<p class='nombre'>".$fila["nombre"]." ".substr($fila["apellido"], 0, 1).".</p>";
		if($fila["localidad"] != "") {
			$data["html"] .= "<p>".$fila["localidad"];
			if($fila["barrio"] != "")
				$data["html"] .= " &gt; ".$fila["barrio"];
			$data["html"] .= "</p>";
		}
		if($fila["educacion"] != 0)
			$data["html"] .= "<p>".$educacion[$fila["educacion"]]."</p>";
		if($fila["presentacion"] != "")
			$data["html"] .= "<p class='presentacion'>".$fila["presentacion"]."</p>";
		if($fila["perfil_fb"] != "")
			$data["html"] .= "<p><img src='img/fb.gif' alt='facebook' /> <a href='http://".$fila["perfil_fb"]."' target='_blank'>Ver perfil</a></p>";
		if($fila["perfil_li"] != "")
			$data["html"] .= "<p><img src='img/li.gif' alt='linkedin' /> <a href='http://".$fila["perfil_li"]."' target='_blank'>Ver perfil</a></p>";
		if($fila["perfil_gp"] != "")
			$data["html"] .= "<p><img src='img/gp.gif' alt='google+' /> <a href='http://".$fila["perfil_gp"]."' target='_blank'>Ver perfil</a></p>";
		$data["html"] .= "</div><div class='der'>";
		$botDetalles = " (<button class='btn-link btn-detalle-calificaciones' data-usuario-id='".$fila["usuario"]."'>ver detalles</button>)";
		if($fila["n"] == 0) {
			$fila["calificacion"] = -1;
			$botDetalles = " <em>todav&iacute;a no tiene</em>";
		}
		if($esInv == 1)
			$data["html"] .= "<p class='red'><i class='icon icon-star'></i> Forma parte de tu red de contactos</p>";
		$data["html"] .= "<p>Calificaci&oacute;n: ".$botDetalles."<span class='indicador'>".$f->indicador($fila["calificacion"], "calificacion")."</span></p>";
		if($fila["confianza"] == "")
			$fila["confianza"] = 0;
		$data["html"] .= "<p>Contactos en la red: <strong>".$fila["confianza"]."</strong><span class='indicador'>".$f->indicador($fila["confianza"], "confianza")."</span></p>";
		$data["html"] .= "<p>Changuitas realizadas: <strong>$nR</strong><span class='indicador'>".$f->indicador($nR, "changuitas")."</span></p>";
		$data["html"] .= "<p>Changuitas publicadas: <strong>$nP</strong><span class='indicador'>".$f->indicador($nP, "changuitas")."</span></p>";
		$data["html"] .= "<p><button class='btn btn-success btn-contratar btn-block' data-usuario-id='".$fila["usuario"]."' data-changuita-id='$id' data-dismiss='modal'>Elegir</button></p>";
		$data["html"] .= "</div><div class='clearfix'></div></div>";
	}
}
echo json_encode($data);
?>