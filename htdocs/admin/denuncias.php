<?php
include_once("../includes/config.php");
include("header.php");
if(isset($_SESSION[SesionNivel]) && $_SESSION[SesionNivel] > 0) {
    $bd = conectar();
    include("menu.php");
    $orden = array(1=>"Fecha (descendente)", "Fecha (ascendente)");
    $limit = array(15=>15, 30=>30, 50=>50, 100=>100);
?>
<form id="filtros">
    <div class="row">
        <div class="span4">
            <label>Tipo:</label>
            <select name="tipo" class="span4">
                <option value="-1">Todos</option>
                <option value="u">Usuario</option>
                <option value="ch">Changuita</option>
                <option value="p">Pregunta</option>
                <option value="r">Respuesta</option>
            </select>
        </div>
        <div class="span3">
            <label><span>Visto:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="visto" class="span3">
                <option value="-1">Todos</option>
                <option value="1">No</option>
                <option value="0">Sí</option>
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
            <button class="btn btn-success btn-exportar tooltip2" title="Genera un archivo de excel (usa los filtros activos)" data-tabla='denuncias'><i class="icon-file icon-white"></i>Exportar</button>
        </div>
    </div>
</form>
<div class="row">
    <div class="span12">
        <table class="table table-bordered table-condensed table-hover vista" id="denuncias">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Datos</th>
                    <th>Comentario</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th class="acciones"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6"></td>
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
    $('#item05').addClass('active');
</script>
<?php
include("footer.php");
?>