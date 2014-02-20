<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
$bd = conectar();
include_once("../class/funciones.php");
$f = new Funciones();
$filtros = array();
$cat = 0;
$subcat = 0;
$loc = 0;
$barrio = array();
$buscar = "";
if(isset($_POST["categoria"]))
	$cat = $bd->real_escape_string($_POST["categoria"]);
if(isset($_POST["subcategoria"]))
	$subcat = $bd->real_escape_string($_POST["subcategoria"]);
if(isset($_POST["localidad"]))
	$loc = $bd->real_escape_string($_POST["localidad"]);
if(isset($_POST["barrio"]))
	$barrio = $_POST["barrio"];
if(isset($_POST["palabras"]))
	$buscar = $bd->real_escape_string(trim($_POST["palabras"]));
if($cat > 0)
	$filtros[] = "ch.categoria = $cat";
if($subcat > 0)
	$filtros[] = "ch.subcategoria = $subcat";
if($loc > 0)
	$filtros[] = "ch.localidad = $loc";
if(count($barrio) > 0) {
	$filtroBarrio = array();
	foreach($barrio as $v)
		$filtroBarrio[] = "ch.barrio = ".$bd->real_escape_string($v);
	$filtros[] = "(".implode(" or ", $filtroBarrio).")";
}
if($buscar != "") {
	$palabras = preg_split("/[\s]+/", $buscar, null, PREG_SPLIT_NO_EMPTY);
	$filtroPalabras = array();
	foreach($palabras as $v) {
		$filtroPalabras[] = "ch.titulo regexp '$v'";
		$filtroPalabras[] = "ch.descripcion regexp '$v'";
		$filtroPalabras[] = "cat.categoria regexp '$v'";
		$filtroPalabras[] = "subcat.subcategoria regexp '$v'";
		$filtroPalabras[] = "pc.palabra regexp '$v'";
	}
	if(count($filtroPalabras) > 0)
		$filtros[] = "(".implode(" or ", $filtroPalabras).")";
}
$data["html"] = "<div class='resultados-tit'><h4 class='inicio-destacadas'>Resultados de la b&uacute;squeda</h4>";
$opOrden = array("Destacadas", "M&aacute;s reciente", "M&aacute;s antigua", "Mayor precio", "Menor precio");
$orden = 0;
if(isset($_POST["orden"]))
	$orden = $bd->real_escape_string($_POST["orden"]);
else if(isset($_SESSION["orden"]))
	$orden = $bd->real_escape_string($_SESSION["orden"]);
$validOrden = range(0, 4);
if(!in_array($orden, $validOrden))
	$orden = 0;
$_SESSION["orden"] = $orden;
switch($orden) {
	case 0:
		$ordenar = "field(ch.plan, '2') desc, ch.fecha desc";
		break;
	case 1:
		$ordenar = "ch.fecha desc";
		break;
	case 2:
		$ordenar = "ch.fecha asc";
		break;
	case 3:
		$ordenar = "ch.precio desc";
		break;
	case 4:
		$ordenar = "ch.precio asc";
		break;
}
$filtrar = "";
if(count($filtros) > 0)
	$filtrar = "and ".implode(" and ", $filtros);
$sql = "select ch.id, ch.titulo, ch.usuario, ch.precio, ch.fecha, cat.categoria, subcat.subcategoria from changuitas as ch left join categorias as cat on cat.id = ch.categoria left join subcategorias as subcat on subcat.id = ch.subcategoria left join changuitas_palabras as pc on pc.changuita = ch.id where ch.activo = '1' and ch.estado = '0' and ch.vencida = '0' $filtrar group by ch.id order by $ordenar";
$res = $bd->query($sql);
if($res->num_rows == 0)
	$data["html"] .= "<p class='no'>No hay resultados. Prob&aacute; con otra b&uacute;squeda.</p>";
