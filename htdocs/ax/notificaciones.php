<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
include_once("../includes/config.php");
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$bd = conectar();
$mostrarN = 10;
$verTodas = 0;
$data["estado"] = "";
$data["html"] = "";
$sql = "select m.id, m.changuita, m.tipo, m.mensaje, m.extra, ch.titulo, cat.categoria from mensajes as m left join changuitas as ch on m.changuita = ch.id left join categorias as cat on ch.categoria = cat.id where m.usuario = ".$_SESSION[SesionId]." order by m.fecha desc";
$res = $bd->query($sql);
if($res->num_rows > $mostrarN) {
	$verTodas = 1;
	$sql .= " limit 0, ".$mostrarN;
	$res = $bd->query($sql);
}
while($fila = $res->fetch_assoc()) {
	// $botVer = "<br/><button class='btn btn-success btn-notificacion-ver' data-changuita-id='".$fila["changuita"]."' data-notificacion-id='".$fila["id"]."'>Ir a changuita</button>";
	// $botVerNoLeer = "<br/><button class='btn btn-success btn-notificacion-ver no-leer' data-changuita-id='".$fila["changuita"]."' data-notificacion-id='".$fila["id"]."'>Ir a changuita</button>";
	switch($fila["tipo"]) {
		case 1: // postulacion
			$txt = "Hay un nuevo postulante para tu changuita <strong>".$fila["titulo"]."</strong>";
			// $bot = $botVer;
			break;
		case 2: // pregunta
			$txt = "Hay una nueva pregunta para tu changuita <strong>".$fila["titulo"]."</strong>";
			// $bot = $botVerNoLeer;
			break;
		case 3: // elegido para changuita
			$txt = "Fuiste elegido para hacer la changuita <strong>".$fila["titulo"]."</strong>";
			// $bot = $botVer;
			break;
		case 4: // ch finalizada por usuario
			$txt = "Ten&eacute;s una calificaci&oacute;n pendiente para la changuita <strong>".$fila["titulo"]."</strong>";
			// $bot = $botVer;
			break;
		case 5: // respuesta
			$txt = "Hay una respuesta a tu pregunta en la changuita <strong>".$fila["titulo"]."</strong>";
			// $bot = $botVer;
			break;
		case 7: // calificacion
			$txt = "Recibiste una calificaci&oacute;n por la changuita <strong>".$fila["titulo"]."</strong>";
			// $bot = $botVer;
			break;
		case 8: // invitados
			$sql = "select nombre, apellido from usuarios where id = ".$fila["extra"];
			$res2 = $bd->query($sql);
			if($res2->num_rows != 1)
				continue;
			$fila2 = $res2->fetch_assoc();
			$txt = "<strong>".$fila2["nombre"]." ".$fila2["apellido"]."</strong> se sum&oacute; a tu red de contactos";
			// $bot = "";
			break;
		case 9: // postulacion rechazada
			$txt = "Tu postulaci&oacute;n para la changuita <strong>".$fila["titulo"]."</strong> fue rechazada. Puede ser porque la changuita fue borrada, venci&oacute; o porque fue elegido otro usuario para realizarla";
			// $bot = "";
			break;
		case 10: // vencio mi changuita
			$txt = "Venci&oacute; la publicaci&oacute;n de la changuita <strong>".$fila["titulo"]."</strong>";
			// $bot = "";
			break;
		case 11: // vencio mi changuita, pero me devuelven la plata
			$txt = "Venci&oacute; la publicaci&oacute;n de la changuita <strong>".$fila["titulo"]."</strong>. Como no tuviste postulantes, te devolvimos como cr&eacute;dito lo que pagaste al publicarla.";
			// $bot = "";
			break;
		case 12: // changuita no realizada, devuelve la plata
			$txt = "Como la changuita <strong>".$fila["titulo"]."</strong> no se realiz&oacute;, te devolvimos como cr&eacute;dito lo que pagaste al publicarla.";
			// $bot = "";
			break;
		case 13: // changuita por vencer
			$txt = "La changuita <strong>".$fila["titulo"]."</strong> vence en menos de una semana. Eleg&iacute; un postulante antes de que sea tarde.";
			// $bot = $botVer;
			break;
		case 14: // changuita nueva en mi subcat
			$txt = "Hay una changuita nueva en la categor&iacute;a <strong>".$fila["categoria"]."</strong>";
			// $bot = $botVer;
			break;
		case 15: // changuita gratis no realizada, devuelve 1 gratis
			$txt = "Como la changuita <strong>".$fila["titulo"]."</strong> no se realiz&oacute;, pod&eacute;s volver a usar la bonificaci&oacute;n y publicar otra changuita GRATIS.";
			// $bot = "";
			break;
		case 16: // fee
			$txt = "Ten&eacute;s que pagar el fee de la changuita <strong>".$fila["titulo"]."</strong>.";
			// $bot = "";
			break;

		case 6: // mensaje de admin
			$txt = $fila["mensaje"];
			// $bot = $botVer;
			// if($fila["changuita"] == 0)
			// 	$bot = "";
			break;
	}
	// $data["html"] .= "<div class='notif'><p class='left'>$txt $bot</p><button class='btn-link btn-notificacion-leer' data-notificacion-id='".$fila["id"]."'><i class='icon-trash'></i></button><div class='clearfix'></div></div>";
	$data["html"] .= "<div class='notif'><p class='left'><a href='#/changuita|".$fila["changuita"]."' class='btn-notificaciones-cerrar'>$txt</a></p></div>";
}
if($verTodas == 1)
	$data["html"] .= "<button class='btn btn-link btn-notificaciones-todas'>Ver todas</button>";
if($res->num_rows > 0)
	$data["estado"] = "ok";
else
	$data["estado"] = "n0";
echo json_encode($data);
?>