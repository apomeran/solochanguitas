// hack for iOS6 / Safari POST caching bug
$.ajaxSetup({
    type: 'POST',
    headers: { "cache-control": "no-cache" }
});
// Funciones
// - validacion general
function esMail(txt) {
    var mail = $.trim(txt);
    if (mail === '') {
        return true;
    }
    var exp = /^([a-zA-Z0-9_\.\-])+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]+)$/;
    if (!exp.test(mail)) {
        return false;
    }
    return true;
}

function esClave(txt) {
    var clave = $.trim(txt);
    if (clave === '') {
        return true;
    }
    if (clave.length < 8) {
        return false;
    }
    var exp = /^[a-zA-Z0-9]+$/;
    if (!exp.test(clave)) {
        return false;
    }
    return true;
}

function esFecha(txt) {
    var d = new Date();
    var anoActual = d.getFullYear();
    var fecha = $.trim(txt);
    if (fecha === '') {
        return true;
    }
    var exp = /^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/;
    if (!exp.test(fecha)) {
        return false;
    }
    var fechaSplit = fecha.split('/');
    var dia = parseInt(fechaSplit[0], 10);
    var mes = parseInt(fechaSplit[1], 10);
    var ano = parseInt(fechaSplit[2], 10);
    if (isNaN(dia) || dia < 1 || dia > 31) {
        return false;
    }
    if (isNaN(mes) || mes < 1 || mes > 12) {
        return false;
    }
    if (isNaN(ano) || ano < anoActual - 110 || ano > anoActual) {
        return false;
    }
    return true;
}

function esDia(txt) {
    var dia = $.trim(txt);
    var exp = /^[0-9]{1,2}$/;
    if (!exp.test(dia)) {
        return false;
    }
    dia = parseInt(dia, 10);
    if (isNaN(dia) || dia < 1 || dia > 31) {
        return false;
    }
    return true;
}

function esMes(txt) {
    var mes = $.trim(txt);
    var exp = /^[0-9]{1,2}$/;
    if (!exp.test(mes)) {
        return false;
    }
    mes = parseInt(mes, 10);
    if (isNaN(mes) || mes < 1 || mes > 12) {
        return false;
    }
    return true;
}

function esAno(txt) {
    var d = new Date();
    var anoActual = d.getFullYear();
    var ano = $.trim(txt);
    var exp = /^[0-9]{4}$/;
    if (!exp.test(ano)) {
        return false;
    }
    ano = parseInt(ano, 10);
    if (isNaN(ano) || ano < anoActual - 110 || ano > anoActual) {
        return false;
    }
    return true;
}

function esPrecio(n) {
    var num = $.trim(n);
    if (num === '') {
        return true;
    }
    var exp = /^[0-9]+$/;
    if (!exp.test(num)) {
        return false;
    }
    num = parseInt(num, 10);
    if (num <= 0 || isNaN(num)) {
        return false;
    }
    return true;
}

function esDNI(n) {
    var num = $.trim(n);
    if (num === '') {
        return true;
    }
    var exp = /^[0-9]+$/;
    if (!exp.test(num)) {
        return false;
    }
    num = parseInt(num, 10);
    if (num <= 1000000 || isNaN(num)) {
        return false;
    }
    return true;
}
// - validacion usuario

function validarUsuario() {
    var error = 0;
    var mail = $.trim($('#mail').val());
    var nombre = $.trim($('#nombre').val());
    var apellido = $.trim($('#apellido').val());
    var dni = $.trim($('#dni').val());
    var clave = $.trim($('#clave').val());
    var clave2 = $.trim($('#clave2').val());
    // var dia = $.trim($('#nacimiento_d option:selected').val());
    // var mes = $.trim($('#nacimiento_m option:selected').val());
    var ano = $.trim($('#nacimiento').val());
    var condiciones = $('#condiciones').is(':checked');
    var aviso = $("input[name='aviso']:checked").size();
    $('.help-block').html('');
    $('.error').removeClass('error');
    $('#validar').html('');
    if (!esMail(mail) || mail === '') {
        $('#mail').parent().parent().addClass('error');
        $('#mail').siblings('span').html('No es una direcci&oacute;n v&aacute;lida');
        error++;
    }
    if (nombre === '') {
        $('#nombre').parent().parent().addClass('error');
        $('#nombre').siblings('span').html('No puede estar vac&iacute;o');
        error++;
    }
    if (apellido === '') {
        $('#apellido').parent().parent().addClass('error');
        $('#apellido').siblings('span').html('No puede estar vac&iacute;o');
        error++;
    }
    if (!esDNI(dni) || dni === '') {
        $('#dni').parent().parent().addClass('error');
        $('#dni').siblings('span').html('Debe ser un n&uacute;mero de DNI v&aacute;lido, sin letras ni puntos');
        error++;
    }
    if ($('#clave').is(':visible')) {
        if (!esClave(clave) || clave === '') {
            $('#clave').parent().parent().addClass('error');
            $('#clave').siblings('span').html('Debe tener al menos 8 caracteres, solo letras y/o n&uacute;meros');
            error++;
        }
        if (!esClave(clave2) || clave2 === '') {
            $('#clave2').parent().parent().addClass('error');
            $('#clave2').siblings('span').html('Debe tener al menos 8 caracteres, solo letras y/o n&uacute;meros');
            error++;
        }
        if (clave !== '' && clave2 !== '') {
            if (clave !== clave2) {
                $('#clave').parent().parent().addClass('error');
                $('#clave2').parent().parent().addClass('error');
                $('#clave2').siblings('span').html('Ambas contrase&ntilde;as deben ser iguales');
                error++;
            }
        }
    }
    /*
    if(!esDia(dia)) {
        $('#nacimiento_d').parent().parent().addClass('error');
        $('#nacimiento_d').siblings('span').html('No es una fecha v&aacute;lida');
        error++;
    }
    if(!esMes(mes)) {
        $('#nacimiento_m').parent().parent().addClass('error');
        $('#nacimiento_m').siblings('span').html('No es una fecha v&aacute;lida');
        error++;
    }
    */
    if (!esAno(ano) && $.trim(ano) !== '') {
        $('#nacimiento').parent().parent().addClass('error');
        $('#nacimiento').siblings('span').html('No es un a&ntilde;o v&aacute;lido');
        error++;
    }
    if ($('#condiciones').is(':visible')) {
        if (!condiciones) {
            $('#condiciones').parent().parent().parent().addClass('error');
            $('#condiciones').parent().siblings('span').html('Hay que aceptar los t&eacute;rminos y condiciones para continuar');
            error++;
        }
    }
    if (aviso === 0) {
        $('#aviso1').parent().parent().parent().addClass('error');
        $('#aviso1').parent().siblings('span').html('Hay que elegir una opci&oacute;n');
        error++;
    }
    if (error === 0) {
        return true;
    }
    $('#validar').html('Error: revis&aacute; los campos marcados en rojo');
    return false;
}
// - validacion changuita

function validarChanguita() {
    var errorChanguita = 0,
        titulo = $.trim($('#titulo').val()),
        descripcion = $.trim($('#descripcion').val()),
        precio = $.trim($('#precio').val()),
        cuando = $('input[name="cuando"]:checked').val(),
        desdeH = $('select[name="desde_hora"]').val(),
        desdeM = $('select[name="desde_minuto"]').val(),
        hastaH = $('select[name="hasta_hora"]').val(),
        hastaM = $('select[name="hasta_minuto"]').val();
    $('.help-block').html('');
    $('.error').removeClass('error');
    $('#validar').html('');
    if (titulo === '') {
        $('#titulo').parent().parent().addClass('error');
        $('#titulo').siblings('span').html('No puede estar vac&iacute;o');
        errorChanguita++;
    }
    if (descripcion === '') {
        $('#descripcion').parent().parent().addClass('error');
        $('#descripcion').siblings('span').html('No puede estar vac&iacute;o');
        errorChanguita++;
    }
    if (!esPrecio(precio) || precio === '') {
        $('#precio').parent().parent().parent().addClass('error');
        $('#precio').parent().siblings('span').html('No es un valor v&aacute;lido');
        errorChanguita++;
    }
    if ($('#changuita-categoria option:selected').val() <= 0) {
        $('#changuita-categoria').parent().parent().addClass('error');
        $('#changuita-categoria').siblings('span').html('Ten&eacute;s que elegir una opci&oacute;n');
        errorChanguita++;
    }
    if ($('#changuita-subcategoria option:selected').val() === 0 && $('#changuita-subcategoria option').size() > 1) {
        $('#changuita-subcategoria').parent().parent().addClass('error');
        $('#changuita-subcategoria').siblings('span').html('Ten&eacute;s que elegir una opci&oacute;n');
        errorChanguita++;
    }
    if ($('#changuita-localidad option:selected').val() === 0) {
        $('#changuita-localidad').parent().parent().addClass('error');
        $('#changuita-localidad').siblings('span').html('Ten&eacute;s que elegir una opci&oacute;n');
        errorChanguita++;
    }
    if ($('#changuita-barrio option:selected').val() === 0 && $('#changuita-barrio').is(':visible')) {
        $('#changuita-barrio').parent().parent().addClass('error');
        $('#changuita-barrio').siblings('span').html('Ten&eacute;s que elegir una opci&oacute;n');
        errorChanguita++;
    }
    if (cuando === 2) {
        var diasCheck = $('input[name="cuando_dias[]"]:checked').size();
        if (diasCheck === 0) {
            $('#cuando1').parent().parent().parent().addClass('error');
            $('#cuando_dias1').parent().siblings('span').html('Ten&eacute;s que elegir por lo menos un d&iacute;a');
            errorChanguita++;
        }
    } else if (cuando === 3) {
        var fechaCh = $.trim($('#cuando_fecha').val());
        if (!esFecha(fechaCh) || fechaCh === '') {
            $('#cuando1').parent().parent().parent().addClass('error');
            $('#cuando_fecha').siblings('span').html('No es un valor v&aacute;lido');
            errorChanguita++;
        }
    }
    if (desdeH !== '-1' || hastaH !== '-1') {
        if (desdeH === '-1' || hastaH === '-1') {
            $('select[name="desde_hora"]').parent().parent().addClass('error');
            $('select[name="desde_hora"]').siblings('span').html('Ten&eacute;s que completar ambos horarios');
            errorChanguita++;
        } else if (hastaH < desdeH || (hastaH === desdeH && (hastaM < desdeM))) {
            $('select[name="desde_hora"]').parent().parent().addClass('error');
            $('select[name="desde_hora"]').siblings('span').html('El segundo horario no puede ser anterior al primero');
            errorChanguita++;
        }
    }
    if (errorChanguita === 0) {
        return true;
    }
    $('#validar').html('Error: revis&aacute; los campos marcados en rojo');
    return false;
}

