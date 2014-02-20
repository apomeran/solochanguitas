<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
// filtros
$filtrar = array();
if($_POST["changuita"] != -1)
    $filtrar[] = "pr.changuita = ".$bd->real_escape_string($_POST["changuita"]);
//
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
//
$sql = "select pr.id, pr.pregunta, pr.pregunta_fecha, pr.respuesta, pr.respuesta_fecha, usu.nombre, usu.apellido, ch.titulo from preguntas as pr left join changuitas as ch on ch.id = pr.changuita left join usuarios as usu on pr.usuario = usu.id where pr.activo = '1' $filtros order by pregunta_fecha desc";
$res = $bd->query($sql);
if($res->num_rows > 0) {
    include("../class/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("www.solochanguitas.com.ar")
                                 ->setLastModifiedBy("www.solochanguitas.com.ar");
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Preguntas');
    $sheet->setCellValue("A1", "Changuita");
    $sheet->setCellValue("B1", "Pregunta");
    $sheet->setCellValue("C1", "Fecha pregunta");
    $sheet->setCellValue("D1", "Usuario");
    $sheet->setCellValue("E1", "Respuesta");
    $sheet->setCellValue("F1", "Fecha respuesta");

    $i = 2;
    while($fila = $res->fetch_assoc()) {
        $sheet->setCellValue("A".$i, $fila["titulo"]);
        $sheet->setCellValue("B".$i, $fila["pregunta"]);
        $sheet->setCellValue("C".$i, $f->convertirMuestra($fila["pregunta_fecha"], "fecha"));
        $sheet->setCellValue("D".$i, $fila["nombre"]." ".$fila["apellido"]);
        $sheet->setCellValue("E".$i, $fila["respuesta"]);
        $sheet->setCellValue("F".$i, $f->convertirMuestra($fila["respuesta_fecha"], "fecha"));

        $i++;
    }
    $sheet->getStyle("A1:F1")->getFont()->setBold(true);
    $sheet->getStyle("A1:F1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
    $sheet->getStyle("A1:F$i")->getFont()->setSize(10);
    foreach(range("A", "F") as $v)
        $sheet->getColumnDimension($v)->setAutoSize(true);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $archivo = "Solochanguitas - Preguntas ".date("d-m-Y H-i-s").".xlsx";
    $objWriter->save("../xls/".$archivo);
    $data["archivo"] = $archivo;
    $data["estado"] = "ok";
}
else
    $data["estado"] = "vacio";
echo json_encode($data);
?>