<?php
include_once("../includes/config.php");
$data["estado"] = "";
$data["error"] = "";
$data["html"] = "<input type='hidden' name='source' value='gm' />";
if(!isset($_POST["data"]))
    exit;
$contactos = $_POST["data"];
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$bd = conectar();
// $sql = "select mail from usuarios where activo = '2'";
// $res = $bd->query($sql);
// $yaUsuarios = array();
// while($fila = $res->fetch_assoc())
//     $yaUsuarios[] = $fila["mail"];
$n = 0;
$htmlContact = array();
foreach ($contactos["feed"]["entry"] as $v) {
    $mail = $v['gd$email'][0]["address"];
    if($mail == "")
        continue;
    $name = $v['title']['$t'];
    $spanNombre = "";
    if($name != "")
        $spanNombre = "<br/><span>$name</span>";
    $divClass = "";
    $checked = "checked";
    $disabled = "";
    // if(in_array($mail, $yaUsuarios)) {
    //     $divClass = "yaInvitado";
    //     $checked = "";
    //     $disabled = "disabled";
    // }
    $htmlContact[] = "<div class='invitado $divClass $disabled'><input type='checkbox' name='invitado[]' value='".$mail."' $checked $disabled /><label>".$mail.$spanNombre."</label></div>";
    $n++;
}
$data["html"] .= "<p>Ten&eacute;s $n contactos. Eleg&iacute; a qui&eacute;nes quer&eacute;s invitar.</p><p><button class='btn btn-link btn-invitar-todos'>Todos</button> | <button class='btn btn-link btn-invitar-ninguno'>Ninguno</button></p>".implode("", $htmlContact);
$data["estado"] = "ok";
if($n == 0)
    $data["estado"] = "vacio";
echo json_encode($data);
?>