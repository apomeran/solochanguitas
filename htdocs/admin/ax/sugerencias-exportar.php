<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$order = array("", "s.fecha desc", "s.fecha asc");
$visto = array("Sí", "No");
// filtros
$filtrar = array();
if($_POST["visto"] != -1)
    $filtrar[] = "s.activo = '".$bd->real_escape_string($_POST["visto"])."'";
// buscar
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(s.sugerencia like '%$buscar%')";
}
//
$orden = $bd->real_escape_string($_POST["orden"]);
$ordenar = "order by ".$order[$orden];
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
//
$sql = "select s.id, s.sugerencia, s.fecha, s.activo, u.nombre, u.apellido from sugerencias as s left join usuarios as u on s.usuario = u.id where s.id > 0 $filtros $ordenar";
$res = $bd->query($sql);
if($res->num_rows > 0) {
    include("../class/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("www.solochanguitas.com.ar")
                                 ->setLastModifiedBy("www.solochanguitas.com.ar");
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Sugerencias');
    $sheet->setCellValue("A1", "Categoría sugerida");
    $sheet->setCellValue("B1", "Usuario");
    $sheet->setCellValue("C1", "Fecha");
    $sheet->setCellValue("D1", "Visto");

    $i = 2;
    while($fila = $res->fetch_assoc()) {

        $sheet->setCellValue("A".$i, $fila["sugerencia"]);
        $sheet->setCellValue("B".$i, $fila["nombre"]." ".$fila["apellido"]);
        $sheet->setCellValue("C".$i, $f->convertirMuestra($fila["fecha"], "fecha"));
        $sheet->setCellValue("D".$i, $visto[$fila["activo"]]);

        $i++;
    }
    $sheet->getStyle("A1:D1")->getFont()->setBold(true);
    $sheet->getStyle("A1:D1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
    $sheet->getStyle("A1:D$i")->getFont()->setSize(10);
    foreach(range("A", "D") as $v)
        $sheet->getColumnDimension($v)->setAutoSize(true);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $archivo = "Solochanguitas - Sugerencias ".date("d-m-Y H-i-s").".xlsx";
    $objWriter->save("../xls/".$archivo);
    $data["archivo"] = $archivo;
    $data["estado"] = "ok";
}
else
    $data["estado"] = "vacio";
echo json_encode($data);
?>