function validarChanguitaEdit() {
    var errorChanguita = 0;
    var precio = $.trim($('#precio').val());
    $('.help-block').html('');
    $('.error').removeClass('error');
    $('#validar').html('');
    if (!esPrecio(precio) || precio === '') {
        $('#precio').parent().parent().parent().addClass('error');
        $('#precio').parent().siblings('span').html('No es un valor v&aacute;lido');
        errorChanguita++;
    }
    if (errorChanguita === 0) {
        return true;
    }
    $('#validar').html('Error: revis&aacute; los campos marcados en rojo');
    return false;
}
// - validacion contacto

function validarContacto() {
    var error = 0;
    var mail = $.trim($('#mail').val());
    var mensaje = $.trim($('#mensaje').val());
    $('.help-block').html('');
    $('.error').removeClass('error');
    $('#validar').html('');
    if (!esMail(mail) || mail === '') {
        $('#mail').parent().parent().addClass('error');
        $('#mail').siblings('span').html('No es una direcci&oacute;n v&aacute;lida');
        error++;
    }
    if (mensaje === '') {
        $('#mensaje').parent().parent().addClass('error');
        $('#mensaje').siblings('span').html('No puede estar vac&iacute;o');
        error++;
    }
    if (error === 0) {
        return true;
    }
    $('#validar').html('Error: revis&aacute; los campos marcados en rojo');
    return false;
}
// - login

function FBok(userid) {
    $('#cargando').modal('show');
    $.post('ax/fb.php', {
        id: userid
    }, function (data) {
        $('#cargando').modal('hide');
        if (data.estado === 'ok') {
            $('#columna').load('columna-ok.php');
            if ($('#datos-usuarios').size()) {
                $.address.path('/inicio');
            }
        } else if (data.estado === 'activar') {
            $.address.path('/mi-perfil|'+data.id);
        } else if (data.estado === 'mail') {
            $('#form-login-mensaje').show('clip').html('Facebook no nos deja acceder a tu perfil. No pod&eacute;s iniciar sesi&oacute;n por este medio.');
        } else {
            // fallo login FB
            console.log(data);
        }
    }, 'json');
}

function FBlogin() {
    FB.getLoginStatus(function (response) {
        if (response.status === 'connected') {
            FBok(response.authResponse.userID);
        } else {
            FB.login(function (response) {
                if (response.authResponse && response.status === 'connected') {
                    FBok(response.authResponse.userID);
                }
            }, {
                scope: 'email'
            });
        }
    });
}

var liLoginN = 0;

function LIok(n) {
    if (n > 1) {
        return;
    }
    $('#cargando').modal('show');
    IN.API.Profile("me")
        .fields(["id", "firstName", "lastName", "emailAddress"])
        .result(function (result) {
            $.post('ax/li.php', {
                res: result
            }, function (data) {
                $('#cargando').modal('hide');
                if (data.estado === 'ok') {
                    $('#columna').load('columna-ok.php');
                    if ($('#datos-usuarios').size()) {
                        $.address.path('/inicio');
                    }
                } else if (data.estado === 'activar') {
                    $.address.path('/mi-perfil|'+data.id);
                } else {
                    // fallo login LI
                    console.log(data);
                }
            }, 'json');
        })
        .error(function (e) {
            // fallo login LI
            console.log(e);
            $('#cargando').modal('hide');
        });
}

function LIlogin() {
    if (IN.User.isAuthorized()) {
        LIok();
    } else {
        IN.User.authorize(function () {
            liLoginN++;
            LIok(liLoginN);
        });
    }
    return false;
}

// - address

function cargar(valor, container) {
    if (!container) {
        container = $('#principal');
    }
    var url = $('[rel="address:' + valor + '"]').attr('href');
    if (url) { // si hay a con rel: agrego .php y saco /#
        url = url.substring(2) + '.php';
    } else {
        url = 'inicio.php';
    }
    //
    //console.log(valor, url);
    container.hide('clip');
    container.load(url, function () {
        container.show('clip');
    });
}

function cargar2(url, container) {
    if (!container) {
        container = $('#principal');
    }
    if (!url) {
        url = 'inicio.php';
    }
    container.hide('clip');
    container.load(url, function () {
        container.show('clip');
    });
}
// - vistas

function chBarrios(n) {
    if (n === 0) {
        $('#btn-changuitas-barrios').html('Ninguno elegido');
    } else {
        $('#btn-changuitas-barrios').html('Elegidos: <strong>' + n + '</strong>');
    }
}

function desactivaBuscarCh() {
    $('#btn-buscar-changuitas, #btn-changuitas-todas').addClass('disabled').attr('disabled', 'disabled');
    $('#changuitas-resultados').hide('clip');
    $('.resultados-cargando').show('clip');
}

function activaBuscarCh() {
    $('#btn-buscar-changuitas, #btn-changuitas-todas').removeClass('disabled').removeAttr('disabled');
    $('.resultados-cargando').hide('clip');
    $('#changuitas-resultados').show('clip');
}

function postulaciones() {
    $('#postulaciones-tabla').html('').hide('clip');
    $('.resultados-cargando').show('clip');
    var tipo = 0;
    tipo = $('#postulaciones-filtros button.active').val();
    $.post('ax/postulaciones.php', {
        tipo: tipo
    }, function (data) {
        $('#postulaciones-tabla').html(data.html).show('clip');
        $('.resultados-cargando').hide('clip');
    }, 'json');
}

function misChanguitas() {
    $('#mis-changuitas-tabla').html('').hide('clip');
    $('.resultados-cargando').show('clip');
    var tipo = 0;
    tipo = $('#mis-changuitas-filtros button.active').val();
    $.post('ax/mis-changuitas.php', {
        tipo: tipo
    }, function (data) {
        $('#mis-changuitas-tabla').html(data.html).show('clip');
        $('.resultados-cargando').hide('clip');
    }, 'json');
}

function preguntas() {
    $('#preguntas-tabla').html('').hide('clip');
    $('.resultados-cargando').show('clip');
    var tipo = 0;
    tipo = $('#preguntas-filtros button.active').val();
    $.post('ax/preguntas.php', {
        tipo: tipo
    }, function (data) {
        $('#preguntas-tabla').html(data.html).show('clip');
        $('.resultados-cargando').hide('clip');
    }, 'json');
}

function preguntas2() {
    $('#preguntas2-tabla').html('').hide('clip');
    $('.resultados-cargando').show('clip');
    $.post('ax/preguntas2.php', function (data) {
        $('#preguntas2-tabla').html(data.html).show('clip');
        $('.resultados-cargando').hide('clip');
    }, 'json');
}

function calificaciones() {
    $('#calificaciones-tabla').html('').hide('clip');
    $('.resultados-cargando').show('clip');
    var tipo = 0;
    tipo = $('#calificaciones-filtros button.active').val();
    $.post('ax/calificaciones.php', {
        tipo: tipo
    }, function (data) {
        $('#calificaciones-tabla').html(data.html).show('clip');
        $('.resultados-cargando').hide('clip');
    }, 'json');
}
// - columna

function cerrarNotificaciones() {
    $('.btn-notificaciones').popover('hide');
    $('.btn-notificaciones').removeAttr('disabled').removeClass('disabled');
    // lee todas
    $.post('ax/leido.php', function () {
        actualizarNotificaciones();
    });
}

function actualizarNotificaciones() {
    $.post('ax/notificacionesN.php?' + Math.random(), function (data) {
        $('#notificacionN button:first').html(data.html);
        $('#notificacionN span:first').removeClass('badge-warning').addClass(data.estiloSpan);
    }, 'json');
}

