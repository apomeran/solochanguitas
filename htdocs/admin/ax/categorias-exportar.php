<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$order = array("", "orden asc", "orden desc", "categoria asc", "categoria desc");
// filtros
$filtrar = array();
// buscar
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(categoria like '%$buscar%'')";
}
//
$orden = $bd->real_escape_string($_POST["orden"]);
$ordenar = "order by ".$order[$orden];
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
$sql = "select id, categoria, orden from categorias where activo = '1' $filtros $ordenar";
$res = $bd->query($sql);
if($res->num_rows > 0) {
    include("../class/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("www.solochanguitas.com.ar")
                                 ->setLastModifiedBy("www.solochanguitas.com.ar");
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Categorias');
    $sheet->setCellValue("A1", "Categoría");
    $sheet->setCellValue("B1", "Orden");
    $sheet->setCellValue("C1", "Subcategorías");

    $i = 2;
    while($fila = $res->fetch_assoc()) {
        $sqlS = "select id from subcategorias where categoria = ".$fila["id"]." and activo = '1'";
        $resS = $bd->query($sqlS);
        $nS = $resS->num_rows;

        $sheet->setCellValue("A".$i, $fila["categoria"]);
        $sheet->setCellValue("B".$i, $fila["orden"]);
        $sheet->setCellValue("C".$i, $nS);

        $i++;
    }
    $sheet->getStyle("A1:C1")->getFont()->setBold(true);
    $sheet->getStyle("A1:C1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
    $sheet->getStyle("A1:C$i")->getFont()->setSize(10);
    foreach(range("A", "C") as $v)
        $sheet->getColumnDimension($v)->setAutoSize(true);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $archivo = "Solochanguitas - Categorias ".date("d-m-Y H-i-s").".xlsx";
    $objWriter->save("../xls/".$archivo);
    $data["archivo"] = $archivo;
    $data["estado"] = "ok";
}
else
    $data["estado"] = "vacio";
echo json_encode($data);
?>