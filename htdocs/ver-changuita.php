<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("includes/config.php");
$bd = conectar();
include_once("class/seguridad.php");
$s = new Seguridad();
include_once("class/funciones.php");
$f = new Funciones();
if(!isset($_GET["id"]))
	$s->salir();
$id = $bd->real_escape_string($_GET["id"]);
$statusPago = -1;
$statusPagoValidos = array(0, 1, 2, 3);
// -1: no muestra alert
// 0: no pago
// 1: ok
// 2: pendiente
// 3: error
$calificaciones = array("Negativo", "Neutro", "Positivo");
if(isset($_GET["s"]) && in_array($_GET["s"], $statusPagoValidos))
	$statusPago = $bd->real_escape_string($_GET["s"]);
?>
<div class="row ver-changuita">
	<div class="span6">
<?php
// datos de la changuita
$sql = "select ch.id, ch.usuario, ch.contratado, ch.titulo, ch.descripcion, ch.precio, ch.cuando, ch.cuando_dias, ch.cuando_fecha, ch.cuando_hora_desde, ch.cuando_hora_hasta, ch.fecha, ch.fecha_contratacion, ch.estado, ch.activo, ch.vencida, cat.categoria, subcat.subcategoria, loc.localidad, bar.barrio, usu.id as uid, usu.nombre, usu.apellido, usu.mail, usu.celular, usu.celular_area, usu.presentacion, cal.calificacion, cal.n, con.confianza from changuitas as ch left join categorias as cat on cat.id = ch.categoria left join subcategorias as subcat on subcat.id = ch.subcategoria left join localidades as loc on ch.localidad = loc.id left join barrios as bar on ch.barrio = bar.id left join usuarios as usu on ch.usuario = usu.id left join calificacion as cal on ch.usuario = cal.usuario left join confianza as con on ch.usuario = con.usuario where ch.id = $id and ch.activo = '1'";
$res = $bd->query($sql);
if($res->num_rows == 0) {
?>
	<p>No existe la changuita que busc&aacute;s (quiz&aacute; venci&oacute;, fue borrada o ten&eacute;s mal la direcci&oacute;n).</p>
	</div>
</div>
<?php
	exit;
}
$fila = $res->fetch_assoc();
// vencida
$vencida = 0;
if($fila["vencida"] == 1)
	$vencida = 1;
// propia?
$propia = 0;
if(isset($_SESSION[SesionId]) && $_SESSION[SesionId] == $fila["usuario"])
	$propia = 1;
$soyContratado = 0;
if(isset($_SESSION[SesionId]) && $fila["contratado"] == $_SESSION[SesionId])
	$soyContratado = 1;
