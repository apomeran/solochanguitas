<?php
include_once("../includes/config.php");
include("header.php");
if(isset($_SESSION[SesionNivel]) && $_SESSION[SesionNivel] > 0) {
    $bd = conectar();
    if(!isset($_GET["id"]))
        $id = 0;
    else
        $id = $bd->real_escape_string($_GET["id"]);
    if($id > 0) {
        $sql = "select id from categorias where id = $id and activo = '1'";
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
    }
    include("menu.php");
    include("../class/funciones.php");
    $f = new Funciones();
    $valCategoria = "";
    if($id > 0) {
        $sql = "select categoria from categorias where id = $id and activo = '1'";
        $res = $bd->query($sql);
        $fila = $res->fetch_assoc();
        $valCategoria = $fila["categoria"];
    }
?>
<div class="row">
    <div class="span12">
        <form class="form-horizontal" id="editar-categoria">
            <fieldset>
                <input type="hidden" name="id" value="<?php echo $id ?>" />
                <div class="control-group">
                    <label class="control-label" for="pregunta">Categoría</label>
                    <div class="controls">
                        <input type="text" name="categoria" id="categoria" value="<?php echo $valCategoria ?>" class="span5" />
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-success btn-large" id="boton-submit">Enviar</button>
                    <span class="text-error" id="validar"></span>
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
    $('#item02').addClass('active');
</script>
<?php
include("footer.php");
?>