function actualizaColumna() {
    if ($('#columna-ok').size()) {
        $('#columna').load('columna-ok.php');
    }
}
// General
// - button
$('.container').on('click', '.btn-link', function (e) {
    e.preventDefault();
});
// Login
window.fbAsyncInit = function () {
    FB.init({
        appId: '511297335556303', // App ID
        channelUrl: 'includes/fb-channel.php', // Channel File
        status: true, // check login status
        cookie: true, // enable cookies to allow the server to access the session
        xfbml: true // parse XFBML
    });
    // Additional initialization code here
};
// Load the SDK Asynchronously
(function (d) {
    var js, id = 'facebook-jssdk',
        ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement('script');
    js.id = id;
    js.async = true;
    js.src = "//connect.facebook.net/es_LA/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));

$('#columna').on('click', '#login-fb-btn', function () {
    FBlogin();
});
$('#columna').on('click', '#login-li-btn', function () {
    LIlogin();
});
$('#columna').on('submit', '#form-login', function (e) {
    e.preventDefault();
    $('#login-usuario').removeClass('input-error');
    $('#login-clave').removeClass('input-error');
    $('#form-login-mensaje').html('').hide('clip');
    var error = 0;
    var usuario = $.trim($('#login-usuario').val());
    var clave = $.trim($('#login-clave').val());
    if (usuario === '' || !esMail(usuario)) {
        $('#login-usuario').addClass('input-error').val('');
        $('#form-login-mensaje').show('clip').html('Los datos ingresados son incorrectos');
        error++;
    }
    if (clave === '' || !esClave(clave)) {
        $('#login-clave').addClass('input-error').val('');
        $('#form-login-mensaje').show('clip').html('Los datos ingresados son incorrectos');
        error++;
    }
    if (error > 0) {
        return false;
    }
    $('#procesando').modal('show');
    $.post('ax/login.php', $(this).serialize(), function (data) {
        $('#procesando').modal('hide');
        if (data.estado === 'ok') {
            $.address.update();
            $('#columna').load('columna-ok.php');
            if ($('#datos-usuarios').size()) {
                $.address.path('/inicio');
            }
        } else {
            $('#login-usuario').addClass('input-error').val('');
            $('#login-clave').addClass('input-error').val('');
            $('#form-login-mensaje').show('clip').html('Los datos ingresados son incorrectos');
        }
    }, 'json');
    return false;
});
$('#columna').on('focus', '#form-login .input-error', function () {
    $(this).removeClass('input-error');
});
$('#columna').on('click', '#olvido', function () {
    $('#form-login-mensaje').html('Ingres&aacute; tu direcci&oacute;n de e-mail').show('clip');
    $('#olvido-usuario').removeClass('input-error').val('');
    $('#form-login').hide('clip');
    $('#form-olvido').show('clip');
});
$('#columna').on('click', '#iniciar', function () {
    $('#form-login-mensaje').html('').hide('clip');
    $('#login-usuario').removeClass('input-error').val('');
    $('#login-clave').removeClass('input-error').val('');
    $('#form-login').show('clip');
    $('#form-olvido').hide('clip');
});
$('#columna').on('submit', '#form-olvido', function (e) {
    e.preventDefault();
    $('#olvido-usuario').removeClass('input-error');
    $('#form-login-mensaje').html('').hide('clip');
    var error = 0;
    var usuario = $.trim($('#olvido-usuario').val());
    if (usuario === '' || !esMail(usuario)) {
        $('#olvido-usuario').addClass('input-error').val('');
        error++;
    }
    if (error > 0) {
        return false;
    }
    $('#procesando').modal('show');
    $.post('ax/olvido.php', $(this).serialize(), function (data) {
        $('#procesando').modal('hide');
        if (data.estado === 'ok') {
            $('#form-login').show('clip');
            $('#form-olvido').hide('clip');
            $('#form-login-mensaje').html('Te enviamos un e-mail con instrucciones para recuperar la contrase&ntilde;a').show('clip');
        } else {
            $('#olvido-usuario').addClass('input-error').val('');
            $('#form-login-mensaje').show('clip').html('No est&aacute;s registrada/o con ese e-mail');
        }
    }, 'json');
    return false;
});
$('#principal').on('submit', '#contrasena-nueva', function (e) {
    e.preventDefault();
    $('#nueva-clave').removeClass('input-error');
    $('#nueva-clave2').removeClass('input-error');
    $('#contrasena-nueva-mensaje').html('').hide('clip');
    var error = 0;
    var clave = $.trim($('#nueva-clave').val());
    var clave2 = $.trim($('#nueva-clave2').val());
    if (!esClave(clave) || clave === '') {
        $('#nueva-clave').parent().parent().addClass('error');
        $('#nueva-clave').siblings('span').html('Debe tener al menos 8 caracteres, solo letras y/o n&uacute;meros');
        error++;
    }
    if (!esClave(clave2) || clave2 === '') {
        $('#nueva-clave2').parent().parent().addClass('error');
        $('#nueva-clave2').siblings('span').html('Debe tener al menos 8 caracteres, solo letras y/o n&uacute;meros');
        error++;
    }
    if (clave !== '' && clave2 !== '') {
        if (clave !== clave2) {
            $('#nueva-clave').parent().parent().addClass('error');
            $('#nueva-clave2').parent().parent().addClass('error');
            $('#nueva-clave2').siblings('span').html('Ambas contrase&ntilde;as deben ser iguales');
            error++;
        }
    }
    if (error > 0) {
        return false;
    }
    $('#procesando').modal('show');
    $.post('ax/contrasena-nueva.php', $(this).serialize(), function (data) {
        $('#procesando').modal('hide');
        if (data.estado === 'ok') {
            $.address.update();
            $('#columna').load('columna-ok.php');
        } else {
            $('#nueva-clave').addClass('input-error').val('');
            $('#contrasena-nueva-mensaje').show('clip').html('Error al conectarse con la base de datos. Intent&aacute; m&aacute;s tarde o contact&aacute; al administrador del sistema.');
        }
    }, 'json');
    return false;
});
$('#principal').on('focus', '#contrasena-nueva .input-error', function () {
    $(this).removeClass('input-error');
});
$('#principal').on('click', '#btn-contrasena-nueva', function (e) {
    e.preventDefault();
    $('#contrasena-nueva').submit();
});
$('#btn-modal-login').click(function () {
    $('#columna-login').css('border-color', '#FF496F');
    $('#login-usuario').focus();
});
$('#columna-login').click(function () {
    $('#columna-login').css('border-color', '#E3E3E3');
});
$('#columna').on('click', '.btn-registrate', function () {
    $('html,body').animate({
        scrollTop: $('#principal').offset().top
    }, 'slow');
});
// Formularios
// - datepicker
var datepickerOp = { // para fecha de changuita: de manana a +1mes
    changeMonth: true,
    changeYear: true,
    dateFormat: 'dd/mm/yy',
    dayNames: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
    dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
    dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
    monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
    monthNamesShort: ["Ene", "Feb", "Mar", "Abr", "Mayo", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    firstDay: 1,
    maxDate: '+1m',
    minDate: '+0d',
    //yearRange: '+0:+1',
    showOn: "both",
    buttonImage: "img/calendar.gif"
};
$('#principal').on('focus', '.datepicker', function () {
    $(this).datepicker(datepickerOp);
});
$('#principal').on('mouseenter', '.ayuda-datepicker', function () {
    $('.datepicker').datepicker(datepickerOp);
});
// - tooltip
$('#principal, #columna').tooltip({
    html: false,
    placement: 'right',
    selector: '.ayuda'
});
$('#principal, #columna').on('click', '.ayuda', function (e) {
    e.preventDefault();
});
// - validacion
$('#principal').on('focus', '.form-horizontal .error input, .form-horizontal .error select, .form-horizontal .error textarea', function () {
    $(this).parent().parent('.error').removeClass('error');
    $(this).parent().parent().parent('.error').removeClass('error');
    $(this).siblings('span:not(".add-on")').html('');
    $(this).parent().siblings('span:not(".add-on")').html('');
    $(this).addClass('validar');
});
// - editar usuario
$('#principal').on('blur', '#datos-usuarios .validar', function () {
    $(this).removeClass('validar');
    validarUsuario();
});
$('#principal').on('click', '#modificarClave', function (e) {
    e.preventDefault();
    $(this).parent().parent().hide('clip');
    $('.divClave').show('clip');
});
$('#principal').on('change', '#localidad', function () {
    var localidad = $('#localidad option:selected').val();
    if (localidad !== '0') {
        $(this).addClass('disabled');
        $(this).attr('disabled', 'disabled');
        $('#barrio').removeClass('disabled');
        $('#barrio').removeAttr('disabled');
        $('#barrio').addClass('cargandoBarrios');
        $.post('ax/usuario-barrios.php', {
            id: localidad
        }, function (data) {
            if (data.estado === 'ok') {
                $('#barrio').html('');
                $('#barrio').html(data.html);
                $('#localidad').removeClass('disabled');
                $('#localidad').removeAttr('disabled');
            }
            $('#barrio').removeClass('cargandoBarrios');
        }, 'json');
    } else {
        $('#barrio').val('0');
        $('#barrio').addClass('disabled');
        $('#barrio').attr('disabled', 'disabled');
    }
});
/*$('#principal').on('change', '#educacion', function () {
    var educacion = $('#educacion option:selected').val();
    if (educacion !== '0') {
        $('#educacion2').removeClass('disabled');
        $('#educacion2').removeAttr('disabled');
    } else {
        $('#educacion2').val('0');
        $('#educacion2').addClass('disabled');
        $('#educacion2').attr('disabled', 'disabled');
    }
});*/
/*
$('#principal').on('change', '#clave', function() {
    var clave = $.trim($('#clave').val());
    if(clave !== '') {
        $('#clave2').removeClass('disabled');
        $('#clave2').removeAttr('disabled');
    }
    else {
        $('#clave2').val('');
        $('#clave2').addClass('disabled');
        $('#clave2').attr('disabled', 'disabled');
    }
});
*/
$('#principal').on('click', '#categoriasTodas', function (e) {
    e.preventDefault();
    $('#categoriasLista .categoriaCheck').attr('checked', 'checked');
    $('#categoriasLista .categoriaCheck').parent('label').addClass('btn-success');
    $('#categoriasLista .subcategoriaCheck').attr('checked', 'checked');
    $('#categoriasLista .subcats').show();
});
$('#principal').on('click', '#categoriasNinguna', function (e) {
    e.preventDefault();
    $('#categoriasLista .categoriaCheck').removeAttr('checked');
    $('#categoriasLista .categoriaCheck').parent('label').removeClass('btn-success');
    $('#categoriasLista .subcategoriaCheck').removeAttr('checked');
    $('#categoriasLista .subcats').hide();
});
$('#principal').on('click', '#zonasTodas', function (e) {
    e.preventDefault();
    $('#zonasLista .categoriaCheck').attr('checked', 'checked');
    $('#zonasLista .categoriaCheck').parent('label').addClass('btn-success');
    $('#zonasLista .subcategoriaCheck').attr('checked', 'checked');
    $('#zonasLista .subcats').show();
});
$('#principal').on('click', '#zonasNinguna', function (e) {
    e.preventDefault();
    $('#zonasLista .categoriaCheck').removeAttr('checked');
    $('#zonasLista .categoriaCheck').parent('label').removeClass('btn-success');
    $('#zonasLista .subcategoriaCheck').removeAttr('checked');
    $('#zonasLista .subcats').hide();
});
$('#principal').on('click', '.categoriaCheck', function () {
    var sc = $(this).parent().siblings('.subcats');
    if ($(this).is(':checked')) {
        sc.show();
        //$('.subcategoriaCheck', sc).attr('checked', 'checked');
        $(this).parent('label').addClass('btn-success');
    } else {
        sc.hide();
        $('.subcategoriaCheck', sc).removeAttr('checked');
        $(this).parent('label').removeClass('btn-success');
    }
});
$('#principal').on('click', '.subcategoriaCheck', function () {
    if (!$(this).is(':checked')) {
        var sc = $(this).parents('.subcats');
        var c = $(this).parents('.categoria');
        if ($('.subcategoriaCheck:checked', sc).size() === 0) {
            sc.hide();
            $('.categoriaCheck', c).removeAttr('checked');
            $('label:first', c).removeClass('btn-success');
        }
    }
});
$('#principal').on('click', '#datos-usuarios #boton-submit', function (e) {
    e.preventDefault();
    if (validarUsuario()) {
        $('#procesando').modal('show');
        $.post('ax/editar-usuario.php', $('#datos-usuarios').serialize(), function (data) {
            if (data.estado === 'ok') {
                $.address.path('/inicio');
                actualizaColumna();
                if(data.col) {
                    $('#columna').load('columna-ok.php');
                }
            } else if (data.estado === 'existeMail') {
                $('#validar').html('Error: ya existe un usuario con esa direcci&oacute;n de e-mail. Si ya te registraste en estos d&iacute;as, busc&aacute; el e-mail de activaci&oacute;n (fijate tambi&eacute;n en el correo no deseado o spam)');
                $('#mail').parent().parent().addClass('error');
            } else if (data.estado === 'existeDNI') {
                $('#validar').html('Error: ya existe un usuario con ese DNI. Si ya te registraste en estos d&iacute;as, busc&aacute; el e-mail de activaci&oacute;n (fijate tambi&eacute;n en el correo no deseado o spam)');
                $('#dni').parent().parent().addClass('error');
            } else if (data.estado === 'req') {
                $('#validar').html('Error: ten&eacute;s que completar todos los campos obligatorios.');
            } else {
                $('#validar').html('Error al conectarse con la base de datos. Intent&aacute; m&aacute;s tarde o contact&aacute; al administrador del sistema.');
            }
            $('#procesando').modal('hide');
        }, 'json');
    }
    return false;
});
$('#principal').on('click', '#datos-usuarios .btn-abrir-sugerir', function () {
    $('#ini-div-sugerir').show('clip');
});
$('#principal').on('click', '.btn-condiciones', function () {
    $('#ventana').modal('show');
    $('#ventana .modal-header h3').html('');
    $('#ventana .modal-header h4').html('');
    $('#ventana .modal-body').load('condiciones.php');
});
// - editar changuita
$('#principal').on('blur', '#editar-changuita .validar', function () {
    $(this).removeClass('validar');
    validarChanguita();
});
$('#principal').on('change', '#cuando_fecha', function () {
    $(this).removeClass('validar');
    validarChanguita();
    $('#vence').html('');
    if ($(this).val() !== '' && esFecha($(this).val())) {
        $('#vence').html('Por lo tanto, si no eleg&iacute;s un postulante antes, vencer&aacute; el ' + $(this).val() + '.');
    }
});
$('#principal').on('change', '#changuita-localidad', function () {
    var localidad = $('#changuita-localidad option:selected').val();
    if (localidad !== '0') {
        $(this).addClass('disabled');
        $(this).attr('disabled', 'disabled');
        $('#changuita-barrio').removeClass('disabled');
        $('#changuita-barrio').removeAttr('disabled');
        $('#changuita-barrio').addClass('cargandoBarrios');
        $.post('ax/changuita-barrios.php', {
            id: localidad
        }, function (data) {
            if (data.estado === 'ok') {
                $('#changuita-barrio').html('');
                $('#changuita-barrio').html(data.html);
                $('#changuita-localidad').removeClass('disabled');
                $('#changuita-localidad').removeAttr('disabled');
            }
            $('#changuita-barrio').removeClass('cargandoBarrios');
        }, 'json');
    } else {
        $('#changuita-barrio').val('0');
        $('#changuita-barrio').addClass('disabled');
        $('#changuita-barrio').attr('disabled', 'disabled');
    }
});
$('#principal').on('change', '#changuita-categoria', function () {
    var categoria = $('#changuita-categoria option:selected').val();
    $('#ini-div-sugerir').hide('clip');
    $('#ini-sugerir').val('');
    if (categoria > '0') {
        $(this).addClass('disabled');
        $(this).attr('disabled', 'disabled');
        $('#changuita-subcategoria').removeClass('disabled');
        $('#changuita-subcategoria').removeAttr('disabled');
        $('#changuita-subcategoria').addClass('cargandoBarrios');
        $.post('ax/changuita-subcategorias.php', {
            id: categoria
        }, function (data) {
            if (data.estado === 'ok') {
                $('#changuita-subcategoria').html('');
                $('#changuita-subcategoria').html(data.html);
                $('#changuita-categoria').removeClass('disabled');
                $('#changuita-categoria').removeAttr('disabled');
            }
            $('#changuita-subcategoria').removeClass('cargandoBarrios');
        }, 'json');
    } else {
        $('#changuita-subcategoria').addClass('disabled');
        $('#changuita-subcategoria').attr('disabled', 'disabled');
        if (categoria < 0) {
            $('#ini-div-sugerir').show('clip');
        }
    }
    $('#changuita-subcategoria').val('0').change();
});
$('#principal').on('change', '#changuita-subcategoria', function () {
    var subcategoria = $('#changuita-subcategoria option:selected').val();
    $('#precio-sugerido').html('');
    if (subcategoria > '0') {
        $('#precio-sugerido').load('ax/precio-sugerido.php', {
            id: subcategoria
        });
    }
});
$('#principal').on('change', '#changuita-localidad', function () {
    var localidad = $('#changuita-localidad option:selected').val();
    if (localidad === '-1') {
        $('#changuita-barrio').val('0');
        $('#changuita-barrio').parents('.control-group').hide();
    }
    else {
        $('#changuita-barrio').parents('.control-group').show();
    }
});
$('#principal').on('click', '#editar-changuita #boton-submit', function (e) {
    e.preventDefault();
    $(this).attr('disabled', 'disabled').addClass('disabled');
    if (validarChanguitaEdit()) {
        var status = 0;
        $('#procesando').modal('show');
        $.post('ax/editar-changuita.php', $('#editar-changuita').serialize(), function (data) {
            if (data.estado === 'ok') {
                $.address.path('/changuita|' + data.id);
                $('#columna').load('columna-ok.php');
            } else if (data.estado === 'pagar') {
                // PAGO
                $.post('ax/pagar.php', {
                    id: data.id
                }, function (data2) {
                    // pagar arma el json con precio, datos del comprador, token, apiID, etc.
                    if (data2.estado === 'ok') {
                        $MPC.openCheckout({
                            url: data2.preferencia.response.init_point,
                            mode: "modal",
                            onreturn: function (dataMP) {
                                if (dataMP.collection_status === 'approved') {
                                    $.post('ax/pagado.php', {
                                        id: data.id
                                    });
                                    status = 1;
                                } else if (dataMP.collection_status === 'in_process' || dataMP.collection_status === 'pending') {
                                    status = 2;
                                } else {
                                    status = 3;
                                }
                                $.address.path('/changuita|' + data.id + '|' + status);
                                $('#columna').load('columna-ok.php');
                            }
                        });
                    } else {
                        status = 3;
                        $.address.path('/changuita|' + data.id + '|' + status);
                        $('#columna').load('columna-ok.php');
                    }
                    $('#editar-changuita #boton-submit').removeAttr('disabled').removeClass('disabled');
                }, 'json');
                //
            } else if (data.estado === 'precio') {
                $('#precio').parent().parent().parent().addClass('error');
                $('#precio').parent().siblings('span').html('No es un valor v&aacute;lido');
                $('#validar').html('No pod&eacute;s bajar el precio.');
            } else {
                $('#validar').html('Error al conectarse con la base de datos. Intent&aacute; m&aacute;s tarde o contact&aacute; al administrador del sistema.');
            }
            $('#procesando').modal('hide');
        }, 'json');
    } else {
        $('#editar-changuita #boton-submit').removeAttr('disabled').removeClass('disabled');
    }
    return false;
});
$('#principal').on('click', '#editar-changuita #boton-submit-nueva', function (e) {
    e.preventDefault();
    $(this).attr('disabled', 'disabled').addClass('disabled');
    if (validarChanguita()) {
        var status = 0;
        $('#procesando').modal('show');
        $.post('ax/editar-changuita.php', $('#editar-changuita').serialize(), function (data) {
            if (data.estado === 'ok') {
                $.address.path('/changuita|' + data.id);
                $('#columna').load('columna-ok.php');
            } else if (data.estado === 'pagar') {
                // PAGO
                $.post('ax/pagar.php', {
                    id: data.id
                }, function (data2) {
                    // pagar arma el json con precio, datos del comprador, token, apiID, etc.
                    if (data2.estado === 'ok') {
                        $('#procesando').modal('hide');
                        $MPC.openCheckout({
                            url: data2.preferencia.response.init_point,
                            mode: "modal",
                            onreturn: function (dataMP) {
                                if (dataMP.collection_status === 'approved') {
                                    $.post('ax/pagado.php', {
                                        id: data.id
                                    });
                                    status = 1;
                                } else if (dataMP.collection_status === 'in_process' || dataMP.collection_status === 'pending') {
                                    status = 2;
                                } else {
                                    status = 3;
                                }
                                $.address.path('/changuita|' + data.id + '|' + status);
                                $('#columna').load('columna-ok.php');
                            }
                        });
                    } else {
                        status = 3;
                        $.address.path('/changuita|' + data.id + '|' + status);
                        $('#columna').load('columna-ok.php');
                    }
                    $('#boton-submit-nueva').removeAttr('disabled', 'disabled').removeClass('disabled');
                }, 'json');
                //
            } else {
                $('#validar').html('Error al conectarse con la base de datos. Intent&aacute; m&aacute;s tarde o contact&aacute; al administrador del sistema.');
            }
            $('#procesando').modal('hide');
        }, 'json');
    } else {
        $('#boton-submit-nueva').removeAttr('disabled', 'disabled').removeClass('disabled');
    }
    return false;
});
$('#principal').on('click', '.btn-planes', function () {
    $('#ventana').modal('show');
    $('#ventana .modal-header h3').html('Servicios');
    $('#ventana .modal-header h4').html('');
    $('#ventana .modal-body').load('planes.php');
});
$('#principal').on('change', '#plan4', function () {
    if ($(this).is(':checked')) {
        $(this).parent().parent().siblings('.plan-precio').addClass('plan-no');
    } else {
        $(this).parent().parent().siblings('.plan-precio').removeClass('plan-no');
    }
});
$('#principal').on('change', 'input[name="cuando"]', function () {
    if ($('#cuando2').is(':checked')) {
        $('#cuando2').parent().next('div').show();
    } else {
        $('#cuando2').parent().next('div').hide();
        $('input[name="cuando_dias[]"]').removeAttr('checked');
    }
    if ($('#cuando3').is(':checked')) {
        $('#cuando3').parent().next('div').show();
        $('.vence-default').hide();
        $('.vence-fecha').show();
        $('#vence').html('');
    } else {
        $('#cuando3').parent().next('div').hide();
        $('#cuando_fecha').val('');
        $('.vence-default').show();
        $('.vence-fecha').hide();
    }
});
$('#principal').on('click', '.btn-cuando-lav', function () {
    $('input[name="cuando_dias[]"]').removeAttr('checked');
    $('#cuando_dias1').attr('checked', 'checked');
    $('#cuando_dias2').attr('checked', 'checked');
    $('#cuando_dias3').attr('checked', 'checked');
    $('#cuando_dias4').attr('checked', 'checked');
    $('#cuando_dias5').attr('checked', 'checked');
});
// Home
// - buscar
$('#principal').on('click', '#drop-categoria a', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-cat-id');
    var cat = $(this).html();
    if (id < 0) {
        $('#ini-div-sugerir').show('clip');
    } else {
        $('input[name="categoria"]').val(id);
        $('#btn-drop-categoria .txt').html(cat);
        $('#btn-drop-categoria p').addClass('bold');
        $('#btn-drop-categoria').addClass('disabled').attr('disabled', 'disabled');
        $('#btn-drop-categoria').parent().addClass('cargandoBarrios');
        $('input[name="subcategoria"]').val(0);
        $('#btn-drop-subcategoria .txt').html('Eleg&iacute; una Subcategor&iacute;a');
        $('#btn-drop-subcategoria p').removeClass('bold');
        $.post('ax/inicio-subcategorias.php', {
            id: id
        }, function (data) {
            if (data.estado === 'ok') {
                $('#drop-subcategoria').html(data.html);
                $('#btn-drop-subcategoria').removeClass('disabled').removeAttr('disabled');
            }
            $('#btn-drop-categoria').parent().removeClass('cargandoBarrios');
            $('#btn-drop-categoria').removeClass('disabled').removeAttr('disabled');
        }, 'json');
    }
});
$('#principal').on('click', '#drop-subcategoria a', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-subcat-id');
    var subcat = $(this).html();
    $('input[name="subcategoria"]').val(id);
    $('#btn-drop-subcategoria .txt').html(subcat);
    $('#btn-drop-subcategoria p').addClass('bold');
});
$('#principal').on('click', '#drop-localidad a', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-loc-id');
    var loc = $(this).html();
    if (id > 0) {
        $('input[name="localidad"]').val(id);
        $('#btn-drop-localidad .txt').html(loc);
        $('#btn-drop-localidad p').addClass('bold');
        $('#btn-drop-localidad').addClass('disabled').attr('disabled', 'disabled');
        $('#btn-drop-localidad').parent().addClass('cargandoBarrios');
        $('input[name="barrio"]').val(0);
        $('#btn-drop-barrio .txt').html('Eleg&iacute; localidades o barrios');
        $('#btn-drop-barrio p').removeClass('bold');
        $.post('ax/inicio-barrios.php', {
            id: id
        }, function (data) {
            if (data.estado === 'ok') {
                $('#drop-barrio').html(data.html);
                $('#btn-drop-barrio').removeClass('disabled').removeAttr('disabled');
            }
            $('#btn-drop-localidad').parent().removeClass('cargandoBarrios');
            $('#btn-drop-localidad').removeClass('disabled').removeAttr('disabled');
        }, 'json');
    }
});
$('#principal').on('click', '#drop-barrio input, #drop-barrio label', function (e) {
    e.stopPropagation();
    var nBarrios = $('#drop-barrio input:checked').size();
    if (nBarrios === 0) {
        $('#btn-drop-barrio .txt').html('Eleg&iacute; localidades o barrios');
        $('#btn-drop-barrio p').removeClass('bold');
    } else {
        $('#btn-drop-barrio .txt').html('Localidades o barrios elegidos: ' + nBarrios);
        $('#btn-drop-barrio p').addClass('bold');
    }
});
$('#principal').on('click', '#drop-barrios-ninguno', function (e) {
    e.preventDefault();
    $('#drop-barrio input').removeAttr('checked', 'checked');
    $('#btn-drop-barrio .txt').html('Eleg&iacute; localidades o barrios');
    $('#btn-drop-barrio p').removeClass('bold');
});
$('#principal').on('click', '#drop-barrios-todos', function (e) {
    e.preventDefault();
    $('#drop-barrio input').attr('checked', 'checked');
    var nBarrios = $('#drop-barrio input:checked').size();
    $('#btn-drop-barrio .txt').html('Localidades o barrios elegidos: ' + nBarrios);
    $('#btn-drop-barrio p').addClass('bold');
});
/*
$('#principal').on('change', '#ini-categoria', function() {
    var categoria = $('#ini-categoria option:selected').val();
    $('#ini-div-sugerir').hide('clip');
    $('#ini-sugerir').val('');
    if(categoria > '0') {
        $('#ini-categoria').addClass('disabled');
        $('#ini-categoria').attr('disabled', 'disabled');
        $('#ini-categoria').addClass('cargandoBarrios');
        $.post('ax/inicio-subcategorias.php', {id: categoria}, function(data) {
            if(data.estado === 'ok') {
                $('#ini-subcategoria').html(data.html);
                $('#ini-categoria').removeClass('disabled');
                $('#ini-categoria').removeAttr('disabled');
                $('#ini-subcategoria').removeClass('disabled');
                $('#ini-subcategoria').removeAttr('disabled');
            }
            $('#ini-categoria').removeClass('cargandoBarrios');
        }, 'json');
    }
    else {
        $('#ini-subcategoria').val(0);
        $('#ini-subcategoria').addClass('disabled');
        $('#ini-subcategoria').attr('disabled', 'disabled');
        if(categoria < 0)
            $('#ini-div-sugerir').show('clip');
    }
});
*/
$('#principal').on('click', '#btn-sugerir', function (e) {
    e.preventDefault();
    var sugerencia = $.trim($('#ini-sugerir').val());
    sugerencia = sugerencia.replace(/[^a-zA-Z0-9\s\u00e1\u00e9\u00ed\u00f3\u00fa\u00f1\00fc\00e7]/gi, ' ');
    sugerencia = sugerencia.replace(/[\s]+/g, ' ');
    $('#ini-sugerir').val(sugerencia);
    if (sugerencia !== '') {
        $.post('ax/logged.php', function (data) {
            if (data.estado === 'ok') {
                $('#ventana').modal('show');
                $('#ventana .modal-header h3').html('Gracias');
                $('#ventana .modal-header h4').html('');
                $.post('ax/sugerir.php', {
                    s: sugerencia
                }, function () {
                    $('#ventana .modal-body').html('Nuestro equipo evaluar&aacute; tu sugerencia.');
                });
                $('#ini-div-sugerir').hide('clip');
                $('#ini-sugerir').val('');
                $('#ini-categoria option:first').attr('selected', 'selected');
            } else {
                $('#aviso-login').modal('show');
            }
        }, 'json');
    }
});
/*$('#principal').on('change', '#ini-localidad', function() {
    var localidad = $('#ini-localidad option:selected').val();
    if(localidad !== '0') {
        $('#ini-localidad').addClass('disabled');
        $('#ini-localidad').attr('disabled', 'disabled');
        $('#ini-localidad').addClass('cargandoBarrios');
        $.post('ax/inicio-barrios.php', {id: localidad}, function(data) {
            if(data.estado === 'ok') {
                $('#ini-barrios').html(data.html);
                $('#ini-localidad').removeClass('disabled');
                $('#ini-localidad').removeAttr('disabled');
            }
            $('#ini-localidad').removeClass('cargandoBarrios');
        }, 'json');
    }
    else
        $('#ini-barrios').html('');
});*/
$('#principal').on('click', '#ini-barrios-todos', function () {
    $('#ini-barrios input').attr('checked', 'checked');
});
$('#principal').on('click', '#ini-barrios-ninguno', function () {
    $('#ini-barrios input').removeAttr('checked', 'checked');
});
$('#principal').on('click', '#btn-buscar', function (e) {
    e.preventDefault();
    if ($('#ini-categoria option:selected').val() === 0 && $('#ini-localidad option:selected').val() === 0 && $.trim($('#ini-palabras').val()) === '') {
        return false;
    }
    var buscar = $('#ini-palabras').val();
    buscar = buscar.replace(/[^a-zA-Z0-9\u00e1\u00e9\u00ed\u00f3\u00fa\u00f1\00fc\00e7]/gi, ' ');
    buscar = buscar.replace(/[\s]+/g, ' ');
    $('#ini-palabras').val(buscar);
    $.post('ax/buscar.php', $('#ini-buscar').serialize(), function (data) {
        $.address.path('/changuitas');
    });
});
$('#principal').on('click', '#btn-ver-todas', function (e) {
    e.preventDefault();
    $.post('ax/buscar.php', function (data) {
        $.address.path('/changuitas');
    });
});
// - publicar
$('.container').on('click', '.btn-publicar', function (e) {
    e.preventDefault();
    //$('#procesando').modal('show');
    $.post('ax/logged.php', {
        bloqueado: 1
    }, function (data) {
        //$('#procesando').modal('hide');
        if (data.estado === 'ok') {
            $.address.path('/changuita-nueva');
        } else if (data.estado === 'bloqueado') {
            $('#aviso-bloqueado').modal('show');
        } else {
            $('#aviso-login').modal('show');
        }
    }, 'json');
});
// Vista changuitas
$('#principal').on('change', '#changuitas-categoria', function () {
    var categoria = $('#changuitas-categoria option:selected').val();
    $('#ini-div-sugerir').hide('clip');
    $('#ini-sugerir').val('');
    if (categoria > '0') {
        $('#changuitas-categoria').addClass('disabled');
        $('#changuitas-categoria').attr('disabled', 'disabled');
        $('#changuitas-categoria').addClass('cargandoBarrios');
        $.post('ax/changuitas-subcategorias.php', {
            id: categoria
        }, function (data) {
            if (data.estado === 'ok') {
                $('#changuitas-subcategoria').html(data.html);
                $('#changuitas-categoria').removeClass('disabled');
                $('#changuitas-categoria').removeAttr('disabled');
                $('#changuitas-subcategoria').removeClass('disabled');
                $('#changuitas-subcategoria').removeAttr('disabled');
            }
            $('#changuitas-categoria').removeClass('cargandoBarrios');
        }, 'json');
    } else {
        $('#changuitas-subcategoria').val(0);
        $('#changuitas-subcategoria').addClass('disabled');
        $('#changuitas-subcategoria').attr('disabled', 'disabled');
        if (categoria < 0) {
            $('#ini-div-sugerir').show('clip');
        }
    }
});
$('#principal').on('change', '#changuitas-localidad', function () {
    var localidad = $('#changuitas-localidad option:selected').val();
    if (localidad !== '0') {
        $('#changuitas-localidad').addClass('disabled');
        $('#changuitas-localidad').attr('disabled', 'disabled');
        $('#changuitas-localidad').addClass('cargandoBarrios');
        $.post('ax/changuitas-barrios.php', {
            id: localidad
        }, function (data) {
            if (data.estado === 'ok') {
                $('#changuitas-barrio').html(data.html);
                $('#changuitas-localidad').removeClass('disabled');
                $('#changuitas-localidad').removeAttr('disabled');
                $('#btn-changuitas-barrios').removeClass('disabled');
                $('#btn-changuitas-barrios').removeAttr('disabled');
                $('#btn-changuitas-barrios').html('Ninguno elegido');
            }
            $('#changuitas-localidad').removeClass('cargandoBarrios');
        }, 'json');
    } else {
        $('#changuitas-barrio').html('');
        $('#btn-changuitas-barrios').addClass('disabled');
        $('#btn-changuitas-barrios').attr('disabled', 'disabled');
        $('#btn-changuitas-barrios').html('Ninguno elegido');
    }
});
$('#principal').on('click', '#btn-buscar-changuitas', function (e) {
    e.preventDefault();
    desactivaBuscarCh();
    var buscar = $('#changuitas-palabras').val();
    buscar = buscar.replace(/[^a-zA-Z0-9\u00e1\u00e9\u00ed\u00f3\u00fa\u00f1\00fc\00e7]/gi, ' ');
    buscar = buscar.replace(/[\s]+/g, ' ');
    $('#changuitas-palabras').val(buscar);
    $.post('ax/buscar.php', $('#changuitas-buscar').serialize(), function () {
        $.post('ax/changuitas.php', $('#changuitas-buscar').serialize(), function (data) {
            activaBuscarCh();
            $('#changuitas-resultados').html(data.html);
        }, 'json');
    });
});
$('#principal').on('click', '#btn-changuitas-todas', function (e) {
    e.preventDefault();
    $('#changuitas-buscar input#changuitas-palabras').val('');
    $('#changuitas-buscar select').val(0);
    $('#changuitas-buscar #changuitas-barrio input').removeAttr('checked');
    chBarrios(0);
    desactivaBuscarCh();
    $.post('ax/buscar.php');
    $.post('ax/changuitas.php', function (data) {
        activaBuscarCh();
        $('#changuitas-resultados').html(data.html);
    }, 'json');
});
$('#principal').on('click', '#btn-changuitas-barrios', function (e) {
    e.preventDefault();
});
$('#principal').on('click', '#changuitas-barrio input, #changuitas-barrio label', function (e) {
    e.stopPropagation();
    var nBarrios = $('#changuitas-barrio input:checked').size();
    chBarrios(nBarrios);
});
$('#principal').on('click', '#btn-changuitas-barrios-ninguno', function (e) {
    e.preventDefault();
    $('#changuitas-barrio input').removeAttr('checked', 'checked');
    $('#btn-changuitas-barrios').html('Ninguno elegido');
});
$('#principal').on('click', '#btn-changuitas-barrios-todos', function (e) {
    e.preventDefault();
    $('#changuitas-barrio input').attr('checked', 'checked');
    var nBarrios = $('#changuitas-barrio input:checked').size();
    if (nBarrios === 0) {
        $('#btn-changuitas-barrios').html('Ninguno elegido');
    } else {
        $('#btn-changuitas-barrios').html('Elegidos: <strong>' + nBarrios + '</strong>');
    }
});
$('#principal').on('click', '.btn-postular:not(".disabled")', function (e) {
    e.preventDefault();
    var btn = $(this);
    $.post('ax/logged.php', {
        bloqueado: 1
    }, function (data) {
        if (data.estado === 'ok') {
            var chId = btn.attr('data-changuita');
            btn.attr('disabled', 'disabled');
            btn.children('.cargando').show();
            $.post('ax/postular.php', {
                id: chId
            }, function (data2) {
                btn.removeAttr('disabled');
                btn.children('.cargando').hide();
                if (data2.estado === 'ok') {
                    $.address.update();
                }
            }, 'json');
        } else if (data.estado === 'bloqueado') {
            $('#aviso-bloqueado').modal('show');
        } else {
            $('#aviso-login').modal('show');
        }
    }, 'json');
});
// Paginacion buscar
$('#principal').on('click', '#pag-pri', function () {
    if ($(this).hasClass('disabled')) {
        return false;
    }
    $('#ini option:first').attr('selected', 'selected');
    $('#ini').change();
});
$('#principal').on('click', '#pag-ant:not(".disabled")', function () {
    $('#ini option:selected').prev().attr('selected', 'selected');
    $('#ini').change();
});
$('#principal').on('click', '#pag-sig', function () {
    if ($(this).hasClass('disabled')) {
        return false;
    }
    $('#ini option:selected').next().attr('selected', 'selected');
    $('#ini').change();
});
$('#principal').on('click', '#pag-ult', function () {
    if ($(this).hasClass('disabled')) {
        return false;
    }
    $('#ini option:last').attr('selected', 'selected');
    $('#ini').change();
});
$('#principal').on('change', '#ini', function () {
    var ini = $('#ini').val();
    var orden = $('#orden').val();
    $('#btn-buscar-changuitas, #btn-changuitas-todas').addClass('disabled').attr('disabled', 'disabled');
    $('#changuitas-resultados').hide('clip');
    $('#changuitas-resultados-cargando').show('clip');
    $.post('ax/changuitas.php', $('#changuitas-buscar').serialize() + '&ini=' + ini + '&orden=' + orden, function (data) {
        $('#btn-buscar-changuitas, #btn-changuitas-todas').removeClass('disabled').removeAttr('disabled');
        $('#changuitas-resultados-cargando').hide('clip');
        $('#changuitas-resultados').show('clip');
        $('#changuitas-resultados').html(data.html);
    }, 'json');
});
// vista changuita
$('#principal').on('click', '.btn-hacer-pregunta', function () {
    $('html:not(:animated), body:not(:animated)').animate({
        scrollTop: $('#pregunta').offset().top
    }, 750);
    $('#pregunta').focus();
});
$('#principal').on('click', '.btn-preguntar:not(".disabled")', function (e) {
    e.preventDefault();
    var btn = $(this);
    $.post('ax/logged.php', function (data) {
        if (data.estado === 'ok') {
            if ($.trim($('#pregunta').val()) === '') {
                return;
            }
            var chId = btn.attr('data-changuita');
            btn.attr('disabled', 'disabled');
            $('#pregunta').attr('disabled', 'disabled');
            btn.children('.cargando').show();
            $.post('ax/preguntar.php', {
                id: chId,
                pregunta: $.trim($('#pregunta').val())
            }, function (data2) {
                btn.removeAttr('disabled');
                $('#pregunta').removeAttr('disabled');
                btn.children('.cargando').hide();
                if (data2.estado === 'ok') {
                    $.address.update();
                }
            }, 'json');
        } else if (data.estado === 'bloqueado') {
            $('#aviso-bloqueado').modal('show');
        } else {
            $('#aviso-login').modal('show');
        }
    }, 'json');
});
$('#principal').on('click', '.btn-responder:not(".disabled")', function (e) {
    e.preventDefault();
    var respuesta = $.trim($(this).prev('.respuesta').val());
    if (respuesta === '') {
        return;
    }
    var id = $(this).attr('data-pregunta');
    $(this).attr('disabled', 'disabled');
    $('.respuesta').attr('disabled', 'disabled');
    $(this).children('.cargando').show();
    var btn = $(this);
    $.post('ax/responder.php', {
        id: id,
        respuesta: respuesta
    }, function (data) {
        btn.removeAttr('disabled');
        $('.respuesta').removeAttr('disabled');
        btn.children('.cargando').hide();
        if (data.estado === 'ok') {
            $.address.update();
            actualizarNotificaciones();
        }
    }, 'json');
});
var denunciaId;
var denunciaTipo;
$('#principal').on('click', '.btn-denunciar-changuita', function (e) {
    e.preventDefault();
    var btn = $(this);
    $.post('ax/logged.php', {
        bloqueado: 1
    }, function (data) {
        if (data.estado === 'ok') {
            denunciaId = btn.attr('data-changuita-id');
            denunciaTipo = 'ch';
            $('#denunciar').modal('show');
            $('#denuncia').val('');
        } else if (data.estado === 'bloqueado') {
            $('#aviso-bloqueado').modal('show');
        } else {
            $('#aviso-login').modal('show');
        }
    }, 'json');
});
$('#principal').on('click', '.btn-denunciar-pregunta', function (e) {
    e.preventDefault();
    var btn = $(this);
    $.post('ax/logged.php', {
        bloqueado: 1
    }, function (data) {
        if (data.estado === 'ok') {
            denunciaId = btn.attr('data-pregunta-id');
            denunciaTipo = 'p';
            $('#denunciar').modal('show');
            $('#denuncia').val('');
        } else if (data.estado === 'bloqueado') {
            $('#aviso-bloqueado').modal('show');
        } else {
            $('#aviso-login').modal('show');
        }
    }, 'json');
});
$('#principal').on('click', '.btn-denunciar-respuesta', function (e) {
    e.preventDefault();
    var btn = $(this);
    $.post('ax/logged.php', {
        bloqueado: 1
    }, function (data) {
        if (data.estado === 'ok') {
            denunciaId = btn.attr('data-respuesta-id');
            denunciaTipo = 'r';
            $('#denunciar').modal('show');
            $('#denuncia').val('');
        } else if (data.estado === 'bloqueado') {
            $('#aviso-bloqueado').modal('show');
        } else {
            $('#aviso-login').modal('show');
        }
    }, 'json');
});
$('#denunciar').on('click', '.btn-denunciar-ok', function (e) {
    e.preventDefault();
    $.post('ax/denunciar.php', {
        id: denunciaId,
        tipo: denunciaTipo,
        comentario: $('#denuncia').val()
    });
});
$('#principal').on('click', '.btn-iniciar-sesion', function () {
    $('html:not(:animated), body:not(:animated)').animate({
        scrollTop: 0
    }, 750);
    $('#login-usuario').focus();
});
$('#principal, #elegir').on('click', '.btn-detalle-calificaciones', function (e) {
    e.preventDefault();
    var usuarioId = $(this).attr('data-usuario-id');
    $('#ventana').modal('show');
    $('#ventana .modal-header h3').html('Calificaciones');
    $.post('ax/ver-calificaciones.php', {
        id: usuarioId
    }, function (data) {
        $('#ventana .modal-header h4').html(data.nombre);
        $('#ventana .modal-body').html(data.html);
    }, 'json');
});
var calificarId;
$('#principal').on('click', '.btn-calificar', function (e) {
    e.preventDefault();
    calificarId = $(this).attr('data-changuita-id');
    $('.btn-calificar-realizo').removeAttr('checked');
    $('.btn-group button').removeClass('disabled active');
    $('#calificar-comentario').val('');
    $('#calificar').modal('show');
    $('.modal-body button').removeClass('active');
});
$('#calificar').on('click', '.modal-body .btn-calificar-realizo', function () {
    $('.btn-group button').removeClass('disabled active');
    if ($(this).val() === '0') {
        $('.btn-calificar-positivo').addClass('disabled');
    }
});
$('#calificar').on('click', '.modal-body button', function (e) {
    e.preventDefault();
    $('.modal-body button').removeClass('active');
    $(this).addClass('active');
});
$('#calificar').on('click', '.btn-calificar-ok', function (e) {
    e.preventDefault();
    var realizo = $('.btn-calificar-realizo:checked').val();
    var valor = $('#calificar .modal-body button.active').val();
    var comentario = $.trim($('#calificar-comentario').val());
    if (!realizo || !valor) {
        return;
    }
    $('#calificar').modal('hide');
    $('.btn-calificar').attr('disabled', 'disabled');
    $('.btn-calificar').children('.cargando').show();
    $.post('ax/calificar.php', {
        id: calificarId,
        valor: valor,
        realizo: realizo,
        comentario: comentario
    }, function (data) {
        $('.btn-calificar').removeAttr('disabled');
        $('.btn-calificar').children('.cargando').hide();
        if (data.estado === 'ok') {
            $.address.update();
            $('#columna').load('columna-ok.php');
        }
    }, 'json');
});
// - orden
$('#principal').on('change', '#orden', function () {
    var orden = $('#orden').val();
    $('#btn-buscar-changuitas, #btn-changuitas-todas').addClass('disabled').attr('disabled', 'disabled');
    $('#changuitas-resultados').hide('clip');
    $('#changuitas-resultados-cargando').show('clip');
    $.post('ax/changuitas.php', $('#changuitas-buscar').serialize() + '&ini=0&orden=' + orden, function (data) {
        $('#btn-buscar-changuitas, #btn-changuitas-todas').removeClass('disabled').removeAttr('disabled');
        $('#changuitas-resultados-cargando').hide('clip');
        $('#changuitas-resultados').show('clip');
        $('#changuitas-resultados').html(data.html);
    }, 'json');
});
// postulaciones / mis-changuitas / preguntas / calificaciones
$('.container').on('click', '#postulaciones-filtros button', function (e) {
    e.preventDefault();
    $('#postulaciones-filtros button').removeClass('active');
    $(this).addClass('active');
    postulaciones();
});
$('.container').on('click', '#mis-changuitas-filtros button', function (e) {
    e.preventDefault();
    $('#mis-changuitas-filtros button').removeClass('active');
    $(this).addClass('active');
    misChanguitas();
});
$('.container').on('click', '#preguntas-filtros button', function (e) {
    e.preventDefault();
    $('#preguntas-filtros button').removeClass('active');
    $(this).addClass('active');
    preguntas();
});
$('.container').on('click', '#calificaciones-filtros button', function (e) {
    e.preventDefault();
    $('#calificaciones-filtros button').removeClass('active');
    $(this).addClass('active');
    calificaciones();
});
$('.container').on('click', '.btn-vista-ver', function (e) {
    e.preventDefault();
    var idCh = $(this).attr('data-changuita-id');
    $.address.path('changuita|' + idCh);
});
var accion, accionId, accionBtn;
$('.container').on('click', '.btn-finalizar', function (e) {
    e.preventDefault();
    accionBtn = $(this);
    accionId = $(this).attr('data-changuita-id');
    accion = 'finalizar';
    $('#confirmar').modal('show');
});
$('.container').on('click', '.btn-anular-postulacion', function (e) {
    e.preventDefault();
    accionBtn = $(this);
    accionId = $(this).attr('data-changuita-id');
    accion = 'despostular';
    $('#confirmar').modal('show');
});
$('.container').on('click', '.btn-borrar-ch', function (e) {
    e.preventDefault();
    accionBtn = $(this);
    accionId = $(this).attr('data-changuita-id');
    accion = 'borrar';
    $('#confirmar').modal('show');
});
$('#confirmar').on('click', '.btn-confirmar-ok', function (e) {
    e.preventDefault();
    accionBtn.attr('disabled', 'disabled');
    accionBtn.children('.cargando').show();
    $.post('ax/' + accion + '.php', {
        id: accionId
    }, function () {
        $('#columna').load('columna-ok.php');
        accionBtn.removeAttr('disabled');
        accionBtn.children('.cargando').hide();
        $.address.update();
    });
});
$('.container').on('click', '.btn-elegir', function (e) {
    e.preventDefault();
    var idCh = $(this).attr('data-changuita-id');
    $(this).attr('disabled', 'disabled');
    $(this).children('.cargando').show();
    var btn = $(this);
    $.post('ax/elegir.php', {
        id: idCh
    }, function (data) {
        btn.removeAttr('disabled');
        btn.children('.cargando').hide();
        $('#elegir .modal-body').html(data.html);
        $('#elegir').modal('show');
    }, 'json');
});
$('#elegir').on('click', '.btn-contratar', function (e) {
    e.preventDefault();
    var idU = $(this).attr('data-usuario-id');
    var idCh = $(this).attr('data-changuita-id');
    $.post('ax/contratar.php', {
        u: idU,
        ch: idCh
    }, function (data) {
        if (data.estado === 'ok') {
            $.address.update();
            actualizarNotificaciones();
        }
    }, 'json');
});
// contacto
$('#principal').on('click', '#contacto #boton-contacto', function (e) {
    e.preventDefault();
    if (validarContacto()) {
        $('#procesando').modal('show');
        $.post('ax/contacto.php', $('#contacto').serialize(), function (data) {
            if (data.estado === 'ok') {
                $('#contacto-ok').show();
            } else {
                $('#validar').html('Error al enviar el mensaje. Intent&aacute; m&aacute;s tarde o contact&aacute; al administrador del sistema.');
            }
            $('#procesando').modal('hide');
        }, 'json');
    }
    return false;
});
// Address
$.address.change(function (event) {
    var pos,
        address = event.value,
        get = '',
        splitCh = '|';

    pos = address.indexOf(splitCh);
    if (pos === -1) {
        splitCh = '%7C';
        pos = address.indexOf(splitCh);
    }
    if (pos > 0) {
        var addSplit = address.split(splitCh);
        address = addSplit[0];
        if (addSplit[1]) {
            get = '?id=' + addSplit[1];
        }
        if (addSplit[2]) {
            get += '&s=' + addSplit[2];
        }
    }
    switch (address) {
    case '/editar-changuita':
        cargar2('editar-changuita.php' + get);
        break;
    case '/changuita-nueva':
        cargar2('editar-changuita.php');
        break;
    case '/changuita':
        cargar2('ver-changuita.php' + get);
        break;
    case '/mi-perfil':
        cargar2('editar-usuario.php' + get);
        break;
    case '/invitar2':
        cargar2('invitar2.php');
        break;
    case '/invitar-ok':
        cargar2('invitar-ok.php');
        break;
    case '/changuitas':
        cargar2('changuitas.php');
        break;
    case '/notificaciones':
        cargar2('notificaciones.php');
        break;
    case '/republicar':
        cargar2('republicar.php' + get);
        break;
    default:
        cargar(address);
    }
});
// destacadas
$('#principal').on('click', '.ver-mas', function () {
    var id = $(this).attr('data-changuita-id');
    var plan = $(this).attr('data-changuita-plan');
    $.post('ax/click.php', {
        id: id,
        plan: plan
    });
});
// columna
$('#columna').on('click', '.btn-notificaciones:not(".disabled")', function () {
    $(this).attr('disabled', 'disabled').addClass('disabled');
    $('.btn-notificaciones').popover({
        title: '<button class="btn-link btn-notificaciones-cerrar">Cerrar</button>',
        content: '<img src="img/cargando.gif" alt="cargando" />',
        html: true,
        placement: 'bottom'
    });
    $.post('ax/notificaciones.php', function (data) {
        if (data.estado === 'ok') {
            $('.btn-notificaciones').popover('show');
            $('#columna-ok .popover-content').html(data.html);
        } else {
            $('.btn-notificaciones').popover('hide');
        }
    }, 'json');
});
$('#columna').on('click', '.btn-notificaciones-cerrar', function () {
    cerrarNotificaciones();
});
$('#columna').on('click', '.btn-notificaciones-todas', function () {
    cerrarNotificaciones();
    $.address.path('/notificaciones');
});
/*$('#columna').on('click', '.btn-notificacion-ver', function () {
    var idCh = $(this).attr('data-changuita-id');
    var idN = $(this).attr('data-notificacion-id');
    cerrarNotificaciones();
    if (!$(this).hasClass('no-leer')) {
        $.post('ax/leido.php', {
            id: idN
        });
    }
    actualizarNotificaciones();
    $.address.path('/changuita|' + idCh);
});*/
/*$('#columna').on('click', '.btn-notificacion-leer', function () {
    $(this).parent('.notif').addClass('leido').hide('clip');
    if ($('.btn-notificacion-leer').parents('.notif:not(".leido")').size() === 0) {
        cerrarNotificaciones();
    }
    var id = $(this).attr('data-notificacion-id');
    $.post('ax/leido.php', {
        id: id
    }, function () {
        actualizarNotificaciones();
    });
});*/
// pagar deuda
$('.container').on('click', '.btn-pagar-deuda', function (e) {
    e.preventDefault();
    var ch = [],
        fee = [],
        respuesta,
        clase;
    $('input[name="pagar-ch[]"]').each(function () {
        ch.push($(this).val());
    });
    $('input[name="pagar-fee[]"]').each(function () {
        fee.push($(this).val());
    });
    $(this).attr('disabled', 'disabled').addClass('disabled');
    $('#procesando').modal('show');
    // PAGO
    $.post('ax/pagar-deuda.php', {
        id: ch,
        fee: fee
    }, function (data) {
        $('#procesando').modal('hide');
        if (data.estado === 'ok') {
            $MPC.openCheckout({
                url: data.preferencia.response.init_point,
                mode: "modal",
                onreturn: function (dataMP) {
                    if (dataMP.collection_status === 'approved') {
                        $.post('ax/pagado.php', {
                            id: ch,
                            fee: fee
                        });
                        respuesta = 'El pago fue aprobado.';
                        clase = 'success';
                        $('#pagar-deuda-container').hide();
                        $('#columna').load('columna-ok.php');
                    } else if (dataMP.collection_status === 'in_process' || dataMP.collection_status === 'pending') {
                        respuesta = 'El pago qued&oacute; pendiente. Cuando se acredite te avisaremos.';
                        clase = 'info';
                        $('#pagar-deuda-container').hide();
                    } else {
                        respuesta = 'No realizaste el pago. Por favor, sald&aacute; la deuda apenas puedas, as&iacute; evit&aacute;s que se bloque&eacute; tu usuario.';
                        clase = 'error';
                    }
                    $('.pagar-deuda-respuesta').html(respuesta).addClass('alert-' + clase).show();
                    $('.btn-pagar-deuda').removeAttr('disabled').removeClass('disabled');
                }
            });
        } else {
            respuesta = 'Error al conectarse con la base de datos. Intent&aacute; m&aacute;s tarde o contact&aacute; al administrador del sistema.';
            clase = 'error';
            $('.pagar-deuda-respuesta').html(respuesta).addClass('alert-' + clase).show();
            $('.btn-pagar-deuda').removeAttr('disabled').removeClass('disabled');
        }
    }, 'json');
    return false;
});
// invitar
// - beta
$('.container').on('click', '.btn-invitar-fbx, .btn-invitar-lix, .btn-invitar-gmx, .btn-invitar-hmx', function (e) {
    e.preventDefault();
    $('.ul-invitar li').removeClass('invitar-li-ok');
    $('p.invitar-li-ok').removeClass('invitar-li-ok');
    $(this).parent().addClass('invitar-li-ok');
    $('.btn-invitar-submit').attr('disabled', 'disabled').addClass('disabled');
    $('#invitar-res').show();
    $('#form-invitar').html('No disponible en este momento');
});
// ********

