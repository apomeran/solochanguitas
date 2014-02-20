<?php
$sql = "select nombre from usuarios where id = ".$_SESSION[SesionId]." and activo = '2' and nivel = '1'";
$res = $bd->query($sql);
if($res->num_rows != 1) {
?>

<?php
}
else {
    $fila = $res->fetch_assoc();
?>
<div class="navbar">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="index.php"><?php echo $fila["nombre"] ?></a>
            <ul class="nav">
                <li class="divider-vertical"></li>
                <li id="item00"><a href="usuarios.php">Usuarios</a></li>
                <li id="item01"><a href="changuitas.php">Changuitas</a></li>
                <li id="item03"><a href="preguntas.php">Preguntas y respuestas</a></li>
                <li id="item02"><a href="categorias.php">Categorías</a></li>
                <li id="item06"><a href="subcategorias.php">Subcategorías</a></li>
                <li id="item04"><a href="sugerencias.php">Sugerencias</a></li>
                <li id="item05"><a href="denuncias.php">Denuncias</a></li>
            </ul>
            <ul class="nav pull-right">
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</div>
<?php
}
?>