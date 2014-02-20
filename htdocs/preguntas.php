<?php
session_start();
$js = "";
if(isset($_SESSION["preguntas-filtros"])) {
	$js .= "$('#preguntas-filtros .btn').removeClass('active');";
	$js .= "$('#preguntas-filtros .btn[value=".$_SESSION["preguntas-filtros"]."]').addClass('active');";
}
?>
<div class="row">
	<div class="span9">
		<h3>Preguntas</h3>
		<div id="preguntas-filtros" class="btn-group vista-filtros" data-toggle="buttons-radio">
			<button class="btn btn-info active" name="tipo" value="1">Pendientes</button>
			<button class="btn btn-info" name="tipo" value="2">Respondidas</button>
			<button class="btn btn-info" name="tipo" value="0">TODAS</button>
		</div>
		<div id="preguntas-tabla"></div>
		<div class="resultados-cargando hide"><img src="img/cargando2.gif" alt="cargando" /></div>
	</div>
</div>
<script>
$(document).ready(function() {
	<?php echo $js ?>
	preguntas();
});
</script>