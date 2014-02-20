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
        $sql = "select id from subcategorias where id = $id and activo = '1'";
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
    $valCat = 0;
    $valSubcat = "";
    $valHora = "";
    if($id > 0) {
        $sql = "select id, subcategoria, categoria, hora from subcategorias where id = $id and activo = '1'";
        $res = $bd->query($sql);
        $fila = $res->fetch_assoc();
        $valCat = $fila["categoria"];
        $valSubcat = $fila["subcategoria"];
        $valHora = $fila["hora"];
    }
    $categorias = array();
    $sql = "select id, categoria from categorias where activo = '1' order by orden asc";
    $res = $bd->query($sql);
    while($filaC = $res->fetch_assoc())
        $categorias[$filaC["id"]] = $filaC["categoria"];
?>
<div class="row">
    <div class="span12">
        <form class="form-horizontal" id="editar-subcategoria">
            <fieldset>
                <input type="hidden" name="id" value="<?php echo $id ?>" />
                <div class="control-group">
                    <label class="control-label" for="categoria">Categoría</label>
                    <div class="controls">
                        <select name="categoria" id="categoria" class="span5">
                            <option value="-1">--- elegir ---</option>
            <?php
            foreach ($categorias as $k => $v) {
                $sel = "";
                if($k == $valCat)
                    $sel = "selected";
            ?>
                            <option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
            <?php
            }
            ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="subcategoria">Subcategoría</label>
                    <div class="controls">
                        <input type="text" name="subcategoria" id="subcategoria" value="<?php echo $valSubcat ?>" class="span5" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="hora">Precio por hora</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on">$</span>
                            <input type="text" name="hora" id="hora" value="<?php echo $valHora ?>" class="span1" />
                        </div>
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
    $('#item06').addClass('active');
</script>
<?php
include("footer.php");
?>