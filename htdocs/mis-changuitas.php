<?php
session_start();
$js = "";
if(isset($_SESSION["mis-changuitas-filtros"])) {
	$js .= "$('#mis-changuitas-filtros .btn').removeClass('active');";
	$js .= "$('#mis-changuitas-filtros .btn[value=".$_SESSION["mis-changuitas-filtros"]."]').addClass('active');";
}
?>
<div class="row">
	<div class="span9">
		<h3>Mis changuitas</h3>
		<div id="mis-changuitas-filtros" class="btn-group vista-filtros" data-toggle="buttons-radio">
			<button class="btn btn-info active" name="tipo" value="1">Changuitas pendientes</button>
			<button class="btn btn-info" name="tipo" value="2">Changuitas en curso</button>
			<button class="btn btn-info" name="tipo" value="3">Changuitas realizadas</button>
			<button class="btn btn-info" name="tipo" value="0">TODAS</button>
		</div>
		<div id="mis-changuitas-tabla"></div>
		<div class="resultados-cargando hide"><img src="img/cargando2.gif" alt="cargando" /></div>
	</div>
</div>
<script>
$(document).ready(function() {
	<?php echo $js ?>
	misChanguitas();
});
</script>