<?php
class Mails {
    protected $bd;
    protected $ahora;
    protected $mailer;
    protected $bodyIni;
    protected $bodyFin;
    protected $calificaciones;
    protected $calificacionColor;

    public function __construct() {
        $this->bd = conectar();
        $this->ahora = date("Y-m-d H:i:s");
        $this->bodyIni = "<table style='width:600px;margin:10px auto;padding:10px 0;'><tr><td style='padding:0 0 10px;margin:0;'><a href='".Sitio."'><img src='".Sitio."/img/logo-mail.jpg' alt='SoloChanguitas' /></a></td></tr><tr><td style='padding:0 0 10px;margin:0;'><p style='padding:0 5px;margin:3px 0;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;'>";
        $this->bodyFin = "</p><p style='padding:0 5px;margin:10px 0 3px;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:14px;'>Saludos,<br/>Equipo SoloChanguitas</p></td></tr><tr><td style='margin:5px 0;padding:5px;border-top:1px solid #ccc'><p style='font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:11px;'>Podés ingresar al sitio y, desde &quot;Mi perfil&quot;, modificar las opciones para elegir qué e-mails querés recibir.</p></td></tr><tr><td style='margin:5px 0;padding:5px;border-top:1px solid #ccc'><p style='font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:11px;'>&copy; ".date("Y")." SoloChanguitas - Todos los derechos reservados.</p></td></tr></table>";
        $this->calificaciones = array("Negativo", "Neutro", "Positivo");
        $this->calificacionColor = array("#F2DEDE", "#FCF8E3", "#DFF0D8");
        $this->mailer = new PHPMailer;
        $this->mailer->isHTML(true);
        $this->mailer->From = MailFrom;
        $this->mailer->FromName = MailFromName;
        $this->mailer->Timeout = 30;
        $this->mailer->CharSet = "UTF-8";
        $this->mailer->ClearAddresses();
    }
    public function rechazar($changuita) {
        // mail a todos los postulantes, menos el elegido
        $sql = "select p.usuario, u.aviso_rech, u.mail, u.nombre, ch.titulo from postulaciones as p left join usuarios as u on p.usuario = u.id left join changuitas as ch on p.changuita = ch.id where u.activo = '2' and p.changuita = $changuita and p.usuario != ch.contratado";
        $res = $this->bd->query($sql);
        $usuarios = array();
        while($fila = $res->fetch_assoc()) {
            if($fila["aviso_rech"] == 1)
                $usuarios[$fila["mail"]] = $fila["nombre"];
            $titulo = $fila["titulo"];
        }
        if(count($usuarios) > 0) {
            $this->mailer->Subject = "Tu postulación para $titulo fue rechazada";
            foreach ($usuarios as $k => $v) {
                $this->mailer->Body = $this->bodyIni;
                $this->mailer->Body .= "Estimado/a $v:<br/>La changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a> venció, fue borrada o su propietario eligió a otro usuario para realizar la changuita. Por lo tanto, tu postulación fue rechazada.";
                $this->mailer->Body .= "<br/>".$this->bodyFin;
                $this->mailer->ClearAddresses();
                $this->mailer->AddAddress($k);
                $this->mailer->Send();
            }
        }
    }
    public function vencio($changuita, $devolucion, $republicar) {
        // mail al propietario
        $sql = "select u.aviso_ve, u.mail, u.nombre, ch.titulo from changuitas as ch left join usuarios as u on ch.usuario = u.id where u.activo = '2' and ch.id = $changuita";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_ve"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $titulo = $fila["titulo"];
            $this->mailer->Subject = "La publicación de tu changuita $titulo venció";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>La publicación de la changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a> venció.";
            if($devolucion == 1)
                $this->mailer->Body .= " Como no tuviste postulantes, te devolvimos como crédito lo que pagaste al publicarla.";
            else if($devolucion == 2)
                $this->mailer->Body .= " Como no tuviste postulantes, podés volver a usar la bonificación y publicar otra changuita GRATIS.";
            if($republicar == 1)
                $this->mailer->Body .= " ¿Querés volver a publicarla? <a href='".Sitio."/#/republicar|".$changuita."'>Hacé click acá</a>.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }
    public function calificar($changuita, $usuario) {
        // mail a calificado
        $sql = "select nombre, mail, aviso_cal from usuarios where id = $usuario and activo = '2'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_cal"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $sql = "select nombre, apellido from usuarios where id = ".$_SESSION[SesionId];
            $res = $this->bd->query($sql);
            $fila = $res->fetch_assoc();
            $calificador = $fila["nombre"]." ".$fila["apellido"];
            $sql = "select titulo from changuitas where id = $changuita";
            $res = $this->bd->query($sql);
            $fila = $res->fetch_assoc();
            $titulo = $fila["titulo"];
            $sql = "select calificacion, comentario from calificaciones where changuita = $changuita and usuario = $usuario and activo = '1'";
            $res = $this->bd->query($sql);
            $fila = $res->fetch_assoc();
            $calificacion = $fila["calificacion"];
            $comentario = trim($fila["comentario"]);
            $this->mailer->Subject = "Te calificaron por la changuita $titulo";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>Recibiste la siguiente calificación de parte de <strong>$calificador</strong> por la changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a>: <span style='background-color:".$this->calificacionColor[$calificacion].";'>".$this->calificaciones[$calificacion]."</span>";
            if($comentario != "")
                $this->mailer->Body .= "<br/>El usuario agregó: <blockquote>$comentario</blockquote>";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }
    public function contratar($changuita) {
        $sql = "select ch.usuario, ch.contratado, ch.titulo, ch.precio, ch.descripcion, l.localidad, b.barrio from changuitas as ch left join localidades as l on ch.localidad = l.id left join barrios as b on ch.barrio = b.id where ch.id = $changuita";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $usuario = $fila["usuario"];
        $contratado = $fila["contratado"];
        $titulo = $fila["titulo"];
        $precio = $fila["precio"];
        $descripcion = $fila["descripcion"];
        $localidad = $fila["localidad"];
        $barrio = $fila["barrio"];
        $sql = "select nombre, apellido, mail, celular_area, celular from usuarios where id = $usuario";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $uNombre = $fila["nombre"];
        $uNombreCompleto = $uNombre." ".$fila["apellido"];
        $uMail = $fila["mail"];
        // $uTel = $fila["telefono_area"]." ".$fila["telefono"];
        $uCel = $fila["celular_area"]." ".$fila["celular"];
        $sql = "select nombre, apellido, mail, celular_area, celular from usuarios where id = $contratado";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $cNombre = $fila["nombre"];
        $cNombreCompleto = $cNombre." ".$fila["apellido"];
        $cMail = $fila["mail"];
        // $cTel = $fila["telefono_area"]." ".$fila["telefono"];
        $cCel = $fila["celular_area"]." ".$fila["celular"];
        // mail a contratado con datos
        $this->mailer->ClearAddresses();
        $this->mailer->Subject = "¡Felicitaciones! Te eligieron para la changuita $titulo";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a $cNombre:<br/><br/>El usuario <strong>$uNombreCompleto</strong> te eligió para realizar la changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a>.<br/>Valor de la changuita: $".$precio."<br/>Tu fee: $".str_replace(".", ",", sprintf("%01.2f", $precio*Fee))."<br/><br/>Contactá a $uNombreCompleto<br/>E-mail: $uMail<br/>";
        // if(trim($uTel) != "")
        //     $this->mailer->Body .= "Tel.: $uTel<br/>";
        if(trim($uCel) != "")
            $this->mailer->Body .= "Cel.: $uCel<br/>";
        $this->mailer->Body .= "<br/>Combinen entre ustedes para realizar la changuita y para que $uNombre te pague en efectivo (o como prefieran).";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->AddAddress($cMail);
        $this->mailer->Send();
        // mail a propietario con datos
        $this->mailer->ClearAddresses();
        $this->mailer->Subject = "$uNombre, elegiste al usuario $cNombreCompleto para la changuita $titulo";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a $uNombre:<br/><br/>Elegiste a <strong>$cNombreCompleto</strong> para realizar la changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a>.<br/>Te recordamos el texto ingresado en tu publicación:<br/>Descripción: $descripcion<br/>Valor de la changuita: $".$precio."<br/>Zona: $localidad<br/>Localidad/Barrio: $barrio<br/><br/>Contactá a $cNombreCompleto<br/>E-mail: $cMail<br/>";
        // if(trim($cTel) != "")
        //     $this->mailer->Body .= "Tel.: $cTel<br/>";
        if(trim($cCel) != "")
            $this->mailer->Body .= "Cel.: $cCel<br/>";
        $this->mailer->Body .= "<br/>Combinen entre ustedes para realizar la changuita y para pagarle a $cNombre en efectivo (o como prefieran).";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->AddAddress($uMail);
        $this->mailer->Send();
    }
    public function calificacionPendiente($changuita, $usuario, $contraparte) {
        // mail a quien debe una calificacion
        $sql = "select nombre, mail, aviso_ca from usuarios where id = $usuario and activo = '2'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_ca"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $sql = "select nombre, apellido, mail, celular_area, celular from usuarios where id = $contraparte";
            $res = $this->bd->query($sql);
            $fila = $res->fetch_assoc();
            $cNombre = $fila["nombre"]." ".$fila["apellido"];
            $cMail = $fila["mail"];
            // $cTel = $fila["telefono_area"]." ".$fila["telefono"];
            $cCel = $fila["celular_area"]." ".$fila["celular"];
            $sql = "select usuario, titulo from changuitas where id = $changuita";
            $res = $this->bd->query($sql);
            $fila = $res->fetch_assoc();
            $titulo = $fila["titulo"];
            $txtContrato = "fuiste elegido por";
            if($usuario == $fila["usuario"])
                $txtContrato = "elegiste a";
            $this->mailer->Subject = "Tenés que calificar a $cNombre por la changuita $titulo";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>Ya pasaron varios días desde que $txtContrato $cNombre para realizar la changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a>. Por este motivo, te pedimos que lo/a califiques por su amabilidad, claridad y efectividad en el intercambio. Si la changuita ya fue concretada, podés <a href='".Sitio."/#/changuita|".$changuita."'>calificarlo/a ahora</a>. De no haberse concretado el intercambio en el tiempo indicado, podés indicarlo y calificar a tu contraparte para obtener una bonificación de los cargos. En ese caso, la reputación de $cNombre se verá afectada negativamente.<br/>Te recordamos sus datos de contacto:<br/>E-mail: $cMail<br/>";
            // if(trim($cTel) != "")
            //     $this->mailer->Body .= "Tel.: $cTel<br/>";
            if(trim($cCel) != "")
                $this->mailer->Body .= "Cel.: $cCel<br/>";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }
    public function postular($changuita) {
        // mail a propietario
        $sql = "select u.nombre, u.mail, u.aviso_np, ch.titulo from changuitas as ch left join usuarios as u on ch.usuario = u.id where ch.id = $changuita and ch.activo = '1' and u.activo = '2' and ch.vencida = '0' and ch.estado = '0'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_np"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $titulo = $fila["titulo"];
            $this->mailer->Subject = $nombre.", hay un nuevo postulante para tu changuita $titulo";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>Hay novedades en tu publicación <strong>$titulo</strong>. <a href='".Sitio."/#/changuita|".$changuita."'>Entrá al sitio</a> para ver quién se postuló.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }
    public function preguntar($changuita, $idPregunta) {
        // mail a propietario
        $sql = "select u.nombre, u.mail, u.aviso_pr, ch.titulo from changuitas as ch left join usuarios as u on ch.usuario = u.id where ch.id = $changuita and ch.activo = '1' and u.activo = '2' and ch.vencida = '0' and ch.estado = '0'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_pr"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $titulo = $fila["titulo"];
            $sql = "select pregunta from preguntas where id = $idPregunta";
            $res = $this->bd->query($sql);
            $fila = $res->fetch_assoc();
            $pregunta = $fila["pregunta"];
            $this->mailer->Subject = $nombre.", hicieron una pregunta en tu changuita $titulo";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>Hicieron la siguiente pregunta en tu changuita <strong>$titulo</strong>:<blockquote><strong>$pregunta</strong></blockquote><a href='".Sitio."/#/changuita|".$changuita."'>Entrá acá</a> para contestarla.<br/>Mientras más rápida sea tu respuesta, más aumentará la cantidad de postulantes a tu publicación, permitiéndote elegir luego entre mayor diversidad de perfiles.<br/>Recordá que no está permitido colocar datos de contacto, como direcciones de e-mail o teléfonos. En ese caso, tu publicación puede ser dada de baja.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }
    public function responder($idPregunta) {
        // mail a quien hizo la pregunta
        $sql = "select ch.id, u.nombre, u.mail, u.aviso_res, ch.titulo, p.respuesta from preguntas as p left join changuitas as ch on p.changuita = ch.id left join usuarios as u on p.usuario = u.id where p.id = $idPregunta and ch.activo = '1' and u.activo = '2' and ch.vencida = '0' and ch.estado = '0'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_res"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $titulo = $fila["titulo"];
            $respuesta = $fila["respuesta"];
            $this->mailer->Subject = $nombre.", respondieron tu pregunta en la changuita $titulo";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>Respondieron tu pregunta en lu changuita <strong>$titulo</strong>:<blockquote><strong>$respuesta</strong></blockquote><a href='".Sitio."/#/changuita|".$fila["id"]."'>Entrá acá</a> para volver a ver la publicación y evaluar postularte.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }
    public function nuevaChanguita($changuita) {
        $sql = "select cat.categoria, ch.subcategoria from changuitas as ch left join categorias as cat on ch.categoria = cat.id where ch.id = $changuita and ch.activo = '1' and ch.vencida = '0' and cat.activo = '1'";
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        $fila = $res->fetch_assoc();
        $categoriaNombre = $fila["categoria"];
        $subcategoria = $fila["subcategoria"];
        $sql = "select usu.nombre, usu.mail from usuarios as usu left join usuarios_categorias as uc on usu.id = uc.usuario where usu.activo = '2' and usu.nivel = '0' and usu.aviso = '1' and uc.categoria = $subcategoria and usu.id != ".$_SESSION[SesionId];
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        while($fila = $res->fetch_assoc()) {
            $this->mailer->Subject = $fila["nombre"].", hay una nueva changuita";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a ".$fila["nombre"].":<br/><br/>Acaban de publicar una changuita en la categoría <em>$categoriaNombre</em>. <a href='".Sitio."/#/changuita|$changuita'>Entrá ahora</a> y fijate si te interesa.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($fila["mail"]);
            $this->mailer->Send();
        }
    }
    public function olvido($usuario) {
        $sql = "select nombre, mail, recuperacion from usuarios where id = $usuario";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $link = Sitio."/recuperar.php?c=$usuario|".sha1($fila["recuperacion"].$usuario.SalRec);
        $this->mailer->Subject = "Recuperación de contraseña";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a ".$fila["nombre"].":<br/><br/>Recibimos una solicitud de recuperación de contraseña. Si no la pediste, ignorá este mensaje y seguí usando tu contraseña de siempre. Si olvidaste tu contraseña, hacé click en el siguiente link: <a href='$link'>$link</a>.";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($fila["mail"]);
        $this->mailer->Send();
    }
    public function activar($usuario) {
        $sql = "select fecha, nombre, mail from usuarios where id = $usuario";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $link = Sitio."/activar.php?c=".$usuario."|".sha1($fila["fecha"].$usuario.SalAct);
        $this->mailer->Subject = "Activación de cuenta";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a ".$fila["nombre"].":<br/><br/>¡Te damos la bienvenida a <strong>SoloChanguitas</strong>!<br/>Para activar tu cuenta, hacé click en el siguiente link:<br/><a href='$link'>$link</a>.";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($fila["mail"]);
        $this->mailer->Send();
    }
    public function reactivar($usuario) {
        $sql = "select fecha, nombre, mail from usuarios where id = $usuario and activo = '1'";
        $res = $this->bd->query($sql);
        if($res->num_rows == 0)
            return;
        $fila = $res->fetch_assoc();
        $link = Sitio."/activar.php?c=".$usuario."|".sha1($fila["fecha"].$usuario.SalAct);
        $this->mailer->Subject = "Activación de cuenta";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a ".$fila["nombre"].":<br/><br/>¡Te damos la bienvenida a <strong>SoloChanguitas</strong>!<br/>Para activar tu cuenta, hacé click en el siguiente link:<br/><a href='$link'>$link</a>.";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($fila["mail"]);
        $this->mailer->Send();
    }
    public function recordatorioActivar($usuario) {
        $sql = "select fecha, nombre, mail from usuarios where id = $usuario";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $link = Sitio."/activar.php?c=".$usuario."|".sha1($fila["fecha"].$usuario.SalAct);
        $this->mailer->Subject = "¡Activá tu cuenta para empezar a recibir las búsquedas!";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a ".$fila["nombre"].":<br/><br/>¡Te damos la bienvenida a <strong>SoloChanguitas</strong>!<br/>Para activar tu cuenta, hacé click en el siguiente link:<br/><a href='$link'>$link</a>.";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($fila["mail"]);
        $this->mailer->Send();
    }
    public function noHecha($changuita, $usuario) {
        // mail a propietario: devolucion de plata
        $sql = "select u.nombre, u.mail, u.aviso_bal, ch.titulo from changuitas as ch left join usuarios as u on ch.usuario = u.id where ch.id = $changuita and ch.activo = '1' and u.activo = '2'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_bal"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $titulo = $fila["titulo"];
            $this->mailer->Subject = $nombre.", recibiste crédito por la changuita $titulo";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>Como la changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a> no se realizó, te devolvimos como crédito lo que pagaste al publicarla.";
            $this->mailer->Body .= "<br/>¿Querés volver a publicarla? <a href='".Sitio."/#/republicar|".$changuita."'>Hacé click acá</a>.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }

    public function noHecha2($changuita, $usuario) {
        // mail a propietario: sin devolucion de plata, solo republicar
        $sql = "select u.nombre, u.mail, u.aviso_cal, ch.titulo from changuitas as ch left join usuarios as u on ch.usuario = u.id where ch.id = $changuita and ch.activo = '1' and u.activo = '2'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_cal"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $titulo = $fila["titulo"];
            $this->mailer->Subject = $nombre.", ¿Querés republicar la changuita $titulo?";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>La changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a> fue calificada como no realizada, ¿querés volver a publicarla? <a href='".Sitio."/#/republicar|".$changuita."'>Hacé click acá</a>.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }

    public function porVencer($changuita) {
        // mail al propietario
        $sql = "select u.aviso_pv, u.mail, u.nombre, ch.titulo from changuitas as ch left join usuarios as u on ch.usuario = u.id where u.activo = '2' and ch.id = $changuita";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        if($fila["aviso_pv"] == 1) {
            $mail = $fila["mail"];
            $nombre = $fila["nombre"];
            $titulo = $fila["titulo"];
            $this->mailer->Subject = "La publicación de tu changuita $titulo está por vencer";
            $this->mailer->Body = $this->bodyIni;
            $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>La publicación de la changuita <a href='".Sitio."/#/changuita|".$changuita."'><strong>$titulo</strong></a> vence en menos de una semana. <a href='".Sitio."/#/changuita|".$changuita."'>Elegí un postulante</a> antes de esa fecha (si no lo hacés, la changuita será dada de baja). Si todavía no tenés ninguno, <a href='".Sitio."/#/editar-changuita|".$changuita."'>elegí uno de nuestros planes</a> para que más gente vea tu publicación.";
            $this->mailer->Body .= "<br/>".$this->bodyFin;
            $this->mailer->ClearAddresses();
            $this->mailer->AddAddress($mail);
            $this->mailer->Send();
        }
    }
    public function resumenNuevas($changuitas, $usuario, $frecuencia) {
        // $frecuencia = diario / semanal
        if(count($changuitas) == 0)
            return;
        $ch = array();
        $cats = array();
        $subcats = array();
        foreach ($changuitas as $v) {
            $sql = "select ch.titulo, cat.categoria, sc.subcategoria, ch.categoria as cId, ch.subcategoria as sId from changuitas as ch left join categorias as cat on ch.categoria = cat.id left join subcategorias as sc on ch.subcategoria = sc.id where ch.id = $v";
            $res = $this->bd->query($sql);
            $fila = $res->fetch_assoc();
            $ch[$fila["cId"]][$fila["sId"]][] = "<p style='padding:5px;margin:0;font-family:Helvetica, Arial, sans-serif;font-weight:normal;font-size:18px;'><a href='".Sitio."/#/changuita|$v'>".$fila["titulo"]."</a></p>";
            $cats[$fila["cId"]] = $fila["categoria"];
            $subcats[$fila["sId"]] = $fila["subcategoria"];
        }
        $sql = "select nombre, mail from usuarios where id = $usuario and activo = '2'";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $this->mailer->Subject = $fila["nombre"].", hay nuevas changuitas";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a ".$fila["nombre"].":<br/><br/>";
        if($frecuencia == "diario")
            $this->mailer->Body .= "En las últimas 24 horas";
        else
            $this->mailer->Body .= "En la última semana";
        $this->mailer->Body .= " se publicaron las siguientes changuitas en las categorías que elegiste:";
        foreach ($ch as $k => $v) {
            $this->mailer->Body .= "<h4 style='padding:5px 5px 0;margin:5px 0 0;text-transform:uppercase;border-top:1px solid #999;'>".$cats[$k]."</h4>";
            foreach ($v as $kk => $vv) {
                $this->mailer->Body .= "<h4 style='padding:0 5px;margin:0;text-transform:uppercase;font-weight:normal;'> > ".$subcats[$kk]."</h4>";
                foreach ($vv as $vvv)
                    $this->mailer->Body .= $vvv;
            }
        }
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($fila["mail"]);
        $this->mailer->Send();
    }
    public function deuda($usuario) {
        $sql = "select mail, nombre, balance from usuarios where id = $usuario";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $mail = $fila["mail"];
        $nombre = $fila["nombre"];
        $deuda = $fila["balance"]*-1;
        $this->mailer->Subject = $nombre.", tenés una deuda que pagar";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>El objetivo de nuestro fee es mantener el sitio funcionando correctamente, ofrecerte soporte de calidad y continuar invirtiendo en el crecimiento de usuarios para que cada vez tengas más demanda de tus trabajos.<br/><br/>Tenés una deuda de $<strong>$deuda</strong>. <a href='".Sitio."/#/pagar-deuda'>Realizá el pago</a> y evitá ser suspendido en el sitio. El envío de este e-mail es suficiente notificación de deuda.";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($mail);
        $this->mailer->Send();
    }
    public function fee($changuita, $usuario) {
         $sql = "select u.mail, u.nombre, ch.titulo from changuitas as ch left join usuarios as u on ch.contratado = u.id where u.activo = '2' and ch.id = $changuita";
        $res = $this->bd->query($sql);
        $fila = $res->fetch_assoc();
        $mail = $fila["mail"];
        $nombre = $fila["nombre"];
        $titulo = $fila["titulo"];
        $this->mailer->Subject = $nombre.", tenés que pagar el fee por la changuita $titulo";
        $this->mailer->Body = $this->bodyIni;
        $this->mailer->Body .= "Estimado/a $nombre:<br/><br/>El objetivo de nuestro fee es mantener el sitio funcionando correctamente, ofrecerte soporte de calidad y continuar invirtiendo en el crecimiento de usuarios para que cada vez tengas más demanda de tus trabajos.<br/><br/>Tenés que <a href='".Sitio."/#/pagar-deuda'>pagar el fee</a> por la changuita <strong>$titulo</strong>. Realizá el pago y evitá ser suspendido en el sitio. El envío de este e-mail es suficiente notificación de deuda.";
        $this->mailer->Body .= "<br/>".$this->bodyFin;
        $this->mailer->ClearAddresses();
        $this->mailer->AddAddress($mail);
        $this->mailer->Send();
    }

}
?>