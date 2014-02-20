<?php
class Bloqueo {
	protected $tiempo = 60;
	protected $maximo = 5;
	protected $pena = 300;
	protected $ahora;
	
	public function __construct() {
		$this->ahora = time();
		$_SESSION["login"][] = $this->ahora;
	}
	public function bloquear() {
		$ventana = $this->ahora - $this->tiempo;
		$intentos = 0;
		foreach($_SESSION["login"] as $v) {
			if($v >= $ventana)
				$intentos++;
		}
		if($intentos > $this->maximo) {
			$_SESSION["bloquear"] = $this->ahora + $this->pena;
			return true;
		}
		$_SESSION["bloquear"] = $this->ahora;
		return false;
	}
}
?>