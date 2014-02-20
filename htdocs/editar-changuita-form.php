<p class="legend2">Como la changuita ya est&aacute; publicada, no pod&eacute;s modificar todos los datos. Solo pod&eacute;s:</p>
<ul class="legend2">
<li>subir el <em>precio</em>, es decir, ofrecer m&aacute;s dinero por la changuita (recomendado si ten&eacute;s pocas postulaciones)</li>
<li>cambiar o agregar palabras clave</li>
<li>agregar alguna aclaraci&oacute;n en la <em>descripci&oacute;n</em> (recomendado si te hicieron muchas preguntas o te parece que hay algo que no se entiende)</li>
<li>contratar un servicio para que tu changuita llegue a m&aacute;s gente (recomendado)</li>
</ul>
<div class="control-group">
	<label class="control-label">Categor&iacute;a</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $data["categoria"] ?></p>
	</div>
</div>
<div class="control-group">
	<label class="control-label">Subcategor&iacute;a</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $data["subcategoria"] ?></p>
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="titulo">Nombre de la changuita</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $data["titulo"] ?></p>
	</div>
</div>
<?php
$mostrarBarrio = 1;
if($data["localidad"] == "") {
	$data["localidad"] = "<em>Todas</em>";
	$mostrarBarrio = 0;
}
?>
<div class="control-group">
	<label class="control-label">Zona</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $data["localidad"] ?></p>
	</div>
</div>
<?php
if($mostrarBarrio == 1) {
?>
<div class="control-group">
	<label class="control-label">Localidad / Barrio</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $data["barrio"] ?></p>
	</div>
</div>
<?php
}
?>
<div class="control-group">
	<label class="control-label" for="descripcion">Descripci&oacute;n de la changuita</label>
	<div class="controls">
		<p class="disabled"><?php echo $data["descripcion"] ?></p>
<?php
$max = 1000 - strlen($data["descripcion"]);
if($max > 0) {
?>
		<textarea id="descripcion" name="descripcion" maxlength="<?php echo $max ?>"></textarea><br/>
		<a class="ayuda" title="Te quedan <?php echo $max ?> caracteres. Lo que escribas se agregar&aacute; a lo anterior."><i class="icon-question-sign"></i></a>
<?php
}
else {
?>
		<p class="alert alert-error">Alcanzaste el m&aacute;ximo de caracteres permitidos. No pod&eacute;s agregar m&aacute;s texto.</p>
<?php
}
?>
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="palabras">Palabras clave</label>
	<div class="controls">
		<input type="text" id="palabras" name="palabras" value="<?php echo implode(", ", $data["palabras"]) ?>" />
			<a class="ayuda" title="Ingres&aacute; algunas palabras (separadas por comas) para ayudar a los dem&aacute;s a encontrar tu changuita."><i class="icon-question-sign"></i></a><span class="help-block"></span>
	</div>
</div>
<?php
$cuando = "";
if($data["cuando"] == 1)
	$cuando = "En cualquier momento, a combinar";
else if($data["cuando"] == 2)
	foreach($data["cuando_dias"] as $v)
		$cuando .= $dias[$v]."<br/>";
else if($data["cuando"] == 3)
	$cuando = $data["cuando_fecha"];
?>
<div class="control-group">
	<label class="control-label">&iquest;Cu&aacute;ndo hay que hacer la changuita?</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $cuando ?></p>
	</div>
</div>
<?php
if($data["cuando_hora_desde"] != "00:00:00") {
	$cuando_hora = "Entre las ".substr($data["cuando_hora_desde"], 0, 5)." hs y las ".substr($data["cuando_hora_hasta"], 0, 5)." hs";
?>
<div class="control-group">
	<label class="control-label">&iquest;En qu&eacute; horario?</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $cuando_hora ?></p>
	</div>
</div>
<?php
}
$precioSugerido = "";
if($data["hora"] != "")
	$precioSugerido = "Precio m&iacute;nimo sugerido por hora, sin incluir traslados: $".str_replace(".", ",", $data["hora"]);
?>
<div class="control-group">
	<label class="control-label" for="precio">&iquest;Cu&aacute;nto quer&eacute;s pagar por la changuita?<br /><small>En pesos argentinos</small></label>
	<div class="controls">
		<div class="input-prepend">
			<span class="add-on">$</span><input type="text" class="input-mini" id="precio" name="precio" value="<?php echo $data["precio"] ?>" maxlength="5" />
		</div>
		<a class="ayuda" title="Este campo es obligatorio. Ingres&aacute; un n&uacute;mero entero, sin centavos. No uses punto, coma ni el signo de pesos. Por ej.: 1234. Solo pod&eacute;s subir el precio."><i class="icon-question-sign"></i></a><p id="precio-sugerido"><?php echo $precioSugerido ?></p><span class="help-block"></span>
	</div>
</div>
<div class="control-group">
	<label class="control-label control-label-sin-margen">Servicio<br /><small>Eleg&iacute; qu&eacute; servicio de SoloChanguitas quer&eacute;s contratar</small></label>
	<div class="controls">
<?php
if($balance > 0) {
?>
		<div class="alert alert-success alert-balance">Record&aacute; que ten&eacute;s $<?php echo $balance ?> de cr&eacute;dito para usar en cualquiera de los planes.</div>
<?php
}
$encontrado = 0;
foreach($plan as $k => $v) {
	$sel = "";
	$planClass = "";
	$planInputClass = "";
	$planInputAttr = "";
	if($data["plan"] == $k) {
		$sel = "checked = 'checked'";
		$encontrado = 1;
	}
	else if($encontrado == 1) {
		$planClass = "plan-menor";
		$planInputClass = "disabled";
		$planInputAttr = "disabled = 'disabled'";
	}
?>
		<label class="plan<?php echo $k ?> <?php echo $planClass ?>">
			<input type="radio" id="plan<?php echo $k ?>" name="plan" value="<?php echo $k ?>" <?php echo $sel ?> class="<?php echo $planInputClass ?>" <?php echo $planInputAttr ?> />
			<div class="plan-nombre"><?php echo $v ?></div>
			<div class="plan-descripcion"><?php echo $planDescripcion[$k] ?></div>
			<div class="plan-precio">$ <?php echo $planPrecio[$k] ?>,00</div>
		</label>
<?php
}
?>
		<span class="help-block"></span>
		<div class="center"><img src="http://imgmp.mlstatic.com/org-img/banners/ar/medios/785X40.jpg" title="MercadoPago - Medios de pago" alt="MercadoPago - Medios de pago" class="imgMP" /></div>
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="changuita-fecha">Fecha de publicaci&oacute;n</label>
	<div class="controls">
		<p class="disabled disabled-corto"><?php echo $data["fecha"] ?></p>
	</div>
</div>
<p><strong>Importante</strong>: si elegiste un plan pago y todav&iacute;a no pagaste la publicaci&oacute;n de esta changuita o si cambi&aacute;s de plan, al hacer click en el bot&oacute;n <em>Aceptar</em> te vamos a mostrar las opciones de pago. Si no complet&aacute;s el proceso de pago, la changuita se publicar&aacute; igual con el nuevo plan y el monto a pagar se te computar&aacute; como deuda.</p>
<div class="form-actions">
	<button class="btn btn-success btn-large" id="boton-submit">Aceptar</button>
	<span class="help-inline text-error" id="validar"></span>
</div>