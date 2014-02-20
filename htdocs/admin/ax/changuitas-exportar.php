<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$order = array("", "ch.fecha desc", "ch.fecha asc");
$planes = array();
$sql = "select id, plan from planes where activo = '1'";
$res = $bd->query($sql);
while($fila = $res->fetch_assoc())
    $planes[$fila["id"]] = $fila["plan"];
$noR = array("No", "1 usuario", "Ambos usuarios", "");
$sn = array("No", "Sí", "Gratis");
$estados = array("Publicada", "En curso", "Realizada", "Realizada y calificada", "Borrada");
$fee = array("No", "Sí", "No se hizo la changuita", "");
// filtros
$filtrar = array();
if($_POST["categoria"] != -1) {
    $filtrar[] = "ch.categoria = ".$bd->real_escape_string($_POST["categoria"]);
    if(isset($_POST["subcategoria"]) && $_POST["subcategoria"] != -1)
        $filtrar[] = "ch.subcategoria = ".$bd->real_escape_string($_POST["subcategoria"]);
}
if($_POST["localidad"] != -1) {
    $filtrar[] = "ch.localidad = ".$bd->real_escape_string($_POST["localidad"]);
    if(isset($_POST["barrio"]) && $_POST["barrio"] != -1)
        $filtrar[] = "ch.barrio = ".$bd->real_escape_string($_POST["barrio"]);
}
if($_POST["estado"] != -1) {
    if($_POST["estado"] < 4)
        $filtrar[] = "(ch.estado = '".$bd->real_escape_string($_POST["estado"])."' and ch.activo = '1')";
    else
        $filtrar[] = "ch.activo = '0'";
}
if($_POST["plan"] != -1)
    $filtrar[] = "ch.plan = '".$bd->real_escape_string($_POST["plan"])."'";
if($_POST["pagado"] != -1)
    $filtrar[] = "ch.pagado = '".$bd->real_escape_string($_POST["pagado"])."'";
if($_POST["fee"] != -1) {
    if($_POST["fee"] == "0")
        $filtrar[] = "(ch.fee = '0' and (ch.estado = '2' || ch.estado = '3'))";
    else
        $filtrar[] = "ch.fee = '".$bd->real_escape_string($_POST["fee"])."'";
}
if($_POST["vencida"] != -1)
    $filtrar[] = "ch.vencida = '".$bd->real_escape_string($_POST["vencida"])."'";
