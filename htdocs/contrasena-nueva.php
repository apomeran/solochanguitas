<div class="alert alert-success">
    <p>Pod&eacute;s elegir una nueva contrase&ntilde;a.</p>
</div>
<form id="contrasena-nueva" class="form-horizontal" >
    <fieldset>
        <div class="control-group">
            <label class="control-label" for="nueva-clave">Contrase&ntilde;a</label>
            <div class="controls">
                <input type="password" id="nueva-clave" name="nueva-clave" value="" />
                <a class="ayuda" title="Este campo es obligatorio. Ten&eacute;s que ingresar una contrase&ntilde;a de por lo menos 8 (ocho) caracteres, que pueden ser letras (may&uacute;sculas y min&uacute;sculas) y n&uacute;meros. Ej.: miCLAveS3cr3t4"><i class="icon-question-sign"></i></a><span class="help-block"></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="nueva-clave2">Repetir contrase&ntilde;a</label>
            <div class="controls">
                <input type="password" id="nueva-clave2" name="nueva-clave2" value="" />
                <a class="ayuda" title="Este campo es obligatorio. Ten&eacute;s que ingresar la misma contrase&ntilde;a que en el campo anterior."><i class="icon-question-sign"></i></a><span class="help-block"></span>
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-success btn-large" id="btn-contrasena-nueva">Enviar</button>
            <span class="help-inline text-error" id="validar"></span>
        </div>
    </fieldset>
</form>
<div id="contrasena-nueva-mensaje" class="alert alert-error hide"></div>