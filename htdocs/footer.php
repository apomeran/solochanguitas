	</div>
		<div id="footer">
			<div class="row">
				<div class="span12">
					<div class="navbar">
						<div class="navbar-inner">
							<p class="navbar-text pull-left">&copy; <?php echo date("Y") ?> Todos los derechos reservados</p>
							<ul class="nav pull-right">
								<li><a href="#/quienes" rel="address:/quienes">Qui&eacute;nes somos</a></li>
								<li><a href="#/faq" rel="address:/faq">Preguntas frecuentes</a></li>
								<li><a href="#/condiciones" rel="address:/condiciones">T&eacute;rminos y condiciones de uso</a></li>
								<li><a href="#/privacidad" rel="address:/privacidad">Pol&iacute;tica de privacidad</a></li>
								<li><a href="#/contacto" rel="address:/contacto">Contacto</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="cargando" class="modal hide fade" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-header">
			<img src="img/cargando.gif" alt="cargando" />
		</div>
		<div class="modal-body">
			<p>Cargando...</p>
		</div>
	</div>
	<div id="procesando" class="modal hide fade" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-header">
			<img src="img/cargando.gif" alt="cargando" />
		</div>
		<div class="modal-body">
			<p>Por favor, esper&aacute; unos segundos mientras el sistema procesa los datos</p>
		</div>
	</div>
	<div id="aviso-login" class="modal hide fade" tabindex="-1">
		<div class="modal-header">
			<h3>&iexcl;Bienvenido!</h3>
		</div>
		<div class="modal-body">
			<p>Ten&eacute;s que iniciar sesi&oacute;n para publicar o postularte a una changuita. Si todav&iacute;a no ten&eacute;s una cuenta, cre&aacute; una en menos de 1 minuto</p>
		</div>
		<div class="modal-footer">
			<a href="#/editar-usuario" rel="address:/editar-usuario" class="btn btn-success aviso-login-cerrar" data-dismiss="modal">Registrate</a> &nbsp; &nbsp; <button class="btn btn-primary" data-dismiss="modal" id="btn-modal-login">Inici&aacute; sesi&oacute;n</button> &nbsp; &nbsp; <button class="btn btn-link" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	<div id="aviso-bloqueado" class="modal hide fade" tabindex="-1">
		<div class="modal-header">
			<h3>Atenci&oacute;n</h3>
		</div>
		<div class="modal-body">
			<p>Tu usuario fue bloqueado porque super&oacute; el l&iacute;mite de deuda permitido. Podr&aacute;s volver a publicar changuitas y postularte cuando realices el pago.</p>
		</div>
		<div class="modal-footer">
			<button class="btn btn-link" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	<div id="denunciar" class="modal hide fade" data-focus-on="textarea:first" tabindex="-1">
		<div class="modal-header">
			<h3>Denunciar</h3>
		</div>
		<div class="modal-body">
			<p>Por favor, aclaranos los motivos de la denuncia:</p>
			<textarea name="denuncia" id="denuncia"></textarea>
		</div>
		<div class="modal-footer">
			<button class="btn btn-warning btn-denunciar-ok" data-dismiss="modal">DENUNCIAR</button> &nbsp; &nbsp; <button class="btn btn-link" data-dismiss="modal">Cancelar</button>
		</div>
	</div>
	<div id="calificar" class="modal hide fade" tabindex="-1">
		<div class="modal-header">
			<h3>Calificar</h3>
		</div>
		<div class="modal-body">
			<p>Las calificaciones permanecen ocultas y tu contraparte no podr&aacute; visualizarlas hasta que finalice el proceso de calificaci&oacute;n.</p>
			<h4>&iquest;Se realiz&oacute; la changuita?</h4>
			<div class="calificar-realizo">
				<label><input type="radio" name="realizo" class="btn-calificar-realizo" value="1"> S&iacute;</label>
				<label><input type="radio" name="realizo" class="btn-calificar-realizo" value="0"> No</label>
			</div>
			<p>Si la changuita no se concret&oacute;, solo pod&eacute;s calificar a tu contraparte en forma neutral o negativa, lo que afectar&aacute; negativamente su reputaci&oacute;n.</p>
			<h4>&iquest;C&oacute;mo calific&aacute;s a la otra parte?</h4>
			<p>Una vez que confirmes la calificaci&oacute;n, ya no podr&aacute;s cambiarla. Esta puede incidir negativamente en la reputaci&oacute;n del otro usuario, por lo que te recomendamos que lo califiques como <em>negativo</em> solo cuando haya habido mal trato, expresa mala voluntad, grave incumplimiento de lo comunicado en la publicaci&oacute;n y/o ausencia en la fecha y hora pautadas. <strong>Calific&aacute; con responsabilidad</strong>.</p>
			<div class="btn-group" data-toggle="buttons-radio">
				<button class="btn disabled" value="0"><img src="img/0.gif" alt="negativo" /><br />Negativo</button>
				<button class="btn disabled" value="1"><img src="img/2.gif" alt="neutro" /><br />Neutro</button>
				<button class="btn btn-calificar-positivo disabled" value="2"><img src="img/4.gif" alt="positivo" /><br />Positivo</button>
			</div>
			<h4>&iquest;Qu&eacute; comentarios pod&eacute;s hacer sobre la amabilidad, claridad y efectividad en el intercambio?</h4>
			<p>Te pedimos que escribas sobre el intercambio con la m&aacute;xima objetividad posible y te recordamos que solo pod&eacute;s mencionar aspectos negativos de la contraparte en el caso de haber experimentado mal trato, expresa mala voluntad, grave incumplimiento de lo comunicado en la publicaci&oacute;n y/o ausencia en la fecha y hora pautadas (m&aacute;ximo 160 caracteres).</p>
			<textarea name="comentario" id="calificar-comentario" maxlength="160"></textarea>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success btn-calificar-ok">Confirmar</button> &nbsp; &nbsp; <button class="btn btn-link" data-dismiss="modal">Cancelar</button>
		</div>
	</div>
	<div id="confirmar" class="modal hide fade" tabindex="-1">
		<div class="modal-header">
			<h3>Atenci&oacute;n</h3>
		</div>
		<div class="modal-body">
			<p>&iquest;Est&aacute;s seguro?</p>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success btn-confirmar-ok" data-dismiss="modal">Aceptar</button> &nbsp; &nbsp; <button class="btn btn-danger" data-dismiss="modal">Cancelar</button>
		</div>
	</div>
	<div id="elegir" class="modal hide fade" tabindex="-1">
		<div class="modal-header">
			<h3>Postulantes</h3>
			<h4>Eleg&iacute; al que prefieras para que haga tu changuita.</h4>
		</div>
		<div class="modal-body">
			<img src="img/cargando.gif" alt="cargando" />
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	<div id="ventana" class="modal hide fade" tabindex="-1">
		<div class="modal-header">
			<h3></h3>
			<h4></h4>
		</div>
		<div class="modal-body">
			<img src="img/cargando.gif" alt="cargando" />
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Cerrar</button>
		</div>
	</div>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-36793215-1']);
		_gaq.push(['_trackPageview']);
		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap-modalmanager.js"></script>
	<script src="js/bootstrap-modal.js"></script>
	<script src="js/jquery.address-1.4.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.carouFredSel.js"></script>
	<script src="http://platform.linkedin.com/in.js">
	api_key: ye2q95grjyiw
	authorize: true
	scope: r_emailaddress
	</script>
	<script type="text/javascript" src="https://www.mercadopago.com/org-img/jsapi/mptools/buttons/render.js"></script>
	<script src="js/main.js"></script>
	<script src="https://apis.google.com/js/client.js"></script>
	</body>
</html>