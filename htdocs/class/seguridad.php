<?php
class Seguridad {

	protected $caducarSesion = 21600;	// 6hs
	protected $error = "";

	public function __construct() {
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
	}
	public function permitir($nivel) {
		$ok = 1;
		$ahora = time();
		if(!isset($_SESSION[SesionId]) || $_SESSION[SesionId] == 0) {
			$ok = 0;
			$this->error = "<div class='alert alert-error'><p>No tiene autorizaci&oacute;n para acceder a esta p&aacute;gina</p></div>";
		}
		else if($_SESSION[SesionTime] + $this->caducarSesion < $ahora) {
			$ok = 0;
			$this->error = "<div class='alert alert-error'><p>Pas&oacute; mucho tiempo sin actividad y, por seguridad, se cerr&oacute; tu sesi&oacute;n.<br/><a class='btn btn-success' href='index.php'>Volver a ingresar</a>.</p></div>";
		}
		else if($_SESSION[SesionNivel] < $nivel) {
			$ok = 0;
			$this->error = "<div class='alert alert-error'><p>No tiene autorizaci&oacute;n para acceder a esta p&aacute;gina</p></div>";
		}
		if($ok == 0)
			$this->salir(1);
	}
	public function salir($errorSalir = 0) {
		//session_destroy();
		if($errorSalir == 0)
			echo "<div class='alert alert-error'><p>No tiene autorizaci&oacute;n para acceder a esta p&aacute;gina</p></div>";
		else
			echo $this->error;
		exit;
	}
}
?>