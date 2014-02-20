	</div>
	<div id="procesando" class="modal hide fade" data-backdrop="static" data-keyboard="false" tabindex="-1">
		<div class="modal-header">
			<img src="../img/cargando.gif" alt="cargando" />
		</div>
		<div class="modal-body">
			<p>Por favor, esper&aacute; unos segundos mientras el sistema procesa los datos</p>
		</div>
	</div>
	<div class="modal hide fade" id="modal-borrar">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h3>Confirmar</h3>
	    </div>
	    <div class="modal-body">
	        <p>Vas a borrar un dato, ¿estás seguro?</p>
	    </div>
	    <div class="modal-footer">
	    	<input type="hidden" name="id" value="" />
	        <input type="hidden" name="tabla" value="" />
	        <button type="button" class="btn btn-success btn-borrar-ok" data-dismiss="modal" aria-hidden="true">Aceptar</button>
	        <button type="button" class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Cancelar</button>
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
	<script src="../js/bootstrap.min.js"></script>
	<script src="../js/bootstrap-modalmanager.js"></script>
	<script src="../js/bootstrap-modal.js"></script>
	<script src="js/jquery.highlight.js"></script>
	<script src="js/admin.js"></script>
	</body>
</html>