$('.container').on('click', '.btn-invitar-gm', function (e) {
    e.preventDefault();
    $('#invitar-res').hide();
    handleClientLoad();
    $('.ul-invitar li').removeClass('invitar-li-ok');
    $('p.invitar-li-ok').removeClass('invitar-li-ok');
    $(this).parent().addClass('invitar-li-ok');
});
// Google API
function handleClientLoad() {
    if(!gapi.client) {
        return;
    }
    var apiKey = 'AIzaSyD-jUuG1xVWmNTDInPB-2d1wAB8VaKnTOw';
    gapi.client.setApiKey(apiKey);
    window.setTimeout(checkAuth,1);
}

function checkAuth() {
    var scope = 'https://www.google.com/m8/feeds',
        clientId = '90058208866';
    gapi.auth.authorize({client_id: clientId, scope: scope, immediate: true}, handleAuthResult);
}

function handleAuthResult(authResult) {
    if (authResult && !authResult.error) {
        makeApiCall(authResult);
    } else {
        console.log(authResult.error);
    }
}

function makeApiCall(authResult) {
    var authParams = authResult;
    $.ajax({
        url: 'https://www.google.com/m8/feeds/contacts/default/full?max-results=9999&alt=json',
        dataType: 'jsonp',
        type: 'GET',
        data: authParams,
        success: function(data) {
            $('#procesando').modal('show');
            $.post('ax/invitar-gm.php', {data: data}, function(data2) {
                if(data2.estado === 'ok') {
                    $('#procesando').modal('hide');
                    $('#form-invitar').html(data2.html);
                    $('#invitar-res').show();
                } else {
                  $('#form-invitar-mensaje').show('clip').html('No hay contactos para importar');
                }
            }, 'json');
        }
    });
}
// ***********


