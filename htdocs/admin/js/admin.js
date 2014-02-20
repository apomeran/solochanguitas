function carga(vista, ini) {
    if (!ini) {
        ini = 0;
    }
    var tabla = '#' + vista + ' tbody';
    var pag = '.paginacion';
    $(tabla).html('');
    $(pag).html('<img src="../img/cargando.gif" alt="cargando" />');
    $.post('ax/' + vista + '.php?' + Math.random(), $('#filtros').serialize() + '&ini=' + ini, function (data) {
        if (data.estado === 'ok') {
            $(tabla).html(data.tabla);
            $(pag).html(data.pag);
        } else {
            $(pag).html('Error');
        }
    }, "json");
}

function validaFiltro(t) {
    var v = $.trim(t.val());
    var exp;
    if (t.hasClass('buscar')) {
        exp = /^[a-zA-Z0-9\.@\sáéíóúüçñÁÉÍÓÚÜÇÑ]+$/;
        if (v === '' || !exp.test(v)) {
            t.val('');
            t.prev('label').children('span').removeClass('label').removeClass('label-success');
            t.prev('label').children('a').hide();
        } else {
            t.prev('label').children('span').addClass('label').addClass('label-success');
            t.prev('label').children('a').removeClass('hide').show();
        }
    } else {
        if (v === '-1') {
            t.prev('label').children('span').removeClass('label').removeClass('label-success');
            t.prev('label').children('a').hide();
        } else {
            t.prev('label').children('span').addClass('label').addClass('label-success');
            t.prev('label').children('a').removeClass('hide').show();
        }
    }
}

function verSubFiltro(t) {
    if(t.attr('name') === 'categoria' || t.attr('name') === 'localidad') {
        t.parent().next().children('select').attr('disabled', 'disabled').html('');
        t.parent().next().children('label').children('.btn-mini').hide();
        t.parent().next().children('label').children('span').removeClass('label label-success');
    }
}

function subFiltro(t) {
    var sub;
    if(t.attr('name') === 'categoria') {
        sub = 'subcategoria';
    } else if (t.attr('name') === 'localidad') {
        sub = 'barrio';
    } else {
        return;
    }
    verSubFiltro(t);
    var v = t.val();
    $('select[name="' + sub + '"]').addClass('cargando').attr('disabled', 'disabled').html('').val(-1);
    if(v != -1) {
        $.post('ax/filtro-' + sub + '.php', {id: v}, function(data) {
            $('select[name="' + sub + '"]').removeClass('cargando').removeAttr('disabled').html(data.html);
        }, "json");
    } else {
        $('select[name="' + sub + '"]').removeClass('cargando');
    }
}

function validar(form) {
    var s = true;
    $('.control-group').removeClass('error');
    switch(form) {
        case 'editar-categoria':
            var cat = $.trim($('#categoria').val());
            if(cat === '') {
                s = false;
                $('#categoria').parents('.control-group').addClass('error');
            }
            break;
        case 'editar-subcategoria':
            var cat = $('#categoria').val(),
                subcat = $.trim($('#subcategoria').val()),
                hora = $('#hora').val();
            if(cat === '-1') {
                s = false;
                $('#categoria').parents('.control-group').addClass('error');
            }
            if(subcat === '') {
                s = false;
                $('#subcategoria').parents('.control-group').addClass('error');
            }
            if(hora !== '' && (isNaN(parseInt(hora, 10)) || parseInt(hora, 10) < 0 )) {
                s = false;
                $('#hora').parents('.control-group').addClass('error');
            }
            break;
        case 'editar-pregunta':
        case 'editar-respuesta':
            break;
    }
    if(!s) {
        $('#validar').html('Error: revisá los campos marcados en rojo.');
    }
    return s;
}

