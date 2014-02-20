<?php
include_once("includes/config.php");
$bd = conectar();
$cat = 0;
if(isset($_SESSION["categoria"]) && $_SESSION["categoria"] != "" && $_SESSION["categoria"] > 0)
	$cat = $_SESSION["categoria"];
$subcat = 0;
if(isset($_SESSION["subcategoria"]) && $_SESSION["subcategoria"] != "" && $_SESSION["subcategoria"] > 0)
	$subcat = $_SESSION["subcategoria"];
$loc = 0;
if(isset($_SESSION["localidad"]) && $_SESSION["localidad"] != "" && $_SESSION["localidad"] > 0)
	$loc = $_SESSION["localidad"];
$barrio = array();
if(isset($_SESSION["barrio"]) && is_array($_SESSION["barrio"]))
	$barrio = $_SESSION["barrio"];
$buscar = "";
if(isset($_SESSION["palabras"]) && $_SESSION["palabras"] != "")
	$buscar = $_SESSION["palabras"];
//
$sql = "select id, categoria from categorias where activo = '1' order by orden asc, categoria asc";
$res = $bd->query($sql);
$categoria = array();
while($fila = $res->fetch_assoc())
	$categoria[$fila["id"]] = $fila["categoria"];
$sql = "select id, subcategoria from subcategorias where categoria = $cat and activo = '1' order by orden asc, subcategoria asc";
$res = $bd->query($sql);
$subcategoria = array();
while($fila = $res->fetch_assoc())
	$subcategoria[$fila["id"]] = $fila["subcategoria"];
$sql = "select id, subcategoria from subcategorias where activo = '1' order by orden asc, subcategoria asc";
$res = $bd->query($sql);
$subcatTodas = array();
while($fila = $res->fetch_assoc())
	$subcatTodas[$fila["id"]+100] = $fila["subcategoria"];
$sql = "select id, localidad from localidades where activo = '1' order by id asc";
$res = $bd->query($sql);
$localidad = array();
while($fila = $res->fetch_assoc())
	$localidad[$fila["id"]] = $fila["localidad"];
$sql = "select id, barrio from barrios where localidad = $loc and activo = '1' order by barrio asc";
$res = $bd->query($sql);
$barrios = array();
while($fila = $res->fetch_assoc())
	$barrios[$fila["id"]] = $fila["barrio"];
?>
<div class="row">
	<div class="span9">
		<h3>Buscador de changuitas</h3>
		<div id="changuitas-filtros">
			<form name="buscar" id="changuitas-buscar">
			<div class="row">
				<div class="span3">
					<label>Categor&iacute;a</label>
					<select name="categoria" id="changuitas-categoria" class="span3">
						<option value="0">Todas</option>
<?php
foreach($categoria as $k => $v) {
	$sel = "";
	if($k == $cat)
		$sel = "selected = 'selected'";
?>
						<option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
						<option value="-1">&iquest;No encontr&aacute;s lo que busc&aacute;s?</option>
					</select>
					<div id="ini-div-sugerir" class="hide">
						<input type="text" name="sugerir" id="ini-sugerir" value="" placeholder="Sugerinos una categor&iacute;a" maxlength="100" />
						<button class="btn btn-primary" id="btn-sugerir">Sugerir</button>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="span3">
					<label>Subcategor&iacute;a</label>
<?php
$subcatClass = "";
$subcatAttr = "";
if($cat == 0) {
	$subcatClass = "disabled";
	$subcatAttr = "disabled='disabled'";
}
?>
					<select name="subcategoria" id="changuitas-subcategoria" class="span3 <?php echo $subcatClass ?>" <?php echo $subcatAttr ?>>
						<option value="0">--- elegir ---</option>
<?php
foreach($subcategoria as $k => $v) {
	$sel = "";
	if($k == $subcat)
		$sel = "selected = 'selected'";
?>
						<option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
					</select>
				</div>
				<div class="span3">
					<label>Localidad/Zona</label>
					<select name="localidad" id="changuitas-localidad" class="span3">
						<option value="0">Todas</option>
<?php
foreach($localidad as $k => $v) {
	$sel = "";
	if($k == $loc)
		$sel = "selected = 'selected'";
?>
						<option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="span3">
					<label>Barrios <a class="ayuda" title="Pod&eacute;s elegir m&aacute;s de uno. Primero ten&eacute;s que elegir una Localidad/Zona"><i class="icon-question-sign"></i></a></label>
<?php
$barrioClass = "";
$barrioAttr = "";
if($loc == 0) {
	$barrioClass = "disabled";
	$barrioAttr = "disabled";
}
?>
				    <div class="btn-group">
						<a class="btn dropdown-toggle span3 <?php echo $barrioClass ?>" data-toggle="dropdown" id="btn-changuitas-barrios" href="#" <?php echo $barrioClass ?>><span class="txt">Ninguno elegido</span>
							<span class="caret"></span>
						</a>
						<div class="dropdown-menu">
							<fieldset id="changuitas-barrio">
							<button class="btn btn-link" id="btn-changuitas-barrios-todos">Todos</button> |
							<button class="btn btn-link" id="btn-changuitas-barrios-ninguno">Ninguno</button>
							<button class="btn btn-link">Cerrar</button>
<?php
foreach($barrios as $k => $v) {
	$sel = "";
	if(in_array($k, $barrio))
		$sel = "checked = 'checked'";
?>
								<label><input name="barrio[]" type="checkbox" value="<?php echo $k ?>" <?php echo $sel ?> /> <?php echo $v ?></label>
<?php
}
?>
							</fieldset>
						</div>
					</div>
				</div>
				<div class="span3">
					<label>Palabras clave <a class="ayuda" title="Pod&eacute;s escribir algunas palabras para afinar la b&uacute;squeda."><i class="icon-question-sign"></i></a></label>
					<input type="text" name="palabras" id="changuitas-palabras" value="<?php echo $buscar ?>" class="span3 auto-palabras" />
				</div>
				<div class="span3 center">
					<label>&nbsp;</label>
					<button class="btn btn-success" id="btn-buscar-changuitas">Buscar</button>
					<button class="btn btn-link" id="btn-changuitas-todas">Ver todas las changuitas</button>
				</div>
			</div>
			</form>
		</div>
		<div id="changuitas-resultados" class="hide"></div>
		<div class="resultados-cargando hide"><img src="img/cargando2.gif" alt="cargando" /></div>
	</div>
</div>
<script>
$(document).ready(function() {
	var nBarrios = $('#changuitas-barrio input:checked').size();
	chBarrios(nBarrios);
	desactivaBuscarCh();
	$.post('ax/changuitas.php', $('#changuitas-buscar').serialize(), function(data) {
		activaBuscarCh();
		$('#changuitas-resultados').html(data.html);
	}, 'json');

	$('.auto-palabras').typeahead({
		source: ['<?php echo implode("', '", array_unique($categoria+$subcatTodas)) ?>']
	});
});
</script>