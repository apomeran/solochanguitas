<?php
include_once("../includes/config.php");
include("header.php");
if(isset($_SESSION[SesionNivel]) && $_SESSION[SesionNivel] > 0) {
    $bd = conectar();
    include("menu.php");
    if(!isset($_GET["id"]))
        $id = 0;
    else
        $id = $bd->real_escape_string($_GET["id"]);
    $categorias = array();
    $sql = "select id, categoria from categorias where activo = '1' order by orden asc";
    $res = $bd->query($sql);
    while($fila = $res->fetch_assoc())
        $categorias[$fila["id"]] = $fila["categoria"];
    $orden = array(1=>"Categoría + Orden (ascendente)", "Categoría + Orden (descendente)", "Categoría + Nombre (ascendente)", "Categoría + Nombre (descendente)");
    $limit = array(15=>15, 30=>30, 50=>50, 100=>100);
?>
<div class="row">
    <form id="filtros">
        <div class="span1">
            <a href="editar-subcategoria.php" class="btn btn-info"><i class="icon-asterisk icon-white"></i>Nuevo</a>
        </div>
        <div class="span2">
            <label><span>Buscar:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <input name="buscar" type="text" value="" class="buscar" />
            <button class="btn btn-buscar"><i class="icon-search"></i></button>
        </div>
        <div class="span4">
            <label><span>Categoría:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="categoria2" class="span4">
                <option value="-1">Todas</option>
<?php
foreach ($categorias as $k => $v) {
    $sel = "";
    if($k == $id)
        $sel = "selected";
?>
                <option value="<?php echo $k ?>" <?php echo $sel ?>><?php echo $v ?></option>
<?php
}
?>
            </select>
        </div>
        <div class="span2">
            <label>Ordenar por:</label>
            <select name="orden" class="span2">
<?php
foreach($orden as $k => $v) {
    if($v == "")
        continue;
?>
                <option value="<?php echo $k ?>"><?php echo $v ?></option>
<?php
}
?>
            </select>
        </div>
        <div class="span2">
            <label>Mostrar:</label>
            <select name="limit" class="span2">
<?php
foreach($limit as $k => $v) {
?>
                <option value="<?php echo $k ?>"><?php echo $v ?></option>
<?php
}
?>
            </select>
        </div>
        <div class="span1">
            <button class="btn btn-success btn-exportar tooltip2" title="Genera un archivo de excel (usa los filtros activos)" data-tabla='subcategorias'><i class="icon-file icon-white"></i>Exportar</button>
        </div>
    </form>
</div>
<div class="row">
    <div class="span12">
        <table class="table table-bordered table-condensed table-hover vista" id="subcategorias">
            <thead>
                <tr>
                    <th>Subcategoría</th>
                    <th>Categoría</th>
                    <th>Precio por hora</th>
                    <th>Orden</th>
                    <th class="acciones"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5"></td>
                </tr>
            </tbody>
        </table>
        <div class="paginacion"></div>
    </div>
</div>
<div id="error" class="alert alert-error hide">
    <p></p>
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