$(document).ready(function () {
    var id,
        tabla = $('table.vista').attr('id');
    // filtros
    $('#filtros').on('change', 'select, input', function () {
        validaFiltro($(this));
        subFiltro($(this));
        $('#ini option:first').attr('selected', 'selected');
        carga(tabla, $('#ini').val());
    });
    $('#filtros').on('submit', function (e) {
        e.preventDefault();
    });
    $('#filtros').on('click', '.btn-mini', function () {
        $(this).hide();
        $(this).parent().nextAll('input').val('');
        $(this).parent().nextAll('select').val(-1);
        $(this).prev('span').removeClass('label label-success');
        $('#ini option:first').attr('selected', 'selected');
        verSubFiltro($(this).parent().next('select'));
        carga(tabla, $('#ini').val());
    });
    // paginacion
    $('.paginacion').on('click', '.pag-pri', function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $('#ini option:first').attr('selected', 'selected');
        $('#ini').change();
    });
    $('.paginacion').on('click', '.pag-ant', function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $('#ini option:selected').prev().attr('selected', 'selected');
        $('#ini').change();
    });
    $('.paginacion').on('click', '.pag-sig', function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $('#ini option:selected').next().attr('selected', 'selected');
        $('#ini').change();
    });
    $('.paginacion').on('click', '.pag-ult', function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $('#ini option:last').attr('selected', 'selected');
        $('#ini').change();
    });
    $('.paginacion').on('change', '#ini', function () {
        carga(tabla, $('#ini').val());
    });
    //
    $('body').tooltip({
        selector: '.tooltip2',
        html: false
    });
    // modals
    $('#procesando').modal({
        show: false
    });
    $('#modal-mas').modal({
        show: false
    });
    $('#modal-pagar').modal({
        show: false
    })
    $('#modal-borrar').modal({
        show: false
    })
    // acciones
    $('.vista').on('click', '.btn-ver', function () {
        var id = $(this).attr('data-id');
        var tabla = $(this).attr('data-tabla');
        $.post('ax/' + tabla + '-mas.php?' + Math.random(), {id: id}, function (data) {
            if (data.estado === 'ok') {
                $('#modal-data').html(data.tabla);
                $('#modal-mas').modal('show');
            }
        }, "json");
    });
    $('.vista').on('click', '.btn-borrar', function () {
        var id = $(this).attr('data-id');
        var tabla = $(this).attr('data-tabla');
        $('#modal-borrar input[name="id"]').val(id);
        $('#modal-borrar input[name="tabla"]').val(tabla);
        $('#modal-borrar').modal('show');
    });
    $('#modal-borrar').on('click', '.btn-borrar-ok', function () {
        var id = $(this).siblings('input[name="id"]').val();
        var tabla = $(this).siblings('input[name="tabla"]').val();
        $.post('ax/borrar.php?' + Math.random(), {id: id, tabla: tabla}, function (data) {
            if (data.estado === 'ok') {
                if(tabla === 'respuesta') {
                    tabla = 'preguntas';
                }
                carga(tabla);
            }
        }, "json");
    });
    $('.vista').on('click', '.btn-pagar', function () {
        var id = $(this).attr('data-id');
        $('#modal-pagar .btn-pagar-ok').show();
        $.post('ax/pagar-modal.php?' + Math.random(), {id: id}, function (data) {
            if(data.pagar === 0) {
                $('#modal-pagar .btn-pagar-ok').hide();
            }
            $('#modal-data-pagar').html(data.html);
            $('#modal-pagar').modal('show');
        }, "json");
    });
    $('#modal-pagar').on('click', '.btn-pagar-ok', function () {
        var id = $('#modal-data-pagar input[name="id"]').val();
        var item = 0;
        if($('#modal-data-pagar input[name="item"]').length > 0) {
            if($('#modal-data-pagar input[name="item"]:checked').length === 1) {
                item = $('#modal-data-pagar input[name="item"]:checked').val();
            } else {
                return;
            }
        }
        $.post('ax/pagar.php?' + Math.random(), {id: id, item: item}, function (data) {
            if (data.estado === 'ok') {
                carga(tabla);
            }
            $('#modal-pagar').modal('hide');
        }, "json");
    });
    $('.vista').on('click', '.btn-subir', function () {
        var id = $(this).attr('data-id');
        var tabla = $(this).attr('data-tabla');
        $.post('ax/orden.php?' + Math.random(), {id: id, tabla: tabla, d: 1}, function (data) {
            if (data.estado === 'ok') {
               carga(tabla);
            }
        }, "json");
    });
    $('.vista').on('click', '.btn-bajar', function () {
        var id = $(this).attr('data-id');
        var tabla = $(this).attr('data-tabla');
        $.post('ax/orden.php?' + Math.random(), {id: id, tabla: tabla, d: -1}, function (data) {
            if (data.estado === 'ok') {
               carga(tabla);
            }
        }, "json");
    });
    $('#filtros').on('click', '.btn-exportar', function () {
        var tabla = $(this).attr('data-tabla');
        $("#procesando").modal('show');
        $.post('ax/' + tabla + '-exportar.php', $('#filtros').serialize(), function(data) {
            $("#procesando").modal('hide');
            if(data.estado == 'ok') {
                window.open('xls/'+data.archivo, '_blank');
            }
            else if(data.estado == 'vacio') {
                $('#error p').html("No hay datos para exportar").show();
            }
            else {
                $('#error p').html("Error").show();
            }
        }, "json");
    });
    // formularios
    $('#boton-submit').click(function(e) {
        e.preventDefault();
        var file = $(this).parents('.form-horizontal').attr('id');
        $("#validar").html('');
        if(!validar(file)) {
            return;
        }
        $("#procesando").modal('show');
        $.post('ax/' + file + '.php', $('.form-horizontal').serialize(), function(data) {
            $("#procesando").modal('hide');
            if(data.estado == 'ok') {
                location.href = data.link;
            }
            else if(data.estado == 'forbidden') {
                $("#validar").html('No tenés autorización para ver esta página. Logueate con un usuario autorizado.');
            }
            else {
                $("#validar").html('Error');
            }
        }, "json");
    });
    // inicio
    if ($('table.vista').size()) {
        // filtros default
        if ($('table#sugerencias').size() || $('table#denuncias').size()) {
            $('select[name="visto"]').val(1);
            validaFiltro($('select[name="visto"]'));
        }
        if ($('table#preguntas').size()) {
            validaFiltro($('select[name="changuita"]'));
        }
        if ($('table#subcategorias').size()) {
            validaFiltro($('select[name="categoria2"]'));
        }
        carga(tabla);
    }
});