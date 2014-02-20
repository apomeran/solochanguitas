<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("includes/config.php");
$externo = 0;
if(isset($_SESSION[SesionTmp])) {
	if(strpos($_SESSION[SesionTmp], "ex") !== false) {
		$externo = 1;
		$_SESSION[SesionExterno] = 1;
	}
	else if($_SESSION[SesionTmp] > 0) {
		include("cuenta-no-activa.php");
		exit;
	}
}
include_once("class/seguridad.php");
$s = new Seguridad();
include_once("class/funciones.php");
$f = new Funciones();
$columnas = array("mail", "nombre", "apellido", "sexo", "nacimiento", "localidad", "barrio", "celular_area", "celular", "educacion", "institucion", "presentacion", "aviso", "aviso_np", "aviso_rech", "aviso_ca", "aviso_pr", "aviso_res", "aviso_pv", "aviso_ve", "aviso_inv", "aviso_cal", "aviso_bal", "dni", "perfil_fb", "perfil_li", "perfil_gp");
$convertir = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
$avisosCheck = array("aviso_np", "aviso_rech", "aviso_ca", "aviso_pr", "aviso_res", "aviso_pv", "aviso_ve", "aviso_inv", "aviso_cal", "aviso_bal");
$bd = conectar();
if(isset($_GET["id"])) {
	$id = $bd->real_escape_string($_GET["id"]);
	if($_SESSION[SesionNivel] == "1") {
		$sql = "select ".implode(", ", $columnas)." from usuarios where id = $id";
	}
	else {
		if($externo == 0 && (!isset($_SESSION[SesionId]) || $id != $_SESSION[SesionId]))
			$s->salir();
		if($externo == 0)
			$sql = "select ".implode(", ", $columnas)." from usuarios where id = $id and activo = '2'";
		else
			$sql = "select ".implode(", ", $columnas)." from usuarios where id = $id and activo != '0'";
	}
	$res = $bd->query($sql);
	if($res->num_rows == 0)
		$s->salir();
	$data = array();
	$fila = $res->fetch_assoc();
	foreach($columnas as $k => $v)
		$data[$v] = $f->convertirMuestra($fila[$v], $convertir[$k]);
	if($externo == 1) {
		$data["dni"] = "";
		$data["nacimiento"] = "";
	}
	$sql = "select id, categoria from usuarios_categorias where usuario = ".$id;
	$res = $bd->query($sql);
	$data["categoria"] = array();
	while($fila = $res->fetch_assoc())
		$data["categoria"][$fila["id"]] = $fila["categoria"];
	$sql = "select id, barrio from usuarios_barrios where usuario = ".$id;
	$res = $bd->query($sql);
	$data["barrioAviso"] = array();
	while($fila = $res->fetch_assoc())
		$data["barrioAviso"][$fila["id"]] = $fila["barrio"];
}
else {
	$id = 0;
	foreach($columnas as $v)
		$data[$v] = "";
	// default avisos
	foreach($avisosCheck as $v)
		$data[$v] = 1;
	$data["categoria"] = array();
	$data["barrioAviso"] = array();
}
//
$sexo = array(1=>"Femenino", "Masculino");
$educacion = array(1=>"Primario completo", "Primario incompleto", "Primario en curso", "Secundario completo", "Secundario incompleto", "Secundario en curso", "Terciario completo", "Terciario incompleto", "Terciario en curso", "Universitario completo", "Universitario incompleto", "Universitario en curso");
$aviso = array(1=>"Alerta instant&aacute;nea (esta opci&oacute;n te va a dar mayor ventaja para conseguir changuitas)", "Diariamente", "Semanalmente", "Nunca (solo pod&eacute;s ver las changuitas que te interesan entrando al sitio)");
$sql = "select id, localidad from localidades where activo = '1' order by id asc";
$res = $bd->query($sql);
$localidad = array();
while($fila = $res->fetch_assoc())
	$localidad[$fila["id"]] = $fila["localidad"];
$localidadSql = $data["localidad"];
if($localidadSql == "")
	$localidadSql = 0;
$sql = "select id, barrio from barrios where localidad = $localidadSql and activo = '1' order by barrio asc";
$res = $bd->query($sql);
$barrio = array();
while($fila = $res->fetch_assoc())
	$barrio[$fila["id"]] = $fila["barrio"];
