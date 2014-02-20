<div class="alert alert-error">
	<p>Tu cuenta a&uacute;n no est&aacute; activa.</p>
</div>
<div id="reenviar">
<p>Te enviamos un e-mail con un link de confirmaci&oacute;n.<br/>Si no lo recibiste, podemos reenvi&aacute;rtelo (no te olvides de revisar el correo no deseado o <em>spam</em>).</p>
<p><button class="btn btn-primary" id="btn-reenviar">Reenviar e-mail con link de confirmaci&oacute;n</button></p>
<p>&iquest;Ya hiciste click en el link que te lleg&oacute; por e-mail y no pas&oacute; nada?</p>
<p><a class="btn btn-success" href="index.php">S&iacute;, ya activ&eacute; mi cuenta</a></p>
</div>
<div id="reenviado" class="hide">
<p>El e-mail fue reenviado.</p>
</div>
<script>
$(document).ready(function() {
	$('#btn-reenviar').click(function(e) {
		e.preventDefault();
		$('#procesando').show();
		$.post('ax/reenviar-activacion.php', function() {
			$('#procesando').hide();
			$('#reenviar').hide('clip');
			$('#reenviado').show('clip');
		});
	});
});
</script>