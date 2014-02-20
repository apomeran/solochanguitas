<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../class/seguridad.php");
$s = new Seguridad;
$s->permitir(0);
$bd = conectar();
include_once("../class/funciones.php");
$f = new Funciones();
$tipo = 0;
if(isset($_POST["tipo"]))
	$tipo = $bd->real_escape_string($_POST["tipo"]);
$_SESSION["calificaciones-filtros"] = $tipo;
$data["html"] = "<table class='tabla-vista tabla-calificaciones table table-bordered table-hover'><thead><tr><th class='tipo'>Tipo</th><th>Calificaci&oacute;n</th><th class='ch'>Changuita</th><th class='botones'></th></tr></thead><tbody>";
$sql = "select ch.id, ch.titulo, ch.usuario, ch.contratado, cat.categoria, subcat.subcategoria from changuitas as ch left join categorias as cat on ch.categoria = cat.id left join subcategorias as subcat on ch.subcategoria = subcat.id where (ch.usuario = ".$_SESSION[SesionId]." or ch.contratado = ".$_SESSION[SesionId].") and ch.activo = '1' and (ch.estado = '1' or ch.estado = '2') order by fecha_contratacion desc";
$res = $bd->query($sql);
if($res->num_rows == 0)
	$data["html"] .= "<tr><td colspan='0'>No hay ninguna</td></tr>";
else {
	$mostrar = array();
	$mostrar[1] = array();
	$mostrar[2] = array();
	$mostrar[3] = array();
	while($fila = $res->fetch_assoc()) {
		$sql2 = "select cal.usuario, cal.calificacion, cal.comentario, cal.fecha, usu.nombre, usu.apellido from calificaciones as cal left join usuarios as usu on cal.usuario = usu.id where cal.changuita = ".$fila["id"]." and cal.activo = '1'";
		$res2 = $bd->query($sql2);
		if($res2->num_rows == 0) {
			// va a pendientes
			$mostrar[1][] = array(
				"estado"		=>"Pendiente",
				"calificacion"	=>"",
				"comentario"	=>"",
				"fecha"			=>"",
				"nombre"		=>"",
				"cat"			=>$fila["categoria"],
				"subcat"		=>$fila["subcategoria"],
				"titulo"		=>$fila["titulo"],
				"changuita"		=>$fila["id"],
				"classFila"		=>"error",
				"bot"			=>"<button class='btn btn-block btn-success btn-vista-ver' data-changuita-id='".$fila["id"]."'>Ver changuita</button><button class='btn btn-block btn-warning btn-calificar' data-changuita-id='".$fila["id"]."'>Calificar</button>"
			);
		}
		else {
			// 1 o 2
			$recibida = 0;
			$realizada = 0;
			while($fila2 = $res2->fetch_assoc()) {
				if($fila2["usuario"] == $_SESSION[SesionId]) {
					$recibida++;
					$contraparte = $fila["usuario"];
					if($fila["usuario"] == $fila2["usuario"])
						$contraparte = $fila["contratado"];
					$sql3 = "select nombre, apellido from usuarios where id = $contraparte";
					$res3 = $bd->query($sql3);
					$fila3 = $res3->fetch_assoc();
					$mostrar[2][] = array(
						"estado"		=>"Recibida",
						"calificacion"	=>$fila2["calificacion"],
						"comentario"	=>$fila2["comentario"],
						"fecha"			=>$fila2["fecha"],
						"nombre"		=>"De: <strong>".$fila3["nombre"]." ".substr($fila3["apellido"], 0 , 1).".</strong>",
						"cat"			=>$fila["categoria"],
						"subcat"		=>$fila["subcategoria"],
						"titulo"		=>$fila["titulo"],
						"changuita"		=>$fila["id"],
						"classFila"		=>"warning",
						"bot"			=>"<button class='btn btn-block btn-success btn-vista-ver' data-changuita-id='".$fila["id"]."'>Ver changuita</button>"
					);
				}
				else {
					$realizada++;
					$mostrar[3][] = array(
						"estado"		=>"Realizada",
						"calificacion"	=>$fila2["calificacion"],
						"comentario"	=>$fila2["comentario"],
						"fecha"			=>$fila2["fecha"],
						"nombre"		=>"A: <strong>".$fila2["nombre"]." ".substr($fila2["apellido"], 0 , 1).".</strong>",
						"cat"			=>$fila["categoria"],
						"subcat"		=>$fila["subcategoria"],
						"titulo"		=>$fila["titulo"],
						"changuita"		=>$fila["id"],
						"classFila"		=>"info",
						"bot"			=>"<button class='btn btn-block btn-success btn-vista-ver' data-changuita-id='".$fila["id"]."'>Ver changuita</button>"
					);
				}
			}
			if($realizada == 0) {
				$mostrar[1][] = array(
					"estado"		=>"Pendiente",
					"calificacion"	=>"",
					"comentario"	=>"",
					"fecha"			=>"",
					"nombre"		=>"",
					"cat"			=>$fila["categoria"],
					"subcat"		=>$fila["subcategoria"],
					"titulo"		=>$fila["titulo"],
					"changuita"		=>$fila["id"],
					"classFila"		=>"error",
					"bot"			=>"<button class='btn btn-block btn-success btn-vista-ver' data-changuita-id='".$fila["id"]."'>Ver changuita</button><button class='btn btn-block btn-warning btn-calificar' data-changuita-id='".$fila["id"]."'>Calificar</button>"
				);
			}
		}
	}
	// mostrar tipo o sumar para todas
	if($tipo == 0)
		$mostrar[0] = array_merge($mostrar[1], $mostrar[2], $mostrar[3]);
	if(count($mostrar[$tipo]) == 0)
		$data["html"] .= "<tr><td colspan='0'>No hay ninguna</td></tr>";
	else {
		foreach($mostrar[$tipo] as $v) {
			$calificacion = "";
			if($v["calificacion"] != "")
				$calificacion = $f->indicador($v["calificacion"], "calificacion");
			$data["html"] .= "<tr class='".$v["classFila"]."'><td>".$v["estado"]."</td><td><span>$calificacion</span><p class='calificacion'>".$v["comentario"]."</p><p class='nombre'>".$v["nombre"]."</p><p class='fecha'>".$f->convertirMuestra($v["fecha"], "fecha")."</p></td><td><h6>".$v["cat"]." &gt; ".$v["subcat"]."</h6><p class='ch'>".$v["titulo"]."</p></td><td class='botones'>".$v["bot"]."</td></tr>";
		}
	}
}
$data["html"] .= "</tbody></table>";
echo json_encode($data);
?>