// function getFriendsFB() {
//     FB.api('/me/friends', function (response) {
//         $('#procesando').modal('show');
//         $.post('ax/invitar-fb.php?' + Math.random(), {friends: response.data}, function (data) {
//             $('#procesando').modal('hide');
//             if(data.estado === 'ok') {
//                 $('#form-invitar').html(data.html);
//                 $('#invitar-res').show();
//             } else {
//                 $('#form-invitar-mensaje').show('clip').html('No hay contactos para importar');
//             }
//         }, 'json');
//     });
// }
// $('.container').on('click', '.btn-invitar-fb', function (e) {
//     e.preventDefault();
//     $('.ul-invitar li').removeClass('invitar-li-ok');
//     $('p.invitar-li-ok').removeClass('invitar-li-ok');
//     $(this).parent().addClass('invitar-li-ok');
//     FB.getLoginStatus(function (response) {
//         if (response.status === 'connected') {
//             getFriendsFB();
//         } else {
//             FB.login(function (response) {
//                 if (response.authResponse && response.status === 'connected') {
//                     getFriendsFB();
//                 }
//             }, {
//                 scope: 'email'
//             });
//         }
//     });
// });
$('.container').on('click', '.btn-invitar-manual', function (e) {
    e.preventDefault();
    $('#invitar-res').hide();
    $('.ul-invitar li').removeClass('invitar-li-ok');
    $(this).parent('p').addClass('invitar-li-ok');
    $('.btn-invitar-submit').removeAttr('disabled').removeClass('disabled');
    $('#procesando').modal('show');
    $.post('ax/invitar-manual.php', function (data) {
        $('#procesando').modal('hide');
        $('#invitar-res').show();
        $('#form-invitar').html(data.html);
    }, 'json');
});
$('.container').on('click', '.btn-invitar-submit:visible', function (e) {
    e.preventDefault();
    // valida
    if($('input[name="source"]').val() === 'manual') {
        $('input[name="invitado[]"]').removeClass('error');
        var error = 0,
            ok = 0,
            vacio = 0,
            n = 0;
        $('input[name="invitado[]"]').each(function () {
            n++;
            if ($.trim($(this).val()) === '') {
                vacio++;
            } else if (!esMail($.trim($(this).val()))) {
                error++;
                $(this).addClass('error');
            } else {
                ok++;
            }
        });
        if (vacio === n) {
            $('#form-invitar-mensaje').show('clip').html('Tenés que escribir alguna dirección');
            return false;
        }
        if (error > 0) {
            $('#form-invitar-mensaje').show('clip').html('Los campos marcados en rojo no son direcciones válidas');
            return false;
        }
    } else {
        if($('div.invitado input:not(".disabled"):checked').size() === 0) {
            $('#form-invitar-mensaje').show('clip').html('Tenés que elegir algún contacto');
            return false;
        }
    }
    $('#procesando').modal('show');
    $.post('ax/invitar.php', $('#form-invitar').serialize(), function (data) {
        $('#procesando').modal('hide');
        if (data.estado === 'ok') {
            $.address.path('/invitar-ok');
            $('#columna').load('columna-ok.php');
        } else {
            $('#form-invitar-mensaje').show('clip').html('Ocurrió un error');
        }
    }, 'json');
});
$('.container').on('click', '.btn-invitar-todos', function (e) {
    e.preventDefault();
    $('div.invitado input:not(".disabled")').attr('checked', 'checked');
});
$('.container').on('click', '.btn-invitar-ninguno', function (e) {
    e.preventDefault();
    $('div.invitado input:not(".disabled")').removeAttr('checked');
});
// enter
// - desactivo enter
$('#principal').on('keypress', 'input', function (e) {
    if (e.which === 13) {
        e.preventDefault();
    }
});
$('#principal').on('keypress', '#ini-palabras', function (e) {
    if (e.which === 13) {
        $('#btn-buscar').click();
    }
});
$('#principal').on('keypress', '#changuitas-palabras', function (e) {
    if (e.which === 13) {
        $('#btn-buscar-changuitas').click();
    }
});
$('#principal').on('click', 'div.changuita, div.destacada', function (e) {
    if(e.target.className.indexOf('btn-postular') !== -1) {
        return;
    }
    var id = $(this).attr('data-changuita-id');
    $.address.path('/changuita|'+id);
});
// inicio
$(document).ready(function () {
    // - modals
    $('body').off('.modal');
    $('#cargando').modal({
        show: false
    });
    $('#procesando').modal({
        show: false
    });
    $('#aviso-login').modal({
        show: false
    });
    $('#denunciar').modal({
        show: false
    });
    $('#calificar').modal({
        show: false
    });
    $('#confirmar').modal({
        show: false
    });
    $('#elegir').modal({
        show: false
    });
    $('#ventana').modal({
        show: false
    });
    // - button group
    $('.btn-group button').button();
    //
    setInterval(actualizaColumna, 1000 * 300); // 5 min
    // G+
    window.___gcfg = {
        lang: 'es-419'
    };
    (function () {
        var po = document.createElement('script');
        po.type = 'text/javascript';
        po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(po, s);
    })();
    // twitter
    ! function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + '://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'twitter-wjs');

    // modal fix [http://decadecity.net/blog/2013/03/25/modal-windows-for-small-screens-using-bootstrap-and-vertical-media-queries]
    var modal_window = $('#modal-window');
    $('a[rel=modal]').on('click', function (e) {
    var scroll_position = $(window).scrollTop(), // Where did we start in the window.
        return_position = false; // Should we return to the start position?
    e.preventDefault();
    // Build and show the modal.
    modal_window.modal({
      'remote': $(this).attr('href') + ' #modal-target' // Get remote content from the link.
    }).on('show', function () {
      if (modal_window.css('position') === 'absolute') {
        // We will need to return to where we were.
        return_position = true;
        // Jump to the top of the modal.
        $(window).scrollTop(modal_window.offset().top);
      }
    }).on('hidden', function () {
      if (return_position) {
        // Return to where we were.
        $(window).scrollTop(scroll_position);
      }
    }).modal('show');
    });
});