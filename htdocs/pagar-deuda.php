<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("includes/config.php");
include_once("class/funciones.php");
$bd = conectar();
$f = new Funciones;
?>
<h3>Deuda</h3>
<div id="pagar-deuda-container">
	<h4>Por changuitas publicadas</h4>
<?php
$deuda1 = 0;
$deuda2 = 0;
$sql = "select ch.id, ch.titulo, ch.fecha, p.plan, ch.debe, ch.diferencia from changuitas as ch left join planes as p on ch.plan = p.id where ch.usuario = ".$_SESSION[SesionId]." and ch.pagado = '0' and ch.debe > 0";
$res = $bd->query($sql);
if($res->num_rows == 0) {
?>
	<p class="left no-deuda">No ten&eacute;s deuda</p>
<?php
}
else {
?>
	<table class="tabla-vista table table-bordered table-hover">
		<thead>
			<tr>
				<th class="fecha2">Fecha</th>
				<th>Changuita</th>
				<th>Plan</th>
				<th>Deuda</th>
			</tr>
		</thead>
		<tbody>
<?php
	while($fila = $res->fetch_assoc()) {
		$monto = $fila["debe"];
		if($fila["diferencia"] > 0)
			$monto = $fila["diferencia"];
		$deuda1 += $monto;
?>
			<tr>
				<td><?php echo $f->convertirMuestra($fila["fecha"], "fecha") ?></td>
				<td><p class="ch"><?php echo $fila["titulo"] ?></p></td>
				<td><?php echo $fila["plan"] ?></td>
				<td class="deuda-precio">$<?php echo sprintf("%01.2f", $monto) ?></td>
				<input type="hidden" name="pagar-ch[]" value="<?php echo $fila["id"] ?>" />
			</tr>
<?php
	}
?>
			<tr class="error">
				<td colspan="4" class="deuda-precio deuda-total">SUBTOTAL DEUDA: $<?php echo sprintf("%01.2f", $deuda1) ?></td>
			</tr>
		</tbody>
	</table>
<?php
}
?>
<h4>Por changuitas realizadas</h4>
<?php
$sql = "select id, titulo, fecha, fee_debe from changuitas where contratado = ".$_SESSION[SesionId]." and fee_debe > 0 and fee = '0'";
$res = $bd->query($sql);
if($res->num_rows == 0) {
?>
	<p class="left no-deuda">No ten&eacute;s deuda</p>
<?php
}
else {
?>
	<table class="tabla-vista table table-bordered table-hover">
		<thead>
			<tr>
				<th class="fecha2">Fecha</th>
				<th>Changuita</th>
				<th>Deuda</th>
			</tr>
		</thead>
		<tbody>
<?php
	while($fila = $res->fetch_assoc()) {
		$monto = $fila["fee_debe"];
		$deuda2 += $monto;
?>
			<tr>
				<td><?php echo $f->convertirMuestra($fila["fecha"], "fecha") ?></td>
				<td><p class="ch"><?php echo $fila["titulo"] ?></p></td>
				<td class="deuda-precio">$<?php echo sprintf("%01.2f", $monto) ?></td>
				<input type="hidden" name="pagar-fee[]" value="<?php echo $fila["id"] ?>" />
			</tr>
<?php
	}
?>
			<tr class="error">
				<td colspan="4" class="deuda-precio deuda-total">SUBTOTAL DEUDA: $<?php echo sprintf("%01.2f", $deuda2) ?></td>
			</tr>
		</tbody>
	</table>
<?php
}
if($deuda1 + $deuda2 > 0) {
?>
	<table class="tabla-vista table table-bordered table-hover">
		<tbody>
			<tr class="error">
				<td colspan="4" class="deuda-precio deuda-total">TOTAL DEUDA: $<?php echo sprintf("%01.2f", $deuda1+$deuda2) ?></td>
			</tr>
		</tbody>
	</table>
<?php
}
?>
<button class="btn btn-success btn-pagar-deuda">Pagar</button>
</div>
<div class="alert pagar-deuda-respuesta" style="display:none;"></div>