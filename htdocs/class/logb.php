<?php
class LogBalance {
    protected $bd;
    protected $ahora;
    /*
    tipo
    1   cobro plan (debito)
    2   cobro fee (debito)
    3   devuelvo plan, no se hizo (credito)
    4   paga plan (credito)
    5   paga fee (credito)
    */

    public function __construct() {
        $this->bd = conectar();
        $this->ahora = date("Y-m-d H:i:s");
    }
    public function log($usuario, $changuita, $cantidad, $tipo) {
        $sql = "insert into logb (usuario, changuita, cantidad, tipo, fecha) values ($usuario, $changuita, $cantidad, $tipo, '".$this->ahora."')";
        $this->bd->query($sql);
    }
}
?>