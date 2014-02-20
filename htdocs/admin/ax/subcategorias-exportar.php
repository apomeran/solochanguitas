<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$order = array("", "s.orden asc", "s.orden desc", "s.subcategoria asc", "s.subcategoria desc");
// filtros
$filtrar = array();
if($_POST["categoria2"] != -1)
    $filtrar[] = "s.categoria = ".$bd->real_escape_string($_POST["categoria2"]);
// buscar
if($_POST["buscar"] != "") {
    $buscar = $bd->real_escape_string($_POST["buscar"]);
    $filtrar[] = "(s.subcategoria like '%$buscar%')";
    $highlight[] = "'".$buscar."'";
}
//
$orden = $bd->real_escape_string($_POST["orden"]);
$ordenar = "order by categoriaId asc,  ".$order[$orden];
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
$sql = "select s.id, c.categoria, s.categoria as categoriaId, s.subcategoria, s.orden, s.hora from subcategorias as s left join categorias as c on s.categoria = c.id where s.activo = '1' $filtros $ordenar";
$res = $bd->query($sql);
if($res->num_rows > 0) {
    include("../class/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("www.solochanguitas.com.ar")
                                 ->setLastModifiedBy("www.solochanguitas.com.ar");
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Subcategorias');
    $sheet->setCellValue("A1", "Subcategoría");
    $sheet->setCellValue("B1", "Categoría");
    $sheet->setCellValue("C1", "Precio por hora");
    $sheet->setCellValue("D1", "Orden");

    $i = 2;
    while($fila = $res->fetch_assoc()) {
        $sheet->setCellValue("A".$i, $fila["subcategoria"]);
        $sheet->setCellValue("B".$i, $fila["categoria"]);
        $sheet->setCellValue("C".$i, $fila["hora"]);
        $sheet->setCellValue("D".$i, $fila["orden"]);

        $i++;
    }
    $sheet->getStyle("A1:D1")->getFont()->setBold(true);
    $sheet->getStyle("A1:D1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
    $sheet->getStyle("A1:D$i")->getFont()->setSize(10);
    foreach(range("A", "D") as $v)
        $sheet->getColumnDimension($v)->setAutoSize(true);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $archivo = "Solochanguitas - Subcategorias ".date("d-m-Y H-i-s").".xlsx";
    $objWriter->save("../xls/".$archivo);
    $data["archivo"] = $archivo;
    $data["estado"] = "ok";
}
else
    $data["estado"] = "vacio";
echo json_encode($data);
?>