<?php
include_once("../includes/config.php");
include("header.php");
if(isset($_SESSION[SesionNivel]) && $_SESSION[SesionNivel] > 0) {
    $bd = conectar();
    if(!isset($_GET["id"])) {
?>
<div class="row">
    <div class="span12">
        <p>No existe la página solicitada.</p>
    </div>
</div>
<?php
        include("footer.php");
        exit;
    }
    $id = $bd->real_escape_string($_GET["id"]);
    $ch = $bd->real_escape_string($_GET["ch"]);
    $sql = "select id from preguntas where id = $id and activo = '1'";
    $res = $bd->query($sql);
    if($res->num_rows == 0) {
?>
<div class="row">
    <div class="span12">
        <p>No existe la página solicitada.</p>
    </div>
</div>
<?php
        include("footer.php");
        exit;
    }
    include("menu.php");
    include("../class/funciones.php");
    $f = new Funciones();
    $sql = "select p.respuesta, u.nombre, u.apellido, p.respuesta_fecha from preguntas as p left join usuarios as u on p.usuario = u.id where p.id = $id and p.activo = '1'";
    $res = $bd->query($sql);
    $fila = $res->fetch_assoc();
?>
<div class="row">
    <div class="span12">
        <form class="form-horizontal" id="editar-respuesta">
            <fieldset>
                <input type="hidden" name="id" value="<?php echo $id ?>" />
                <input type="hidden" name="ch" value="<?php echo $ch ?>" />
                <div class="control-group">
                    <label class="control-label" for="respuesta">Respuesta</label>
                    <div class="controls">
                        <textarea id="respuesta" name="respuesta" class="span5"><?php echo $fila["respuesta"] ?></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Fecha</label>
                    <div class="controls">
                        <input type="text" value="<?php echo $f->convertirMuestra($fila["respuesta_fecha"], "fecha") ?>" class="span5" disabled readonly />
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-success btn-large" id="boton-submit">Enviar</button>
                    <span class="help-inline text-error" id="validar"></span>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<?php
}
else {
?>
<div class="row">
    <div class="span12">
        <p>No tenés autorización para ver esta página. Logueate con un usuario autorizado.</p>
    </div>
</div>
<?php
}
?>
<script>
    $('#item03').addClass('active');
</script>
<?php
include("footer.php");
?>