$sql = "select id, localidad, barrio from barrios where activo = '1' order by barrio asc";
$res = $bd->query($sql);
$barriosTodos = array();
while($fila = $res->fetch_assoc())
	$barriosTodos[$fila["localidad"]][$fila["id"]] = $fila["barrio"];
$sql = "select id, categoria from categorias where activo = '1' order by orden asc, categoria asc";
$res = $bd->query($sql);
$categoria = array();
while($fila = $res->fetch_assoc())
	$categoria[$fila["id"]] = $fila["categoria"];
$sql = "select id, categoria, subcategoria from subcategorias where activo = '1' order by orden asc, subcategoria asc";
$res = $bd->query($sql);
$subcategoria = array();
while($fila = $res->fetch_assoc())
	$subcategoria[$fila["categoria"]][$fila["id"]] = $fila["subcategoria"];
?>
<h3>Datos del usuario</h3>
<form class="form-horizontal" id="datos-usuarios">
	<fieldset>
	<input type="hidden" name="id" value="<?php echo $id ?>" />
	<p class="legend2">Complet&aacute; esta informaci&oacute;n para luego encontrar las changuitas que necesit&aacute;s con mayor facilidad, sin tener que seleccionar de nuevo, por ejemplo, el barrio en el que te encontr&aacute;s. Cuantos m&aacute;s datos completes, m&aacute;s f&aacute;cil ser&aacute; que encuentres lo que busc&aacute;s.</p>
<?php
$mailClass = "";
$mailAttr = "";
if(isset($_SESSION[SesionExterno]) && $_SESSION[SesionExterno] == 1) {
	$mailClass = "disabled";
	$mailAttr = "disabled = 'disabled'";
?>
	<input type="hidden" name="mail" value="<?php echo $data["mail"] ?>" />
<?php
}
?>
	<div class="control-group">
		<label class="control-label" for="mail">E-mail</label>
		<div class="controls">
			<input type="text" id="mail" name="mail" value="<?php echo $data["mail"] ?>" class="<?php echo $mailClass ?>" <?php echo $mailAttr ?> />
			<a class="ayuda" title="Este campo es obligatorio. Ten&eacute;s que usar una direcci&oacute;n v&aacute;lida. Los usuarios que contrates o te contraten podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
<?php
if(!isset($_SESSION[SesionExterno]) || $_SESSION[SesionExterno] == 0) {
	$clavesClass = "hide";
	$modificarClass = "";
	if($id == 0) {
		$clavesClass = "";
		$modificarClass = "hide";
	}
?>
	<div class="control-group divClave <?php echo $clavesClass ?>">
		<label class="control-label" for="clave">Contrase&ntilde;a</label>
		<div class="controls">
			<input type="password" id="clave" name="clave" value="" />
			<a class="ayuda" title="Este campo es obligatorio. Ten&eacute;s que ingresar una contrase&ntilde;a de por lo menos 8 (ocho) caracteres, que pueden ser letras (may&uacute;sculas y min&uacute;sculas) y n&uacute;meros. Ej.: miCLAveS3cr3t4"><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group divClave <?php echo $clavesClass ?>">
		<label class="control-label" for="clave2">Repetir contrase&ntilde;a</label>
		<div class="controls">
			<input type="password" id="clave2" name="clave2" value="" />
			<a class="ayuda" title="Este campo es obligatorio. Ten&eacute;s que ingresar la misma contrase&ntilde;a que en el campo anterior."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group <?php echo $modificarClass ?>">
		<label class="control-label" for="clave">Contrase&ntilde;a</label>
		<div class="controls">
			<button class="btn btn-link" id="modificarClave">Modificar contrase&ntilde;a</button>
		</div>
	</div>
<?php
}
?>
	<div class="control-group">
		<label class="control-label" for="nombre">Nombre</label>
		<div class="controls">
			<input type="text" id="nombre" name="nombre" value="<?php echo $data["nombre"] ?>" />
			<a class="ayuda" title="Este campo es obligatorio. Los dem&aacute;s usuarios podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="apellido">Apellido</label>
		<div class="controls">
			<input type="text" id="apellido" name="apellido" value="<?php echo $data["apellido"] ?>" />
			<a class="ayuda" title="Este campo es obligatorio. Los usuarios que contrates o te contraten podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="dni">DNI</label>
		<div class="controls">
			<input type="text" id="dni" name="dni" value="<?php echo $data["dni"] ?>" maxlength="8" />
			<a class="ayuda" title="Este campo es obligatorio. Us&aacute; solo n&uacute;meros, sin puntos"><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="sexo">Sexo</label>
		<div class="controls">
			<select id="sexo" name="sexo">
				<option value="0">--- elegir ---</option>
