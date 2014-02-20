<?php
class Notificaciones {
    protected $bd;
    protected $ahora;

    public function __construct() {
        $this->bd = conectar();
        $this->ahora = date("Y-m-d H:i:s");
    }
    public function reset($changuita) {
        // borra todos las notificaciones para una canguita
        $sql = "update mensajes set leido = '1' where changuita = $changuita";
        $this->bd->query($sql);
    }
    public function rechazar($changuita) {
        // notifica a todos los postulantes, menos el elegido
        $sql = "select p.usuario from postulaciones as p left join changuitas as ch on p.changuita = ch.id where p.changuita = $changuita and p.usuario != ch.contratado";
        $res = $this->bd->query($sql);
        $notificar = array();
        while($fila = $res->fetch_assoc())
            $notificar[] = "(".$fila["usuario"].", ".$changuita.", 9, '".$this->ahora."')";
        if(count($notificar) > 0) {
            $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values ".implode(", ", $notificar).";";
            $this->bd->query($sql);
        }
    }
    public function vencio($changuita, $devolucion) {
        // notifica al propietario
        $sql = "select usuario from changuitas where id = $changuita";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $tipo = 11;
        if($devolucion == 0)
            $tipo = 10;
        else if($devolucion == 2)
            $tipo = 15;
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values (".$fila["usuario"].", ".$changuita.", $tipo, '".$this->ahora."')";
        $this->bd->query($sql);
    }
    public function calificar($changuita, $usuario) {
        // notifica al calificado
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values ($usuario, $changuita, 7, '".$this->ahora."')";
        $this->bd->query($sql);
        // resetea pendiente calificador
        $sql = "update mensajes set leido = '1' where tipo = 4 and changuita = $changuita and usuario = ".$_SESSION[SesionId];
        $this->bd->query($sql);
    }
    public function contratar($changuita, $usuario) {
        // notifico a contratado
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values ($usuario, $changuita, 3, '".$this->ahora."')";
        $this->bd->query($sql);
        // marco mensaje de postulantes
        $sql = "update mensajes set leido = '1' where tipo = 1 and changuita = $changuita and usuario = ".$_SESSION[SesionId];
        $this->bd->query($sql);
    }
    public function calificacionPendiente($changuita, $usuario) {
        // notifica a ambos
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values (".$usuario.", $changuita, 4, '".$this->ahora."')";
        $this->bd->query($sql);
    }
    public function postular($changuita, $extra) {
        // notifica al propietario
        $sql = "select usuario from changuitas where id = $changuita and estado = '0' and activo = '1' and vencida = '0'";
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        $fila = $res->fetch_assoc();
        // resetea si hay (pq se despostulo y volvio a postularse)
        $sql = "update mensajes set leido = '1' where usuario = ".$fila["usuario"]." and changuita = $changuita and tipo = 1";
        $this->bd->query($sql);
        // carga
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha, extra) values (".$fila["usuario"].", $changuita, 1, '".$this->ahora."', $extra)";
        $this->bd->query($sql);
    }
    public function preguntar($changuita, $extra) {
        // notifica al propietario
        $sql = "select usuario from changuitas where id = $changuita and estado = '0' and activo = '1' and vencida = '0'";
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        $fila = $res->fetch_assoc();
        // resetea si hay
        $sql = "update mensajes set leido = '1' where usuario = ".$fila["usuario"]." and changuita = $changuita and tipo = 2";
        $this->bd->query($sql);
        // carga
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha, extra) values (".$fila["usuario"].", $changuita, 2, '".$this->ahora."', $extra)";
        $this->bd->query($sql);
    }
    public function responder($idPregunta) {
        // notifica a quien pregunto
        $sql = "select usuario, changuita from preguntas where id = $idPregunta and activo = '1'";
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        $fila = $res->fetch_assoc();
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values (".$fila["usuario"].", ".$fila["changuita"].", 5, '".$this->ahora."')";
        $this->bd->query($sql);
        // reseteo respuesta pendiente
        $sql = "update mensajes set leido = '1' where tipo = 2 and changuita = ".$fila["changuita"]." and usuario = ".$_SESSION[SesionId]." and extra = $idPregunta";
        $this->bd->query($sql);
    }
    public function noHecha($changuita, $usuario) {
        // notifica a propietario
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values ($usuario, $changuita, 12, '".$this->ahora."')";
        $this->bd->query($sql);
    }
    public function fee($changuita, $usuario) {
        // notifica a empleado
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values ($usuario, $changuita, 16, '".$this->ahora."')";
        $this->bd->query($sql);
    }
    public function porVencer($changuita) {
        // notifica a propietario
        $sql = "select usuario from changuitas where id = $changuita";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values (".$fila["usuario"].", ".$changuita.", 13, '".$this->ahora."')";
        $this->bd->query($sql);
    }
    public function nuevaChanguita($changuita) {
        $sql = "select cat.categoria, ch.subcategoria from changuitas as ch left join categorias as cat on ch.categoria = cat.id where ch.id = $changuita and ch.activo = '1' and ch.vencida = '0' and cat.activo = '1'";
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        $fila = $res->fetch_assoc();
        $categoria = $fila["categoria"];
        $sql = "select usu.id from usuarios as usu left join usuarios_categorias as uc on usu.id = uc.usuario where usu.activo = '2' and usu.nivel = '0' and uc.categoria = ".$fila["subcategoria"]." and usu.id != ".$_SESSION[SesionId];
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        while($fila = $res->fetch_assoc()) {
            $sql = "insert into mensajes (usuario, changuita, tipo, fecha) values (".$fila["id"].", ".$changuita.", 14, '".$this->ahora."')";
            $this->bd->query($sql);
        }
    }

}
?>