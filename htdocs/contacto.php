<h3>Contacto</h3>
<div class="alert alert-success" id="contacto-ok" style="display:none;">
    <p>Mensaje enviado. Gracias por contactarnos. Te responderemos a la brevedad.</p>
</div>
<form class="form-horizontal" id="contacto">
    <fieldset>
        <div class="control-group">
            <label class="control-label">Para</label>
            <div class="controls">
                <p class="contacto-para">info@solochanguitas.com.ar</p>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="nombre">Nombre</label>
            <div class="controls">
                <input type="text" id="nombre" name="nombre" value="" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="mail">E-mail</label>
            <div class="controls">
                <input type="text" id="mail" name="mail" value="" />
                <a class="ayuda" title="Este campo es obligatorio. Ten&eacute;s que usar una direcci&oacute;n v&aacute;lida."><i class="icon-question-sign"></i></a><span class="help-block"></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="mensaje">Mensaje</label>
            <div class="controls">
                <textarea id="mensaje" name="mensaje"></textarea>
                <a class="ayuda" title="Este campo es obligatorio."><i class="icon-question-sign"></i></a><span class="help-block"></span>
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-success btn-large" id="boton-contacto">Enviar</button>
            <span class="help-inline text-error" id="contacto-validar"></span>
        </div>
    </fieldset>
</form>