else {
	$data["html"] .= "<div class='resultados-filtro'>Ordenar por <select name='orden' id='orden'>";
	foreach($opOrden as $k => $v) {
		$sel = "";
		if($k == $orden)
			$sel = "selected";
		$data["html"] .= "<option value='$k' $sel>$v</option>";
	}
	$data["html"] .= "</select></div></div>";
	$total = $res->num_rows;
	$ini = 0;
	if(isset($_POST["ini"]))
		$ini = $bd->real_escape_string($_POST["ini"]);
	else if(isset($_SESSION["ini"]))
		$ini = $bd->real_escape_string($_SESSION["ini"]);
	$_SESSION["ini"] = $ini;
	$sql .= " limit $ini, ".Limit;
	$res = $bd->query($sql);
	while($fila = $res->fetch_assoc()) {
		$chClass = "";
		$botPostular = "<button class='btn btn-primary btn-postular'>Postularme</button>";
		if(isset($_SESSION[SesionId])) {
			if($_SESSION[SesionId] == $fila["usuario"]) {
				$botPostular = "";
				$chClass = "changuita-mia";
			}
			else {
				$sqlP = "select id from postulaciones where changuita = ".$fila["id"]." and usuario = ".$_SESSION[SesionId];
				$resP = $bd->query($sqlP);
				if($resP->num_rows > 0) {
					$chClass = "changuita-ok";
					$botPostular = "<button class='btn btn-warning disabled' disabled>Ya est&aacute;s postulado</button>";
				}
				else
					$botPostular = "<button class='btn btn-primary btn-postular' data-changuita='".$fila["id"]."'>Postularme<div class='cargando hide'><img src='img/cargando2.gif' alt='cargando'/></div></button>";
			}
		}
		$sqlP = "select pos.id from postulaciones as pos left join usuarios as usu on pos.usuario = usu.id where pos.changuita = ".$fila["id"]." and usu.activo = '2'";
		$resP = $bd->query($sqlP);
		if($resP->num_rows == 0)
			$postulantes = "<em>Todav&iacute;a no hay ning&uacute;n postulante</em>";
		else if($resP->num_rows == 1)
			$postulantes = "Ya se postul&oacute; <strong>".$resP->num_rows."</strong> usuario";
		else
			$postulantes = "Ya se postularon <strong>".$resP->num_rows."</strong> usuarios";
		$sqlP = "select pre.id from preguntas as pre left join usuarios as usu on pre.usuario = usu.id where pre.changuita = ".$fila["id"]." and usu.activo = '2'";
		$resP = $bd->query($sqlP);
		if($resP->num_rows == 0)
			$preguntas = "<em>Todav&iacute;a no hay ninguna pregunta</em>";
		else if($resP->num_rows == 1)
			$preguntas = "Hay <strong>".$resP->num_rows."</strong> pregunta";
		else
			$preguntas = "Hay <strong>".$resP->num_rows."</strong> preguntas";
		$data["html"] .= "<div class='changuita $chClass' data-changuita-id='".$fila["id"]."'><div class='changuita-der'><p>Publicada hace <strong>".$f->convertirMuestra($fila["fecha"], "hace")."</strong></p><p>$postulantes</p><p>$preguntas</p><p class='precio'>$".$fila["precio"]."</p></div><h6>".$fila["categoria"]." &gt ".$fila["subcategoria"]."</h6><h4>".$fila["titulo"]."</h4><div><a class='btn btn-success' href='#/changuita|".$fila["id"]."' rel='address:/changuita|".$fila["id"]."'>Ver m&aacute;s</a> $botPostular</div></div>";
	}
	//paginar
	if($total > Limit) {
		$pags = ceil($total/Limit);
		$classPri = "";
		$classUlt = "";
		if($ini == 0)
			$classPri = "disabled";
		if($ini == ($pags-1)*Limit)
			$classUlt = "disabled";
		$data["html"] .= "<div class='pag'><button class='btn btn-primary $classPri' id='pag-pri' $classPri><i class='icon-step-backward'></i></button> <button class='btn btn-primary $classPri' id='pag-ant' $classPri><i class='icon-chevron-left'></i></button> P&aacute;gina <select name='ini' id='ini' class='input-mini'>";
		for($i=0;$i<$pags;$i++) {
			$sel = "";
			if($ini/Limit == $i)
				$sel = "selected";
			$data["html"] .= "<option value='".($i*Limit)."' $sel>".($i+1)."</option>";
		}
		$data["html"] .= "</select> de $pags | Total: <strong>$total</strong> changuitas <button class='btn btn-primary $classUlt' id='pag-sig' $classUlt><i class='icon-chevron-right'></i></button> <button class='btn btn-primary $classUlt' id='pag-ult' $classUlt><i class='icon-step-forward'></i></button></div>";
	}
}
echo json_encode($data);
?>