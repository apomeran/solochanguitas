<?php
include_once("../includes/config.php");
$data["estado"] = "";
$data["error"] = "";
$data["html"] = "<input type='hidden' name='source' value='fb' />";
if(!isset($_POST["friends"]))
	exit;
include_once("../class/seguridad.php");
$s = new Seguridad();
$s->permitir(0);
$bd = conectar();
$sql = "select mail from usuarios where activo = '2'";
$res = $bd->query($sql);
$yaUsuarios = array();
while($fila = $res->fetch_assoc())
    $yaUsuarios[] = $fila["mail"];
include_once("../includes/facebook.php");
$facebook = new Facebook(array(
    'appId'  => '511297335556303',
    'secret' => '574a1a675f22239be84c587f9dda88e6',
));
$data["html"] .= "<p>Ten&eacute;s ".count($_POST["friends"])." contactos. Eleg&iacute; a qui&eacute;nes quer&eacute;s invitar.</p><p>Todos | Ninguno</p>";
$n = 0;
foreach ($_POST["friends"] as $v) {
    if($n > 2)
        continue;
    $uid = $bd->real_escape_string($v["id"]);
    $name = $v["name"];
    $fql = "SELECT email from user where uid = $uid";
    $ret_obj = $facebook->api(array('method' => 'fql.query', 'query' => $fql));
    $mail = $ret_obj[0]['email'];
    $spanNombre = "";
    if($name != $mail)
        $spanNombre = "<br/><span>$name</span>";
    $divClass = "";
    $checked = "checked";
    if(in_array($mail, $yaUsuarios)) {
        $divClass = "yaInvitado";
        $checked = "";
    }
    $data["html"] .= "<div class='invitado ".$divClass."'><input type='checkbox' name='invitado[]' value='".$mail."' $checked /><label>".$mail.$spanNombre."</label></div>";
    $n++;
}
$data["estado"] = "ok";
echo json_encode($data);
?>