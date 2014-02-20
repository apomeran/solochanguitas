<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("includes/config.php");
include_once("class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
include_once("class/funciones.php");
$f = new Funciones();
$bd = conectar();
// datos usuario
$sql = "select usu.nombre, usu.sexo, usu.nacimiento, usu.localidad, usu.barrio, usu.celular, usu.educacion, usu.institucion, usu.presentacion, usu.balance, con.confianza, cal.calificacion, cal.n from usuarios as usu left join confianza as con on usu.id = con.usuario left join calificacion as cal on usu.id = cal.usuario where usu.id = ".$_SESSION[SesionId]." and usu.activo = '2'";
$res = $bd->query($sql);
$fila = $res->fetch_assoc();
$perfil = array("sexo", "nacimiento", "localidad", "localidad", "barrio", "celular", "educacion", "institucion");
$nPerfil = count($perfil)+3;
$perfilOk = 3;
foreach($perfil as $v) {
	if(!in_array($fila[$v], $valoresVacios))
		$perfilOk++;
}
$perfilx100 = round($perfilOk/$nPerfil*100);
$presentacionx100 = 0;
$presentacionLen100 = 200;
if($fila["presentacion"] != "") {
	$presentacionLen = strlen($fila["presentacion"]);
	if($presentacionLen >= $presentacionLen100)
		$presentacionx100 = 100;
	else
		$presentacionx100 = round($presentacionLen/$presentacionLen100*100);
}
// categorias totales
$sql = "select sc.id from subcategorias as sc left join categorias as c on sc.categoria = c.id where c.activo = '1' and sc.activo = '1'";
$res = $bd->query($sql);
$nCat = $res->num_rows;
// categorias elegidas
$sql = "select uc.id from usuarios_categorias as uc left join subcategorias as s on uc.categoria = s.id where uc.usuario = ".$_SESSION[SesionId]." and s.activo = '1'";
$res = $bd->query($sql);
$nCatE = $res->num_rows;
$catx100 = $nCatE/$nCat*100;
// changuitas realizadas
$sql = "select id from changuitas where contratado = ".$_SESSION[SesionId]." and activo = '1' and (estado = '2' or estado = '3')";
$res = $bd->query($sql);
$nChanguitas = $res->num_rows;
// changuitas publicadas
$sql = "select id from changuitas where usuario = ".$_SESSION[SesionId]." and activo = '1'";
$res = $bd->query($sql);
$nPublicadas = $res->num_rows;
//
$calificacion = $fila["calificacion"];
$calificacionTxt = "";
if($fila["n"] == 0) {
	$calificacion = -1;
	$calificacionTxt = "<em>(no tiene)</em>";
}
$confianza = $fila["confianza"];
if($fila["confianza"] == "")
	$confianza = 0;
// deuda
$bloqueado = 0;
if($fila["balance"] <= MaxDeuda*-1)
	$bloqueado = 1;
$_SESSION[SesionBloqueado] = $bloqueado;
$bienvenido = "Bienvenido/a";
if($fila["sexo"] == 1)
	$bienvenido = "Bienvenida";
else if($fila["sexo"] == 2)
	$bienvenido = "Bienvenido";
?>
<div id="fb-root"></div>
<div id="columna-ok">
	<ul>
		<li>&iexcl;<?php echo $bienvenido ?>, <strong><?php echo $fila["nombre"] ?></strong>!</li>
<?php
if($_SESSION[SesionNivel] > 0) {
?>
		<li><i class="icon-briefcase"></i><strong><a href="admin">Panel de control</a></strong></li>
<?php
}
?>
		<li class="titulo">Notificaciones<div id="notificacionN"><span class="notificacion badge"><button class="btn-notificaciones"></button></span></div></li>
		<li class="titulo">Mi estado</li>
<?php
if($fila["balance"] < 0) {
?>
		<li class="balance">Deuda
			<p class="num num2 num3 num-deuda"><a href="#/pagar-deuda" rel="address:/pagar-deuda" class="btn btn-success btn-columna-pagar">Pagar</a> $<?php echo sprintf("%01.2f", $fila["balance"]*-1) ?></p>
		</li>
<?php
	if($bloqueado == 1) {
?>
		<li class="li-deuda">Alcanzaste el l&iacute;mite permitido de deuda. Por eso, tu usuario est&aacute; bloqueado y no pod&eacute;s publicar nuevas changuitas ni postularte para hacerlas.</li>
<?php
	}
}
else if($fila["balance"] > 0) {
?>
		<li class="balance">Cr&eacute;dito
			<p class="num num2 num3 num-credito">$<?php echo sprintf("%01.2f", $fila["balance"]) ?></p>
		</li>
<?php
}
?>
		<li>Calificaciones <?php echo $calificacionTxt ?><span class="indicador"><?php echo $f->indicador($calificacion, "calificacion") ?></span></li>
		<li>Contactos en la red<span class="indicador"><?php echo $f->indicador($confianza, "confianza") ?></span><span class="num"><?php echo $confianza ?></span></li>
		<li>Changuitas<span class="indicador"><?php echo $f->indicador($nChanguitas+$nPublicadas, "changuitas") ?></span><span class="num"><?php echo $nChanguitas+$nPublicadas ?></span></li>
		<li>
			<div class="pre-progress">
				Datos personales<br/>
				<div class="progress progress-striped">
					<div class="bar bar-success" style="width: <?php echo $perfilx100 ?>%;"></div>
					<div class="bar bar-gris" style="width: <?php echo 100-$perfilx100 ?>%;"></div>
				</div>
				<p class="progress-percent num num2"><?php echo $perfilx100 ?>% <a class="ayuda" title="Cuantos m&aacute;s datos pon&eacute;s, m&aacute;s f&aacute;cil va a ser que encuentres lo que busc&aacute;s. Entr&aacute; a MI PERFIL y edit&aacute; tus datos."><i class="icon-question-sign"></i></a></p>
				<div class="clearfix"></div>
			</div>
		</li>
		<li>
			<div class="pre-progress">
				Presentaci&oacute;n<br/>
				<div class="progress progress-striped">
					<div class="bar bar-success" style="width: <?php echo $presentacionx100 ?>%;"></div>
					<div class="bar bar-gris" style="width: <?php echo 100-$presentacionx100 ?>%;"></div>
				</div>
				<p class="progress-percent num num2"><?php echo $presentacionx100 ?>% <a class="ayuda" title="Entr&aacute; a MI PERFIL y complet&aacute; o modific&aacute; tu carta de presentaci&oacute;n."><i class="icon-question-sign"></i></a></p>
				<div class="clearfix"></div>
			</div>
		</li>
		<li class="titulo">Quiero trabajar</li>
		<li><i class="icon-wrench"></i><a href="#/postulaciones" rel="address:/postulaciones">Mis postulaciones</a></li>
		<li><i class="icon-comment"></i><a href="#/preguntas2" rel="address:/preguntas2">Preguntas</a></li>
		<li class="titulo">Quiero contratar</li>
		<li><i class="icon-list"></i><a href="#/mis-changuitas" rel="address:/mis-changuitas">Mis changuitas</a></li>
		<li><i class="icon-comment"></i><a href="#/preguntas" rel="address:/preguntas">Preguntas</a></li>
		<li class="titulo">Herramientas</li>
		<li><i class="icon-user"></i><a href="#/mi-perfil|<?php echo $_SESSION[SesionId] ?>" rel="address:/mi-perfil|<?php echo $_SESSION[SesionId] ?>">Mi perfil</a></li>
		<li><i class="icon-thumbs-up"></i><a href="#/calificaciones" rel="address:/calificaciones">Mis calificaciones</a></li>
		<li><i class="icon-star"></i><a href="#/invitar" rel="address:/invitar">Contactos en la red</a></li>
		<li><i class="icon-off"></i><a href="logout.php">Cerrar sesi&oacute;n</a></li>
	</ul>
</div>
<script>
$(document).ready(function() {
	actualizarNotificaciones();
});
</script>