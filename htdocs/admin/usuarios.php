<?php
include_once("../includes/config.php");
include("header.php");
if(isset($_SESSION[SesionNivel]) && $_SESSION[SesionNivel] > 0) {
    $bd = conectar();
    include("menu.php");
    $zonas = array();
    $sql = "select id, localidad from localidades where activo = '1'";
    $res = $bd->query($sql);
    while($fila = $res->fetch_assoc())
        $zonas[$fila["id"]] = $fila["localidad"];
    $orden = array(1=>"Apellido (ascendente)", "Apellido (descendente)", "Fecha de alta (ascendente)", "Fecha de alta (descendente)");
    $limit = array(15=>15, 30=>30, 50=>50, 100=>100);
?>
<div class="row">
    <form id="filtros">
        <div class="span2">
            <label><span>Buscar:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <input name="buscar" type="text" value="" class="buscar" />
            <button class="btn btn-buscar"><i class="icon-search"></i></button>
        </div>
        <div class="span1">
            <label><span>Sexo:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="sexo" class="span1">
                <option value="-1">Todos</option>
                <option value="1">F</option>
                <option value="2">M</option>
            </select>
        </div>
        <div class="span2">
            <label><span>Zona:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="localidad2" class="span2">
                <option value="-1">Todos</option>
<?php
foreach($zonas as $k => $v) {
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
            <label><span>Estado:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="estado" class="span2">
                <option value="-1">Todos</option>
                <option value="0">Deuda</option>
                <option value="1">Crédito</option>
            </select>
        </div>
        <div class="span1">
            <label><span>Activo:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="activo" class="span1">
                <option value="-1">Todos</option>
                <option value="1">No</option>
                <option value="2">Sí</option>
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
        <div class="span1">
            <label>Mostrar:</label>
            <select name="limit" class="span1">
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
            <button class="btn btn-success btn-exportar tooltip2" title="Genera un archivo de excel (usa los filtros activos)" data-tabla='usuarios'><i class="icon-file icon-white"></i>Exportar</button>
        </div>
    </form>
</div>
<div class="row">
    <div class="span12">
        <table class="table table-bordered table-condensed table-hover vista" id="usuarios">
            <thead>
                <tr>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>E-mail</th>
                    <th>Sexo</th>
                    <th>Año de nac.</th>
                    <th>Localidad</th>
                    <th>Estado</th>
                    <th>Activo</th>
                    <th>Fecha de alta</th>
                    <th class="acciones"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10"></td>
                </tr>
            </tbody>
        </table>
        <div class="paginacion"></div>
    </div>
</div>
<div id="error" class="alert alert-error hide">
    <p></p>
</div>
<div class="modal hide fade" id="modal-mas">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Datos completos</h3>
    </div>
    <div class="modal-body">
        <div id="modal-data"></div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Cerrar</button>
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
    $('#item00').addClass('active');
</script>
<?php
include("footer.php");
?>