// postulantes
$sql = "select id from postulaciones where changuita = $id";
$res = $bd->query($sql);
$nPostulantes = $res->num_rows;
//
if($fila["estado"] == 0) {
	if($nPostulantes == 0) {
		$postulados = "Todav&iacute;a no hay ning&uacute;n postulante.<br/>&iexcl;S&eacute; el primero!";
		if($propia == 1)
			$postulados = "Todav&iacute;a no hay ning&uacute;n postulante.";
	}
	else if($nPostulantes == 1)
		$postulados = "Ya se postul&oacute; <strong>1</strong> usuario.";
	else
		$postulados = "Ya se postularon <strong>$nPostulantes</strong> usuarios.";
	$botPost = "<button class='btn btn-block btn-success btn-large btn-postular' data-changuita='".$fila["id"]."'>Postularme<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button>";
	if(isset($_SESSION[SesionId])) {
		if($propia == 1 && $vencida == 0) {
			if($nPostulantes > 0)
				$botPost = "<button class='btn btn-warning btn-elegir' data-changuita-id='".$fila["id"]."'>Elegir postulante</button>";
			else
				$botPost = "<button class='btn btn-warning btn-elegir disabled' disabled>Elegir postulante</button>";
			$botPost .= "<br/><a class='btn btn-block btn-primary btn-editar' href='#/editar-changuita|".$fila["id"]."' rel='address:/editar-changuita|".$fila["id"]."'>Editar changuita</a>";
		}
		else if($vencida == 0) {
			$sql = "select id from postulaciones where changuita = $id and usuario = ".$_SESSION[SesionId];
			$res = $bd->query($sql);
			if($res->num_rows == 1)
				$botPost = "<button class='btn btn-block btn-warning btn-large disabled' disabled>Ya est&aacute;s postulado</button><p class='center'><button class='btn-link btn-anular-postulacion' data-changuita-id='".$fila["id"]."'>Anular postulaci&oacute;n<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button></p>";
		}
		else {
			$botPost = "<div class='alert alert-error'><h4>Changuita vencida</h4></div>";
			$postulados = "";
		}
	}
	$datos = "<p class='center'>Publicada el ".$f->convertirMuestra($fila["fecha"], "fecha")."<br /> (hace ".$f->convertirMuestra($fila["fecha"], "hace").")</p>";
	$datos .= "<div class='datos-usuario'><h4 class='center'>Datos del usuario</h4>";
	$datos .= "<p class='nombre'>".$fila["nombre"]." ".substr($fila["apellido"], 0, 1).".</p>";
	// calificacion
	$calificacion = $fila["calificacion"];
	if($fila["n"] == 0)
		$calificacion = -1;
	if($calificacion >= 0)
		$datoCalif = "(<button class='btn-link btn-detalle-calificaciones' data-usuario-id='".$fila["uid"]."'>ver detalles</button>)<span class='indicador'>".$f->indicador($calificacion, "calificacion")."</span>";
	else
		$datoCalif = "<em>todav&iacute;a no tiene</em><span class='indicador'>".$f->indicador($calificacion, "calificacion")."</span>";
	$datos .= "<p>Calificaci&oacute;n: $datoCalif</p>";
	// invitados
	$confianza = $fila["confianza"];
	if($confianza == "")
		$confianza = 0;
	$datos .= "<p>Contactos en la red: <strong>$confianza</strong><span class='indicador'>".$f->indicador($confianza, "confianza")."</span></p>";
	// changuitas realizadas por el usuario
	$sql = "select id from changuitas where contratado = ".$fila["uid"]." and activo = '1' and estado = '2'";
	$res = $bd->query($sql);
	$nRealizadas = $res->num_rows;
	$datos .= "<p>Changuitas realizadas: <strong>$nRealizadas</strong><span class='indicador'>".$f->indicador($nRealizadas, "changuitas")."</span></p>";
	// changuitas publicadas por el usuario
	$sql = "select id from changuitas where usuario = ".$fila["uid"]." and activo = '1'";
	$res = $bd->query($sql);
	$nPublicadas = $res->num_rows;
	$datos .= "<p>Changuitas publicadas: <strong>$nPublicadas</strong><span class='indicador'>".$f->indicador($nPublicadas, "changuitas")."</span></p>";
	if($vencida == 0)
		$datos .= "<p class='txt'>Si ".$fila["nombre"]." acepta tu postulaci&oacute;n y te elige para que hagas la changuita, ambos recibir&aacute;n los datos de contacto del otro.</p></div>";
}
else {
	if($nPostulantes == 1)
		$postulados = "Se postul&oacute; <strong>1</strong> usuario.";
	else
		$postulados = "Se postularon <strong>$nPostulantes</strong> usuarios.";
	$botPost = "<div class='alert alert-success'>";
	$datos = "<p>Publicada el ".$f->convertirMuestra($fila["fecha"], "fecha")."</p>";
	$sql2 = "select nombre, apellido, mail, celular, celular_area from usuarios where id = ".$fila["contratado"];
	$res2 = $bd->query($sql2);
	$fila2 = $res2->fetch_assoc();
	if($propia == 1) {
		$datos .= "<p><strong>Postulante elegido:</strong></p><div class='alert'><h4>".$fila2["nombre"]." ".$fila2["apellido"]."</h4><p>E-mail: <a href='mailto:".$fila2["mail"]."'>".$fila2["mail"]."</a></p>";
		// if($fila2["telefono"] != "")
		// 	$datos .= "<p>Tel.: ".$fila2["telefono_area"]." ".$fila2["telefono"]."</p>";
		if($fila2["celular"] != "" || $fila2["celular_area"] != "")
			$datos .= "<p>Cel.: ".$fila2["celular_area"]." ".$fila2["celular"]."</p>";
		$datos .= "</div>";
	}
	else if($soyContratado == 1) {
		$datos .= "<p><strong>Datos de contacto:</strong></p><div class='alert'><h4>".$fila["nombre"]." ".$fila["apellido"]."</h4><p>E-mail: <a href='mailto:".$fila["mail"]."'>".$fila["mail"]."</a></p>";
		// if($fila["telefono"] != "")
		// 	$datos .= "<p>Tel.: ".$fila["telefono_area"]." ".$fila["telefono"]."</p>";
		if($fila["celular"] != "" || $fila["celular_area"] != "")
			$datos .= "<p>Cel.: ".$fila["celular_area"]." ".$fila["celular"]."</p>";
		$datos .= "</div>";
	}
	else {
		$datos .= "<p>Por: <strong>".$fila["nombre"]." ".substr($fila["apellido"], 0, 1).".</strong></p>";
		$datos .= "<p>Postulante elegido: <strong>".$fila2["nombre"]." ".substr($fila2["apellido"], 0, 1).".</strong></p>";
	}
	if($fila["estado"] == 1) {
		if($soyContratado == 1)
			$botPost .= "<h4>Changuita en curso</h4><p>&iexcl;Sos el postulante elegido!</p>";
		else
			$botPost .= "<h4>Changuita en curso</h4><p>Ya hay un postulante elegido y en este momento est&aacute; haciendo la changuita</p>";
	}
	else
		$botPost .= "<h4>Changuita finalizada</h4>";
	if($propia == 1 || $soyContratado == 1) {
		$sqlCC = "select calificacion, comentario, fecha from calificaciones where activo = '1' and changuita = $id and usuario = ".$fila["contratado"];
		$resCC = $bd->query($sqlCC);
		$filaCC = $resCC->fetch_assoc();
		$sqlCU = "select calificacion, comentario, fecha from calificaciones where activo = '1' and changuita = $id and usuario = ".$fila["usuario"];
		$resCU = $bd->query($sqlCU);
		$filaCU = $resCU->fetch_assoc();
		if($propia == 1) {
			if($resCC->num_rows == 0)
				$datos .= "<p>Todav&iacute;a no calificaste</p><p><button class='btn btn-block btn-success btn-calificar' data-changuita-id='$id'>Calificar<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button></p>";
			else {
				if($resCU->num_rows == 0)
					$datos .= "<p>Todav&iacute;a no te calificaron</p>";
				else {
					$datos .= "<p>Recibiste la siguiente calificaci&oacute;n<br/>por esta changuita:</p><div class='alert alert-info'><p>".$f->indicador($filaCU["calificacion"], "calificacion")."</p><p><strong>".$calificaciones[$filaCU["calificacion"]]."</strong></p><p>".$filaCU["comentario"]."</p><p class='fecha'>".$f->convertirMuestra($filaCU["fecha"], "fecha")."</p></div>";
				}
			}
		}
		else { // soy contratado
			$botPost .= "<p>Fuiste el postulante elegido</p>";
			if($resCU->num_rows == 0)
				$datos .= "<p>Todav&iacute;a no calificaste</p><p><button class='btn btn-block btn-success btn-calificar' data-changuita-id='$id'>Calificar<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button></p>";
			else {
				if($resCC->num_rows == 0)
					$datos .= "<p>Todav&iacute;a no te calificaron</p>";
				else {
					$datos .= "<p>Recibiste la siguiente calificaci&oacute;n<br/>por esta changuita:</p><div class='alert alert-info'><p>".$f->indicador($filaCC["calificacion"], "calificacion")."</p><p><strong>".$calificaciones[$filaCC["calificacion"]]."</strong></p><p>".$filaCC["comentario"]."</p><p class='fecha'>".$f->convertirMuestra($filaCC["fecha"], "fecha")."</p></div>";
				}
			}
		}
	}
	$botPost .= "</div>";
}
// preguntas
$sql = "select pr.id, pr.usuario, pr.pregunta, pr.respuesta, pr.pregunta_fecha, pr.respuesta_fecha, usu.nombre, usu.apellido from preguntas as pr left join usuarios as usu on pr.usuario = usu.id where pr.activo = '1' and pr.changuita = $id order by pr.pregunta_fecha desc";
$resPr = $bd->query($sql);
$nPr = $resPr->num_rows;
// denunciar
$denunciarCh = "";
if($fila["estado"] == 0 && $propia == 0)
	$denunciarCh = "<p class='denunciar-ch'><button class='btn-link btn-denunciar-changuita' data-changuita-id='".$fila["id"]."'>Denunciar changuita</button> <a class='ayuda' title='Si te parece que la changuita ofrecida es inadecuada, ofensiva, discriminatoria y/o consider&aacute;s que deber&iacute;a ser eliminada, pod&eacute;s denunciarla. Nuestro equipo la evaluar&aacute; y tomar&aacute; las medidas que correspondan.'><i class='icon-question-sign'></i></a></p>";