// buscar
$highlight = array();
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(ch.titulo like '%$buscar%' or ch.descripcion like '%$buscar%')";
    $highlight[] = "'".$buscar."'";
}
//
$orden = $bd->real_escape_string($_POST["orden"]);
$ordenar = "order by ".$order[$orden];
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
//
$sql = "select ch.id, usu.nombre, usu.apellido, loc.localidad, ch.contratado, ch.fecha, ch.titulo, cat.categoria, scat.subcategoria, bar.barrio, ch.descripcion, ch.cuando, ch.cuando_dias, ch.cuando_fecha, ch.cuando_hora_desde, ch.cuando_hora_hasta, ch.precio, ch.plan, ch.vencida, ch.pagado, ch.estado, ch.fecha_contratacion, ch.fee, ch.activo from changuitas as ch left join usuarios as usu on ch.usuario = usu.id left join localidades as loc on ch.localidad = loc.id left join barrios as bar on ch.barrio = bar.id left join categorias as cat on ch.categoria = cat.id left join subcategorias as scat on ch.subcategoria = scat.id where ch.id != 0 $filtros $ordenar";
$res = $bd->query($sql);
if($res->num_rows > 0) {
    include("../class/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("www.solochanguitas.com.ar")
                                 ->setLastModifiedBy("www.solochanguitas.com.ar");
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Changuitas');
    $sheet->setCellValue("A1", "Título");
    $sheet->setCellValue("B1", "Categoría");
    $sheet->setCellValue("C1", "Subcategoría");
    $sheet->setCellValue("D1", "Zona");
    $sheet->setCellValue("E1", "Localidad/Barrio");
    $sheet->setCellValue("F1", "Usuario");
    $sheet->setCellValue("G1", "Contratado");
    $sheet->setCellValue("H1", "Descripción");
    $sheet->setCellValue("I1", "Palabras clave");
    $sheet->setCellValue("J1", "A realizar");
    $sheet->setCellValue("K1", "Horario");
    $sheet->setCellValue("L1", "Precio");
    $sheet->setCellValue("M1", "Plan");
    $sheet->setCellValue("N1", "Fecha");
    $sheet->setCellValue("O1", "Estado");
    $sheet->setCellValue("P1", "Pagada");
    $sheet->setCellValue("Q1", "Fee pagado");
    $sheet->setCellValue("R1", "Vencida");
    $sheet->setCellValue("S1", "Preguntas realizadas");
    $sheet->setCellValue("T1", "Postulaciones");
    $sheet->setCellValue("U1", "Calificada como no hecha");

    $i = 2;
    while($fila = $res->fetch_assoc()) {

        $sql = "select hecha from calificaciones where changuita = ".$fila["id"]." and activo = '1'";
        $res2 = $bd->query($sql);
        $noHecha = 0;
        while($filaCal = $res2->fetch_assoc()) {
            if($filaCal["hecha"] == "1")
                $noHecha++;
        }
        $sql = "select id from postulaciones where usuario = ".$fila["id"]."";
        $res2 = $bd->query($sql);
        $nP = $res2->num_rows;
        $sql = "select id from preguntas where changuita = ".$fila["id"]." and activo = '1'";
        $res2 = $bd->query($sql);
        $nPr = $res2->num_rows;
        $contratado = "";
        if($fila["contratado"] > 0) {
            $sql = "select nombre, apellido from usuarios where id = ".$fila["contratado"];
            $res2 = $bd->query($sql);
            $filaC = $res2->fetch_assoc();
            $contratado = $filaC["nombre"]." ".$filaC["apellido"];
        }
        if($contratado != "")
            $contratado .= " (".$f->convertirMuestra($fila["fecha_contratacion"], "fecha").")";
        if($fila["activo"] == "0")
            $fila["estado"] = "4";
        $cuando = "";
        if($fila["cuando"] == 1)
            $cuando = "En cualquier momento, a combinar";
        else if($fila["cuando"] == 2) {
            $cuandoDias = explode(",", $fila["cuando_dias"]);
            $cuandoDia = array();
            foreach ($cuandoDias as $v)
                $cuandoDia[] = $dias[$v];
            $cuando = implode(", ", $cuandoDia);
        }
        else if($fila["cuando"] == 3)
            $cuando = $f->convertirMuestra($fila["cuando_fecha"], "fecha");
        if($fila["plan"] == 1)
            $fila["pagado"] = "2";
        if($fila["estado"] == "0" || $fila["estado"] == "4") {
            $fila["fee"] = "3";
            $noHecha = 3;
        }
        $horario = "";
        if($fila["cuando_hora_desde"] != "00:00:00")
          $horario = "Entre las ".substr($fila["cuando_hora_desde"], 0, 5)." hs y las ".substr($fila["cuando_hora_hasta"], 0, 5)." hs";
        $palabras = array();
        $sql = "select palabra from changuitas_palabras where changuita = ".$fila["id"];
        $resPc = $bd->query($sql);
        while($filaPc = $resPc->fetch_assoc())
            $palabras[] = $filaPc["palabra"];

        $sheet->setCellValue("A".$i, $fila["titulo"]);
        $sheet->setCellValue("B".$i, $fila["categoria"]);
        $sheet->setCellValue("C".$i, $fila["subcategoria"]);
        $sheet->setCellValue("D".$i, $fila["localidad"]);
        $sheet->setCellValue("E".$i, $fila["barrio"]);
        $sheet->setCellValue("F".$i, $fila["nombre"]." ".$fila["apellido"]);
        $sheet->setCellValue("G".$i, $contratado);
        $sheet->setCellValue("H".$i, nl2br($fila["descripcion"]));
        $sheet->setCellValue("I".$i, implode(", ", $palabras));
        $sheet->setCellValue("J".$i, $cuando);
        $sheet->setCellValue("K".$i, $horario);
        $sheet->setCellValue("L".$i, $fila["precio"]);
        $sheet->setCellValue("M".$i, $planes[$fila["plan"]]);
        $sheet->setCellValue("N".$i, $f->convertirMuestra($fila["fecha"], "fecha"));
        $sheet->setCellValue("O".$i, $estados[$fila["estado"]]);
        $sheet->setCellValue("P".$i, $sn[$fila["pagado"]]);
        $sheet->setCellValue("Q".$i, $fee[$fila["fee"]]);
        $sheet->setCellValue("R".$i, $sn[$fila["vencida"]]);
        $sheet->setCellValue("S".$i, $nPr);
        $sheet->setCellValue("T".$i, $nP);
        $sheet->setCellValue("U".$i, $noR[$noHecha]);

        $i++;
    }
    $sheet->getStyle("A1:U1")->getFont()->setBold(true);
    $sheet->getStyle("A1:U1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
    $sheet->getStyle("A1:U$i")->getFont()->setSize(10);
    foreach(range("A", "U") as $v) {
        if($v == "H")
            continue;
        $sheet->getColumnDimension($v)->setAutoSize(true);
    }
    $sheet->getColumnDimension("H")->setWidth(25);
    for($j=2;$j<=$i;$j++)
        $sheet->getStyle('H'.$j)->getAlignment()->setWrapText(true);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $archivo = "Solochanguitas - Changuitas ".date("d-m-Y H-i-s").".xlsx";
    $objWriter->save("../xls/".$archivo);
    $data["archivo"] = $archivo;
    $data["estado"] = "ok";
}
else
    $data["estado"] = "vacio";
echo json_encode($data);
?>