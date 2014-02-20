<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$sexo = array("", "F", "M");
$activo = array("N", "N", "S");
$educacion = array("", "Primario completo", "Primario incompleto", "Primario en curso", "Secundario completo", "Secundario incompleto", "Secundario en curso", "Terciario completo", "Terciario incompleto", "Terciario en curso", "Universitario completo", "Universitario incompleto", "Universitario en curso");
$calificacion = array("Negativo", "Neutro", "Positivo");
$order = array("", "usu.apellido asc, usu.nombre asc, usu.nacimiento asc", "usu.apellido desc, usu.nombre desc, usu.nacimiento desc", "usu.fecha asc", "usu.fecha desc");
// filtros
$filtrar = array();
if($_POST["sexo"] != -1)
    $filtrar[] = "usu.sexo = ".$bd->real_escape_string($_POST["sexo"]);
if($_POST["localidad2"] != -1)
    $filtrar[] = "usu.localidad = ".$bd->real_escape_string($_POST["localidad2"]);
if($_POST["estado"] != -1) {
    if($_POST["estado"] == 0)
        $filtrar[] = "usu.balance < 0";
    else
        $filtrar[] = "usu.balance > 0";
}
if($_POST["activo"] != -1) {
    if($_POST["activo"] < 2)
        $filtrar[] = "(usu.activo = '0' or usu.activo = '1')";
    else
        $filtrar[] = "usu.activo = '".$bd->real_escape_string($_POST["activo"])."'";
}
// buscar
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(usu.apellido like '%$buscar%' or usu.nombre like '%$buscar%' or usu.mail like '%$buscar%')";
}
//
$orden = $bd->real_escape_string($_POST["orden"]);
$ordenar = "order by ".$order[$orden];
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
//
$sql = "select usu.id, usu.nombre, usu.apellido, usu.sexo, usu.mail, usu.nacimiento, loc.localidad, usu.fecha, usu.activo, usu.balance, usu.dni, bar.barrio, usu.celular_area, usu.celular, usu.educacion, usu.institucion, usu.presentacion, usu.perfil_fb, usu.perfil_li, usu.perfil_gp from usuarios as usu left join localidades as loc on usu.localidad = loc.id left join barrios as bar on usu.barrio = bar.id where usu.activo != '-1' $filtros $ordenar";
$res = $bd->query($sql);
if($res->num_rows > 0) {
    include("../class/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("www.solochanguitas.com.ar")
                                 ->setLastModifiedBy("www.solochanguitas.com.ar");
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Usuarios');
    $sheet->setCellValue("A1", "Apellido");
    $sheet->setCellValue("B1", "Nombre");
    $sheet->setCellValue("C1", "DNI");
    $sheet->setCellValue("D1", "Sexo");
    $sheet->setCellValue("E1", "Año de nacimiento");
    $sheet->setCellValue("F1", "Zona");
    $sheet->setCellValue("G1", "E-mail");
    $sheet->setCellValue("H1", "Celular");
    $sheet->setCellValue("I1", "Educación");
    $sheet->setCellValue("J1", "Institución");
    $sheet->setCellValue("K1", "Presentación");
    $sheet->setCellValue("L1", "Perfil Facebook");
    $sheet->setCellValue("M1", "Perfil LinkedIn");
    $sheet->setCellValue("N1", "Perfil Google+");
    $sheet->setCellValue("O1", "Activo");
    $sheet->setCellValue("P1", "Estado");
    $sheet->setCellValue("Q1", "Fecha de alta");

    $sheet->setCellValue("R1", "Calificación");
    $sheet->setCellValue("S1", "Calificaciones recibidas");
    $sheet->setCellValue("T1", "Contactos en la red");
    $sheet->setCellValue("U1", "Changuitas publicadas");
    $sheet->setCellValue("V1", "Changuitas realizadas*");
    $sheet->setCellValue("W1", "Preguntas realizadas");
    $sheet->setCellValue("X1", "Postulaciones");

    $i = 2;
    while($fila = $res->fetch_assoc()) {

        $sql = "select calificacion, n from calificacion where usuario = ".$fila["id"]."";
        $res2 = $bd->query($sql);
        $filaCal = $res2->fetch_assoc();
        $nCal = $res2->num_rows;
        $sql = "select confianza from confianza where usuario = ".$fila["id"]."";
        $res2 = $bd->query($sql);
        $filaCon = $res2->fetch_assoc();
        $sql = "select id from changuitas where usuario = ".$fila["id"]." and activo = '1'";
        $res2 = $bd->query($sql);
        $nCU = $res2->num_rows;
        $sql = "select id from changuitas where contratado = ".$fila["id"]." and activo = '1'";
        $res2 = $bd->query($sql);
        $nCC = $res2->num_rows;
        $sql = "select id from postulaciones where usuario = ".$fila["id"]."";
        $res2 = $bd->query($sql);
        $nP = $res2->num_rows;
        $sql = "select id from preguntas where usuario = ".$fila["id"]." and activo = '1'";
        $res2 = $bd->query($sql);
        $nPr = $res2->num_rows;

        if($fila["sexo"] == "")
            $fila["sexo"] = 0;
        if($fila["nacimiento"] == "0000")
            $fila["nacimiento"] = "";
        if($fila["barrio"] != "")
            $fila["localidad"] .= " > ".$fila["barrio"];
        if($fila["dni"] == 0)
            $fila["dni"] = "";
        $celular = "";
        if(trim($fila["celular_area"]) != "")
            $celular .= trim($fila["celular_area"]);
        if(trim($fila["celular"]) != "")
            $celular .= " ".trim($fila["celular"]);
        $celular = trim($celular);

        if($filaCal["n"] == 0 || $nCal == 0 || !isset($calificacion[$filaCal["calificacion"]]))
            $cal = "";
        else
            $cal = $calificacion[$filaCal["calificacion"]];
        $calN = 0;
        if($filaCal["n"] > 0)
            $calN = $filaCal["n"];
        $con = 0;
        if($filaCon["confianza"] > 0)
            $con = $filaCon["confianza"];

        $sheet->setCellValue("A".$i, $fila["apellido"]);
        $sheet->setCellValue("B".$i, $fila["nombre"]);
        $sheet->setCellValue("C".$i, $fila["dni"]);
        $sheet->setCellValue("D".$i, $sexo[$fila["sexo"]]);
        $sheet->setCellValue("E".$i, $fila["nacimiento"]);
        $sheet->setCellValue("F".$i, $fila["localidad"]);
        $sheet->setCellValue("G".$i, $fila["mail"]);
        $sheet->setCellValue("H".$i, $celular);
        $sheet->setCellValue("I".$i, $educacion[$fila["educacion"]]);
        $sheet->setCellValue("J".$i, $fila["institucion"]);
        $sheet->setCellValue("K".$i, $fila["presentacion"]);
        $sheet->setCellValue("L".$i, $fila["perfil_fb"]);
        $sheet->setCellValue("M".$i, $fila["perfil_li"]);
        $sheet->setCellValue("N".$i, $fila["perfil_gp"]);

        $sheet->setCellValue("O".$i, $activo[$fila["activo"]]);
        $sheet->setCellValue("P".$i, "$ ".$fila["balance"]);
        $sheet->setCellValue("Q".$i, $f->convertirMuestra($fila["fecha"], "fecha"));

        $sheet->setCellValue("R".$i, $cal);
        $sheet->setCellValue("S".$i, $calN);
        $sheet->setCellValue("T".$i, $con);
        $sheet->setCellValue("U".$i, $nCU);
        $sheet->setCellValue("V".$i, $nCC);
        $sheet->setCellValue("W".$i, $nPr);
        $sheet->setCellValue("X".$i, $nP);

        $i++;
    }
    $sheet->getStyle("A1:X1")->getFont()->setBold(true);
    $sheet->getStyle("A1:X1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
    $sheet->getStyle("A1:X$i")->getFont()->setSize(10);
    foreach(range("A", "X") as $v) {
        if($v == "K")
            continue;
        $sheet->getColumnDimension($v)->setAutoSize(true);
    }
    $sheet->getColumnDimension("K")->setWidth(25);
    for($j=2;$j<=$i;$j++)
        $sheet->getStyle('K'.$j)->getAlignment()->setWrapText(true);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $archivo = "Solochanguitas - Usuarios ".date("d-m-Y H-i-s").".xlsx";
    $objWriter->save("../xls/".$archivo);
    $data["archivo"] = $archivo;
    $data["estado"] = "ok";
}
else
    $data["estado"] = "vacio";
echo json_encode($data);
?>