if($statusPago > -1) {
	$classPago = "alert-";
	$txtPago = "";
	switch($statusPago) {
		case 0:
			$classPago .= "error";
			$txtPago = "Ocurri&oacute; un error al conectarse con el sistema de pagos. De todos modos, la changuita fue publicada. Por favor, sald&aacute; la deuda apenas puedas, as&iacute; evit&aacute;s que se bloque&eacute; tu usuario.";
			break;
		case 1:
			$classPago .= "success";
			$txtPago = "El pago fue aprobado.";
			break;
		case 2:
			$classPago .= "info";
			$txtPago = "El pago qued&oacute; pendiente. De todos modos, la changuita fue publicada. Cuando se acredite te avisaremos.";
			break;
		case 3:
			$classPago .= "error";
			$txtPago = "No realizaste el pago. De todos modos, la changuita fue publicada. Por favor, sald&aacute; la deuda apenas puedas, as&iacute; evit&aacute;s que se bloque&eacute; tu usuario.";
			break;
	}
?>
		<div class="alert <?php echo $classPago ?>"><?php echo $txtPago ?></div>
<?php
}
?>
		<div class="div-padding">
		<h6><?php echo $fila["categoria"] ?> &gt; <?php echo $fila["subcategoria"] ?></h6>
		<h3><?php echo $fila["titulo"] ?></h3>
