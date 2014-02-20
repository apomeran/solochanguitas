<?php
include("../../includes/config.php");
$bd = conectar();
include("../../class/funciones.php");
$f = new Funciones();
$order = array("", "d.fecha desc", "d.fecha asc");
// filtros
$filtrar = array();
if($_POST["tipo"] != "-1")
    $filtrar[] = "d.tipo = '".$bd->real_escape_string($_POST["tipo"])."'";
if($_POST["visto"] != -1)
    $filtrar[] = "d.activo = '".$bd->real_escape_string($_POST["visto"])."'";
//
$orden = $bd->real_escape_string($_POST["orden"]);
$ordenar = "order by ".$order[$orden];
$filtros = "";
if(!empty($filtrar))
    $filtros = "and ".implode(" and ", $filtrar);
$sql = "select d.id, d.tipo, d.fecha, u.nombre, u.apellido, d.comentario, d.i, d.activo from denuncias as d left join usuarios as u on d.usuario = u.id where d.id > 0 $filtros $ordenar";
$res = $bd->query($sql);
if($res->num_rows > 0) {
    include("../class/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("www.solochanguitas.com.ar")
                                 ->setLastModifiedBy("www.solochanguitas.com.ar");
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Denuncias');
    $sheet->setCellValue("A1", "Tipo");
    $sheet->setCellValue("B1", "Datos");
    $sheet->setCellValue("C1", "Comentario");
    $sheet->setCellValue("D1", "Usuario");
    $sheet->setCellValue("E1", "Fecha respuesta");

    $i = 2;
    while($fila = $res->fetch_assoc()) {

        switch ($fila["tipo"]) {
            case 'u':
                $tipo = "Usuario";
                $sql2 = "select id, nombre, apellido from usuarios where id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = $fila2["nombre"]." ".$fila2["apellido"];
                break;
            case 'ch':
                $tipo = "Changuita";
                $sql2 = "select id, titulo from changuitas where id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = $fila2["titulo"];
                break;
            case 'p':
                $tipo = "Pregunta";
                $sql2 = "select ch.id, p.pregunta, ch.titulo from preguntas as p left join changuitas as ch on p.changuita = ch.id where p.id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = "Changuita: ".$fila2["titulo"]."\nPregunta: ".$fila2["pregunta"];
                break;
            case 'r':
                $tipo = "Respuesta";
                $sql2 = "select ch.id, p.respuesta, ch.titulo from preguntas as p left join changuitas as ch on p.changuita = ch.id where p.id = ".$fila["i"];
                $res2 = $bd->query($sql2);
                $fila2 = $res2->fetch_assoc();
                $dato = "Changuita: ".$fila2["titulo"]."\nRespuesta: ".$fila2["respuesta"];
                break;
        }

        $sheet->setCellValue("A".$i, $tipo);
        $sheet->setCellValue("B".$i, $dato);
        $sheet->setCellValue("C".$i, $fila["comentario"]);
        $sheet->setCellValue("D".$i, $fila["nombre"]." ".$fila["apellido"]);
        $sheet->setCellValue("E".$i, $f->convertirMuestra($fila["fecha"], "fecha"));

        $i++;
    }
    $sheet->getStyle("A1:E1")->getFont()->setBold(true);
    $sheet->getStyle("A1:E1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
    $sheet->getStyle("A1:E$i")->getFont()->setSize(10);
    foreach(range("A", "E") as $v)
        $sheet->getColumnDimension($v)->setAutoSize(true);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $archivo = "Solochanguitas - Denuncias ".date("d-m-Y H-i-s").".xlsx";
    $objWriter->save("../xls/".$archivo);
    $data["archivo"] = $archivo;
    $data["estado"] = "ok";
}
else
    $data["estado"] = "vacio";
echo json_encode($data);
?>