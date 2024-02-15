<?php
header('Content-Type: text/html; charset=UTF-8');
require '../PHPExcel-1.8/Classes/PHPExcel.php';
require_once "../conexion.php";
require_once "../config.php";

function reporteExcel(){
	global $conexion;
    global $objPHPExcel;
	global $conexion2;
    
	$objPHPExcel = new PHPExcel();
	$objPHPExcel -> getProperties() -> setCreator("Gonzalo Puyol") ->setDescription("Archivo Etiquetas");
	
	pedido_procesado();
	
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Disposition: attachment;filename="base archivo.xlsx"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter -> save('php://output');
	
	
}


function pedido_procesado(){

	global $conexion;
    global $objPHPExcel;
	global $conexion2;
	
	//$ola  = $_GET["ola"];
	
	$estilo = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$bordes = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$negrita = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
	
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	
    /*
    $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->applyFromArray($estilo);
	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
	$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($bordes);
	*/
    
	$objPHPExcel -> getActiveSheet()-> setCellValue('A1','CANAL');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B1','DCANAL');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C1','COD TIENDA (SKU)');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D1','DESCRIP');
    $objPHPExcel -> getActiveSheet()-> setCellValue('E1','COD MONARCH');
    $objPHPExcel -> getActiveSheet()-> setCellValue('F1','COD BARRA TIENDA(13)');

    $objPHPExcel -> getActiveSheet()-> setCellValue('A2','4002');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B2','PARIS');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C2','576946002');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D2','PANTY ENERGY 70/NEGRO/T/A');
    $objPHPExcel -> getActiveSheet()-> setCellValue('E2','001089-A-NEG');
    $objPHPExcel -> getActiveSheet()-> setCellValue('F2','2057694600281');

}

reporteExcel();

?>