<?php
if($fila["localidad"] != "") {
?>
		<p><?php echo $fila["localidad"] ?> &gt; <?php echo $fila["barrio"] ?></p>
<?php
}
$cuandoHorario = "";
if($fila["cuando_hora_desde"] != "00:00:00")
	$cuandoHorario = " de ".substr($fila["cuando_hora_desde"], 0, 5)." a ".substr($fila["cuando_hora_hasta"], 0, 5)." hs";
if($fila["cuando"] == 2) {
	$cuandoDias = explode(",", $fila["cuando_dias"]);
	$cuandoDia = array();
	foreach ($cuandoDias as $v)
		$cuandoDia[] = $dias[$v];
?>
		<p>A realizar los d&iacute;as <strong><?php echo implode(", ", $cuandoDia).$cuandoHorario ?></strong></p>
<?php
}
else if($fila["cuando"] == 3) {
?>
		<p>A realizar el <strong><?php echo $f->convertirMuestra($fila["cuando_fecha"], "fecha").$cuandoHorario ?></strong></p>
<?php
}
else if($cuandoHorario != "") {
?>
		<p>A realizar <strong><?php echo $cuandoHorario ?></strong></p>
<?php
}
?>
		<div class="desc"><p><?php echo nl2br($fila["descripcion"]) ?></p><?php echo $denunciarCh ?></div>
		<div id="fb-root"></div>
		<div class="ch-social">
			<iframe src="//www.facebook.com/plugins/like.php?href=<?php echo Sitio ?>%2F%23%2Fchanguita%7C<?php echo $id ?>&amp;width=155&amp;layout=button_count&amp;action=recommend&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=511297335556303" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:155px; height:21px;" allowTransparency="true"></iframe>
			<script src="//platform.linkedin.com/in.js" type="text/javascript">
			 lang: es_ES
			</script>
			<script type="IN/Share" data-counter="right" data-showzero="true"></script>
			<div class="g-plus" data-action="share" data-annotation="bubble" data-href="<?php echo Sitio ?>/#/changuita|<?php echo $id ?>"></div>
		</div>
		<h4 class="preguntas">Preguntas (<?php echo $nPr ?>)</h4>
<?php
if($fila["estado"] == 0 && isset($_SESSION[SesionId]) && $propia == 0 && $vencida == 0) {
?>
		<button class="btn-link btn-hacer-pregunta">Hacer una pregunta</button>
<?php
}
?>
		<div class="clearfix"></div>
