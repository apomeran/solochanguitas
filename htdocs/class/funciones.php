<?php
class Funciones {

	public function convertirCarga($dato, $tipo) {
		switch($tipo) {
			case "fecha":
				return $this->fechaSQL(trim($dato));
				break;
			case "check":
				return implode(",", $dato);
				break;
			default:
				$dato = str_replace('"', "'", $dato);
				return trim($dato);
				break;
		}
	}

	public function convertirMuestra($dato, $tipo) {
		switch($tipo) {
			case "fecha":
				return $this->fechaNormal($dato);
				break;
			case "hace":
				$timestamp = strtotime($dato);
				$ahora = strtotime("now");
				$difEnSeg = $ahora - $timestamp;
				if($difEnSeg < 0)
					return "?";
				else if($difEnSeg < 60)
					return $difEnSeg." segundos";
				else if($difEnSeg < 3600)
					return round($difEnSeg/60)." minutos";
				else if($difEnSeg < (3600*48))
					return round($difEnSeg/3600)." horas";
				else
					return round($difEnSeg/(3600*24))." d&iacute;as";
				break;
			case "check":
				return explode(",", $dato);
				break;
			default:
				return $dato;
				break;
		}
	}

	public function generarClave($largo = 10) {
		$cadena = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($cadena)-1;
		$clave = "";
		for($i=0;$i<$largo;$i++)
			$clave .= $cadena[rand(0, $len)];
		return $clave;
	}

	public function borrarTmp() {
		$d = "tmp";
		$ahora = time();
		$unDia = 60 * 60 * 24;
		$dir = scandir($d);
		foreach($dir as $v) {
			if($v == "." || $v == "..")
				continue;
			$fecha = filemtime("$d/$v");
			if($fecha < $ahora - $unDia && $fecha !== false)
				unlink("$d/$v");
		}
		clearstatcache();
	}

	public function indicador($valor, $tipo) {
		if($valor < 0)
			return "<img src='img/nd.gif' alt='' />";
		$n = 5;
		$min = 0;
		switch($tipo) {
			case "calificacion":
				$max = 2;
				break;
			case "confianza":
				$max = 30;
				break;
			case "changuitas":
				$max = 10;
				break;
		}
		$intervalo = $max / ($n-1);
		$r = round($valor / $intervalo);
		if($r < 0)
			$r = 0;
		if($r > $n-1)
			$r = $n-1;
		return "<img src='img/$r.gif' alt='' />";
	}

	public function filtrarTxt($txt) {
		$txt = preg_replace("/\s+/", " ", $txt);
		$txt = preg_replace("/[0-9\-\s]{8,}/", " *** ", $txt);
		$txt = preg_replace("/([a-zA-Z0-9_\.\-])+(\s+)?@(\s+)?(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]+)/", " *** ", $txt);
		return $txt;
	}

	protected function fechaNormal($dato) {
		if(in_array($dato, $GLOBALS["valoresVacios"]))
			return "";
		$e = explode(" ", $dato);
		if(!isset($e[1]))
			$e[1] = "";
		$fecha = explode("-", $e[0]);
		return trim($fecha[2]."/".$fecha[1]."/".$fecha[0]." ".$e[1]);
	}
	protected function fechaSQL($dato) {
		if(in_array($dato, $GLOBALS["valoresVacios"]))
			return "0000-00-00";
		$e = explode(" ", $dato);
		if(!isset($e[1]))
			$e[1] = "";
		$fecha = explode("/", $e[0]);
			return trim($fecha[2]."-".$fecha[1]."-".$fecha[0]." ".$e[1]);
	}
}
?>