<?php
foreach($sexo as $k => $v) {
	$sel = "";
	if($data["sexo"] == $k)
		$sel = "selected = 'selected'";
?>
				<option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
			</select>
			<span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="nacimiento">A&ntilde;o de nacimiento</label>
		<div class="controls">
			<input type="text" id="nacimiento" name="nacimiento" value="<?php echo $data["nacimiento"] ?>" maxlength="4" class="span1" />
			<span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="localidad">Zona</label>
		<div class="controls">
			<select id="localidad" name="localidad">
				<option value="0">--- elegir ---</option>
<?php
foreach($localidad as $k => $v) {
	$sel = "";
	if($data["localidad"] == $k)
		$sel = "selected = 'selected'";
?>
				<option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
			</select>
			<span class="help-block"></span>
		</div>
	</div>
<?php
$barrioClass = "";
$barrioAttr = "";
if($data["localidad"] == 0) {
	$barrioClass = "disabled";
	$barrioAttr = "disabled = 'disabled'";
	$data["barrio"] = 0;
}
?>
	<div class="control-group">
		<label class="control-label" for="barrio">Localidad / Barrio</label>
		<div class="controls">
			<select id="barrio" name="barrio" class="<?php echo $barrioClass ?>" <?php echo $barrioAttr ?>>
				<option value="0">--- elegir ---</option>
<?php
foreach($barrio as $k => $v) {
	$sel = "";
	if($data["barrio"] == $k)
		$sel = "selected = 'selected'";
?>
				<option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
			</select>
			<a class="ayuda" title="Primero ten&eacute;s que elegir una opci&oacute;n en el campo anterior."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="celular">Celular</label>
		<div class="controls">
			<input type="text" id="celular_area" name="celular_area" value="<?php echo $data["celular_area"] ?>" class="span1" /> <input type="text" id="celular" name="celular" value="<?php echo $data["celular"] ?>" class="inputMenosSpan1" />
			<a class="ayuda" title="Indic&aacute; primero el n&uacute;mero de &aacute;rea. Los usuarios que contrates o te contraten podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="educacion">Nivel de educaci&oacute;n</label>
		<div class="controls">
			<select id="educacion" name="educacion">
				<option value="0">--- elegir ---</option>
<?php
foreach($educacion as $k => $v) {
	$sel = "";
	if($data["educacion"] == $k)
		$sel = "selected = 'selected'";
?>
				<option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
			</select>
			<span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="institucion">Instituci&oacute;n</label>
		<div class="controls">
			<input type="text" id="institucion" name="institucion" value="<?php echo $data["institucion"] ?>" />
			<span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="presentacion">Carta de presentaci&oacute;n<br /><small>Contale brevemente qui&eacute;n sos a tus posibles empleados o empleadores</small></label>
		<div class="controls">
			<textarea id="presentacion" name="presentacion" maxlength="500"><?php echo $data["presentacion"] ?></textarea><br/>
			<a class="ayuda" title="M&aacute;ximo 500 caracteres. Los dem&aacute;s usuarios podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="perfil_fb">Link a tu perfil de Facebook<br /></label>
		<div class="controls">
			http://<input type="text" id="perfil_fb" name="perfil_fb" value="<?php echo $data["perfil_fb"] ?>" />
			<a class="ayuda" title="Los dem&aacute;s usuarios podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="perfil_li">Link a tu perfil de LinkedIn<br /></label>
		<div class="controls">
			http://<input type="text" id="perfil_li" name="perfil_li" value="<?php echo $data["perfil_li"] ?>" />
			<a class="ayuda" title="Los dem&aacute;s usuarios podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="perfil_gp">Link a tu perfil de Google+<br /></label>
		<div class="controls">
			http://<input type="text" id="perfil_gp" name="perfil_gp" value="<?php echo $data["perfil_gp"] ?>" />
			<a class="ayuda" title="Los dem&aacute;s usuarios podr&aacute;n ver este dato."><i class="icon-question-sign"></i></a><span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label control-label-sin-margen">Si quer&eacute;s trabajar, &iquest;sobre qu&eacute; categor&iacute;as te gustar&iacute;a recibir las b&uacute;squedas?</label>
		<div class="controls">
			<button class="btn btn-link" id="categoriasTodas">Todos</button> | <button class="btn btn-link" id="categoriasNinguna">Ninguno</button>
			<div id="categoriasLista" class="sub-lista">
<?php
foreach($categoria as $k => $v) {
?>
				<div class="categoria">
					<label>
						<input type="checkbox" class="categoriaCheck" /> <?php echo $v ?>
					</label>
					<div class="subcats hide">
<?php
	foreach($subcategoria[$k] as $kk => $vv) {
		$sel = "";
		if(in_array($kk, $data["categoria"]))
			$sel = "checked = 'checked'";
?>
						<label>
							<input type="checkbox" id="categoria<?php echo $kk ?>" name="categoria[]" value="<?php echo $kk ?>" <?php echo $sel ?> class="subcategoriaCheck" />
							<?php echo $vv ?>
						</label>
<?php
	}
?>
					</div>
				</div>
<?php
}
?>
			</div>
<?php
if($id > 0) {
?>
			<button class="btn-link btn-abrir-sugerir">&iquest;No encontr&aacute;s lo que busc&aacute;s?</button>
<?php
}
?>
			<span class="help-block"></span>
<?php
if($id > 0) {
?>
			<div id="ini-div-sugerir" class="hide">
				<input type="text" name="sugerir" id="ini-sugerir" value="" placeholder="Sugerinos la categor&iacute;a que falta" maxlength="100" />
				<button class="btn btn-primary" id="btn-sugerir">Sugerir</button>
				<div class="clearfix"></div>
			</div>
<?php
}
?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label control-label-sin-margen">Si quer&eacute;s trabajar, &iquest;de qu&eacute; zonas te gustar&iacute;a recibir las b&uacute;squedas?</label>
		<div class="controls">
			<button class="btn btn-link" id="zonasTodas">Todos</button> | <button class="btn btn-link" id="zonasNinguna">Ninguno</button>
			<div id="zonasLista" class="sub-lista">
<?php
foreach($localidad as $k => $v) {
?>
				<div class="categoria">
					<label>
						<input type="checkbox" class="categoriaCheck" /> <?php echo $v ?>
					</label>
					<div class="subcats hide">
<?php
	foreach($barriosTodos[$k] as $kk => $vv) {
		$sel = "";
		if(in_array($kk, $data["barrioAviso"]))
			$sel = "checked = 'checked'";
?>
						<label>
							<input type="checkbox" id="barrioAviso<?php echo $kk ?>" name="barrioAviso[]" value="<?php echo $kk ?>" <?php echo $sel ?> class="subcategoriaCheck" />
							<?php echo $vv ?>
						</label>
<?php
	}
?>
					</div>
				</div>
<?php
}
?>
			</div>
			<span class="help-block"></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label control-label-sin-margen">E-mails con changuitas nuevas<br /><small>Eleg&iacute; cada cu&aacute;nto quer&eacute;s recibir e-mails con las nuevas changuitas publicadas en las categor&iacute;as que elegiste</small></label>
		<div class="controls">
<?php
if($data["aviso"] == "")
	$data["aviso"] = 1;
foreach($aviso as $k => $v) {
	$aviso1Class = "";
	if($k == 1)
		$aviso1Class = "text-success bold";
	$sel = "";
	if($data["aviso"] == $k)
		$sel = "checked = 'checked'";
?>
			<label class="<?php echo $aviso1Class ?>">
				<input type="radio" id="aviso<?php echo $k ?>" name="aviso" value="<?php echo $k ?>" <?php echo $sel ?> />
				<?php echo $v ?>
			</label>
<?php
}
?>
			<span class="help-block"></span>
		</div>
	</div>

<?php
$avisoCheckNp = "";
$avisoCheckRech = "";
$avisoCheckCa = "";
$avisoCheckCal = "";
$avisoCheckBal = "";
$avisoCheckPr = "";
$avisoCheckRes = "";
$avisoCheckPv = "";
$avisoCheckVe = "";
$avisoCheckInv = "";
if($data["aviso_np"] == 1)
	$avisoCheckNp = "checked='checked'";
if($data["aviso_rech"] == 1)
	$avisoCheckRech = "checked='checked'";
if($data["aviso_ca"] == 1)
	$avisoCheckCa = "checked='checked'";
if($data["aviso_cal"] == 1)
	$avisoCheckCal = "checked='checked'";
if($data["aviso_bal"] == 1)
	$avisoCheckBal = "checked='checked'";
if($data["aviso_pr"] == 1)
	$avisoCheckPr = "checked='checked'";
if($data["aviso_res"] == 1)
	$avisoCheckRes = "checked='checked'";
if($data["aviso_pv"] == 1)
	$avisoCheckPv = "checked='checked'";
if($data["aviso_ve"] == 1)
	$avisoCheckVe = "checked='checked'";
if($data["aviso_inv"] == 1)
	$avisoCheckInv = "checked='checked'";
?>
<div class="control-group">
	<label class="control-label control-label-sin-margen">Avisos por e-mail<br /><small>Eleg&iacute; qu&eacute; notificaciones quer&eacute;s recibir por e-mail</small></label>
	<div class="controls">
		<label><input type="checkbox" id="aviso_np" name="aviso_np" value="1" <?php echo $avisoCheckNp ?> /> Hay un nuevo postulante para mi changuita</label>
		<label><input type="checkbox" id="aviso_rech" name="aviso_rech" value="1" <?php echo $avisoCheckRech ?> /> Una changuita a la que me postul&eacute; fue borrada, venci&oacute; o eligieron a otro para realizarla</label>
		<label><input type="checkbox" id="aviso_ca" name="aviso_ca" value="1" <?php echo $avisoCheckCa ?> /> Tengo una calificaci&oacute;n pendiente</label>
		<label><input type="checkbox" id="aviso_cal" name="aviso_cal" value="1" <?php echo $avisoCheckCal ?> /> Me calificaron</label>
		<label><input type="checkbox" id="aviso_pr" name="aviso_pr" value="1" <?php echo $avisoCheckPr ?> /> Hay una pregunta para mi changuita</label>
		<label><input type="checkbox" id="aviso_res" name="aviso_res" value="1" <?php echo $avisoCheckRes ?> /> Hay una respuesta para mi pregunta</label>
		<label><input type="checkbox" id="aviso_pv" name="aviso_pv" value="1" <?php echo $avisoCheckPv ?> /> Una changuita que publiqu&eacute; est&aacute; por vencer</label>
		<label><input type="checkbox" id="aviso_ve" name="aviso_ve" value="1" <?php echo $avisoCheckVe ?> /> Una changuita que publiqu&eacute; venci&oacute;</label>
		<label><input type="checkbox" id="aviso_bal" name="aviso_bal" value="1" <?php echo $avisoCheckBal ?> /> Tengo cr&eacute;dito a favor o deuda</label>
		<label><input type="checkbox" id="aviso_inv" name="aviso_inv" value="1" <?php echo $avisoCheckInv ?> /> Un contacto se sum&oacute; a mi red</label>
	</div>
</div>
<?php
if($id == 0 || $externo == 1) {
?>
	<div class="control-group">
		<label class="control-label control-label-sin-margen" for="condiciones">T&eacute;rminos y condiciones</label>
		<div class="controls">
			<label>
				<input type="checkbox" id="condiciones" name="condiciones" value="1" />
				Declaro que le&iacute; y acepto los <button class="btn-link btn-condiciones">t&eacute;rminos y condiciones de uso</button>
			</label>
			<span class="help-block"></span>
		</div>
	</div>
<?php
}
?>
	<div class="form-actions">
		<button class="btn btn-success btn-large" id="boton-submit">Continuar</button>
		<span class="help-inline text-error" id="validar"></span>
	</div>
	</fieldset>
</form>
<script>
$(document).ready(function() {
	$('.subcategoriaCheck:checked').each(function() {
		var c = $(this).parents('.categoria');
		var sc = $(this).parents('.subcats');
		$('.categoriaCheck', c).attr('checked', 'checked');
		$('label:first', c).addClass('btn-success');
		sc.show();
	});
});
</script>