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
    $changuitas = array();
    $sql = "select id, titulo from changuitas order by titulo asc";
    $res = $bd->query($sql);
    while($fila = $res->fetch_assoc())
        $changuitas[$fila["id"]] = $fila["titulo"];
?>
<form id="filtros">
    <div class="row">
        <div class="span11">
            <label><span>Changuita:</span> <a class="btn btn-mini btn-warning pull-right hide" href="#">&times;</a></label>
            <select name="changuita" class="span11">
                <option value="-1">Todas</option>
<?php
foreach ($changuitas as $k => $v) {
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
        <div class="span1">
            <button class="btn btn-success btn-exportar tooltip2" title="Genera un archivo de excel (usa los filtros activos)" data-tabla='preguntas'><i class="icon-file icon-white"></i>Exportar</button>
        </div>
    </div>
</form>
<div class="row">
    <div class="span12">
        <table class="table table-bordered table-condensed table-hover vista" id="preguntas">
            <thead>
                <tr>
                    <th>Changuita</th>
                    <th>Pregunta</th>
                    <th>Fecha pregunta</th>
                    <th>Usuario</th>
                    <th class="acciones"></th>
                    <th>Respuesta</th>
                    <th>Fecha respuesta</th>
                    <th class="acciones"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="8"></td>
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
    $('#item03').addClass('active');
</script>
<?php
include("footer.php");
?>