<?php
include_once("../includes/config.php");
include("header.php");
if(isset($_SESSION[SesionNivel]) && $_SESSION[SesionNivel] > 0) {
    $bd = conectar();
    include("menu.php");
    $cats = array();
    $zonas = array();
    $planes = array();
    $sql = "select id, categoria from categorias where activo = '1'";
    $res = $bd->query($sql);
    while($fila = $res->fetch_assoc())
        $cats[$fila["id"]] = $fila["categoria"];
    $sql = "select id, localidad from localidades where activo = '1'";
    $res = $bd->query($sql);
    while($fila = $res->fetch_assoc())
        $zonas[$fila["id"]] = $fila["localidad"];
    $sql = "select id, plan from planes where activo = '1'";
    $res = $bd->query($sql);
    while($fila = $res->fetch_assoc())
        $planes[$fila["id"]] = $fila["plan"];
    $orden = array(1=>"Fecha (descendente)", "Fecha (ascendente)");
    $limit = array(15=>15, 30=>30, 50=>50, 100=>100);
?>
<form id="filtros">
    <div class="row">
        <div class="span2">
            <label><span>Buscar:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <input name="buscar" type="text" value="" class="buscar" />
            <button class="btn btn-buscar"><i class="icon-search"></i></button>
        </div>
        <div class="span2">
            <label><span>Categoría:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="categoria" class="span2">
                <option value="-1">Todos</option>
<?php
foreach($cats as $k => $v) {
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
            <label><span>Subcategoría:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="subcategoria" class="span2" disabled>
            </select>
        </div>
        <div class="span2">
            <label><span>Zona:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="localidad" class="span2">
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
            <label><span>Localidad/Barrio:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="barrio" class="span2" disabled>
            </select>
        </div>
        <div class="span2">
            <label><span>Plan:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="plan" class="span2">
                <option value="-1">Todos</option>
<?php
foreach($planes as $k => $v) {
    if($v == "")
        continue;
?>
                <option value="<?php echo $k ?>"><?php echo $v ?></option>
<?php
}
?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="span2">
            <label><span>Estado:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="estado" class="span2">
                <option value="-1">Todos</option>
                <option value="0">Publicada</option>
                <option value="1">En curso</option>
                <option value="2">Realizada</option>
                <option value="3">Realizada y calificada</option>
                <option value="4">Borrada</option>
            </select>
        </div>
        <div class="span2">
            <label><span>Pagada:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="pagado" class="span2">
                <option value="-1">Todos</option>
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>
        </div>
        <div class="span2">
            <label><span>Fee pagado:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="fee" class="span2">
                <option value="-1">Todos</option>
                <option value="0">No</option>
                <option value="1">Sí</option>
                <option value="2">Calificada como no hecha</option>
            </select>
        </div>
        <div class="span2">
            <label><span>Vencida:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="vencida" class="span2">
                <option value="-1">Todos</option>
                <option value="0">No</option>
                <option value="1">Sí</option>
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
            <button class="btn btn-success btn-exportar tooltip2" title="Genera un archivo de excel (usa los filtros activos)" data-tabla='changuitas'><i class="icon-file icon-white"></i>Exportar</button>
        </div>
    </div>
</form>
<div class="row">
    <div class="span12">
        <table class="table table-bordered table-condensed table-hover vista" id="changuitas">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Subcategoría</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Precio</th>
                    <th>Plan</th>
                    <th>Fecha</th>
                    <th>Preguntas</th>
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
<div class="modal hide fade" id="modal-pagar">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Confirmar pago</h3>
    </div>
    <div class="modal-body">
        <div id="modal-data-pagar"></div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-success btn-pagar-ok">Aceptar</button>
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
    $('#item01').addClass('active');
</script>
<?php
include("footer.php");
?>