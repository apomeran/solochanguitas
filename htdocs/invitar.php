<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("includes/config.php");
$bd = conectar();
?>
<h3>Contactos en la red</h3>
<div class="row" id="invitar-container">
	<div class="span5">
		<h4>Tus contactos</h4>
<?php
$sql = "select u.nombre, u.apellido from invitados as inv left join usuarios as u on inv.mail = u.mail where inv.usuario = ".$_SESSION[SesionId]." and inv.ok = '1' and u.activo = '2'";
$res = $bd->query($sql);
if($res->num_rows > 0) {
	while($fila = $res->fetch_assoc()) {
?>
	<p><?php echo $fila["nombre"]." ".$fila["apellido"] ?></p>
<?php
	}
}
else {
?>
	<p>Todav&iacute;a no ten&eacute;s</p>
<?php
}
?>
	</div>
	<div class="span4">
		<h4>Generar contactos</h4>
		<p>Import&aacute; tus contactos desde:</p>
		<ul class="ul-invitar">
		   <!--  <li><button class="btn-link btn-invitar-fbx">desde Facebook</button></li>
		    <li><button class="btn-link btn-invitar-lix">desde LinkedIn</button></li> -->
		    <li><button class="btn-link btn-invitar-gm"><img src="img/invitar/gmail.gif" alt="Gmail"></button></li>
		    <!-- <li><button class="btn-link btn-invitar-hmx">desde Hotmail</button></li> -->
		    <!-- <li><button class="btn-link btn-invitar-yhx">desde Yahoo</button></li> -->
		</ul>
		<p>O si no, <button class="btn-link btn-invitar-manual">escrib&iacute; las direcciones manualmente</button></p>
		<!-- <p><button class="btn-link btn-invitar-manual">Escrib&iacute; las direcciones manualmente</button></p> -->
		<div id="invitar-res" class="hide">
			<form id="form-invitar"></form>
			<div><input type="submit" class="btn btn-success btn-invitar-submit" value="Invitar" /></div>
		</div>
		<div></div>
		<div id="form-invitar-mensaje" class="alert alert-error hide"></div>
	</div>
</div>