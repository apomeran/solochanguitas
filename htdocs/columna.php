<div id="columna" class="span3">
<?php
if(!isset($_SESSION[SesionId]) || $_SESSION[SesionId] == 0)
	include("columna-login.php");
else
	include("columna-ok.php");
?>
</div>