<?php
while($filaPr = $resPr->fetch_assoc()) {
	$denunciarP = "<p>&nbsp;</p>";
	$denunciarR = "<p>&nbsp;</p>";
	if($fila["estado"] == 0 && $propia == 0) {
		if(!isset($_SESSION[SesionId]) || $filaPr["usuario"] != $_SESSION[SesionId])
			$denunciarP = "<p class='denunciar-ch'><button class='btn-link btn-denunciar-pregunta' data-pregunta-id='".$filaPr["id"]."'>Denunciar pregunta</button> <a class='ayuda' title='Si te parece que la pregunta es inadecuada, ofensiva, discriminatoria y/o consider&aacute;s que deber&iacute;a ser eliminada, pod&eacute;s denunciarla. Nuestro equipo la evaluar&aacute; y tomar&aacute; las medidas que correspondan.'><i class='icon-question-sign'></i></a></p>";
		$denunciarR = "<p class='denunciar-ch'><button class='btn-link btn-denunciar-respuesta' data-respuesta-id='".$filaPr["id"]."'>Denunciar respuesta</button> <a class='ayuda' title='Si te parece que la respuesta es inadecuada, ofensiva, discriminatoria y/o consider&aacute;s que deber&iacute;a ser eliminada, pod&eacute;s denunciarla. Nuestro equipo la evaluar&aacute; y tomar&aacute; las medidas que correspondan.'><i class='icon-question-sign'></i></a></p>";
	}
?>
			<div class="pregunta">
				<p><strong><?php echo $filaPr["nombre"] ?> <?php echo substr($filaPr["apellido"], 0, 1) ?>.</strong>, el <?php echo $f->convertirMuestra($filaPr["pregunta_fecha"], "fecha") ?> (hace <?php echo $f->convertirMuestra($filaPr["pregunta_fecha"], "hace") ?>) pregunt&oacute;:</p>
				<p class="p"><?php echo nl2br($filaPr["pregunta"]) ?></p>
				<?php echo $denunciarP ?>
<?php
	if($filaPr["respuesta"] != "") {
?>
				<p><strong><?php echo $fila["nombre"] ?> <?php echo substr($fila["apellido"], 0, 1) ?>.</strong>, el <?php echo $f->convertirMuestra($filaPr["respuesta_fecha"], "fecha") ?> (hace <?php echo $f->convertirMuestra($filaPr["respuesta_fecha"], "hace") ?>) respondi&oacute;:</p>
				<p class="r"><?php echo nl2br($filaPr["respuesta"]) ?></p>
				<?php echo $denunciarR ?>
<?php
	}
	else if($fila["estado"] == 0 && $vencida == 0) {
		if($propia == 0) {
?>
				<p class="no-r"><em>Todav&iacute;a no hay respuesta</em></p>
<?php
		}
		else {
?>
				<textarea name="respuesta" class="respuesta" placeholder="Respuesta" maxlength="500"></textarea>
				<button class="btn btn-success btn-responder" data-pregunta='<?php echo $filaPr["id"] ?>'>Responder<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button><p class="ayuda-responder">No est&aacute; permitido dar datos personales (tel&eacute;fonos, direcciones de e-mail, etc.). M&aacute;ximo 500 caracteres.</p>
<?php
		}
	}
?>
			</div>
<?php
}
if($fila["estado"] == 0 && isset($_SESSION[SesionId]) && $propia == 0 && $vencida == 0) {
?>
	<textarea name="pregunta" id="pregunta" placeholder="Hacer una pregunta" maxlength="500"></textarea>
	<button class="btn btn-primary btn-preguntar" data-changuita='<?php echo $fila["id"] ?>'>Preguntar<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button><p class="ayuda-preguntar">Record&aacute; que no pod&eacute;s dar datos personales ni direcciones de e-mail.<br/>M&aacute;ximo 500 caracteres.</p>
<?php
}
else if($fila["estado"] == 0 && !isset($_SESSION[SesionId]) && $vencida == 0) {
?>
	<p><em>Para hacer preguntas ten&eacute;s que <button class="btn-link btn-iniciar-sesion">iniciar sesi&oacute;n</button>.</em></p>
<?php
}
?>
	</div>
	</div>
	<div class="span3 columna">
		<p class="center"><?php echo $postulados ?></p>
		<?php echo $botPost ?>
		<p class="precio">$<?php echo $fila["precio"] ?><br/><small><span>Tu fee (<?php echo Fee*100 ?>%):</span> $<?php echo str_replace(".", ",", sprintf("%01.2f", $fila["precio"]*Fee)) ?></small></p>
		<?php echo $datos ?>
	</div>
</div>
<script type="text/javascript" src="https://apis.google.com/js/platform.js">
  {lang: 'es-419'}
</script>