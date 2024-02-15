<?php
header('Content-Type: text/html; charset=UTF-8');
require '../PHPExcel-1.8/Classes/PHPExcel.php';
require_once "../conexion.php";
require_once "../config.php";

function reporteExcel(){
	global $tipobd_totvs,$conexion_totvs;
    global $objPHPExcel;
    
	$objPHPExcel = new PHPExcel();
	$objPHPExcel -> getProperties() -> setCreator("Gonzalo Puyol") ->setDescription("Archivo Equivalencia");
	
	pedido_procesado();
	
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Disposition: attachment;filename="Equivalencias.xlsx"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter -> save('php://output');
	
	
}


function pedido_procesado(){

	global $tipobd_totvs,$conexion_totvs;
    global $objPHPExcel;
	
	$canal  = $_GET["canal"];
	
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
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
    $objPHPExcel->getActiveSheet()->getStyle('N1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	
    /*
    $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->applyFromArray($estilo);
	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
	$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($bordes);
	*/
    
	$objPHPExcel -> getActiveSheet()-> setCellValue('A1','CLIENTE');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B1','CANAL');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C1','DCANAL');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D1','COD. MONARCH');
    $objPHPExcel -> getActiveSheet()-> setCellValue('E1','CODBAR MONARCH');
    $objPHPExcel -> getActiveSheet()-> setCellValue('F1','DESCR. MONARCH');
    $objPHPExcel -> getActiveSheet()-> setCellValue('G1','SKU CLIENTE');
    $objPHPExcel -> getActiveSheet()-> setCellValue('H1','UPC CLIENTE');
    $objPHPExcel -> getActiveSheet()-> setCellValue('I1','DESCR. CLIENTE');
    $objPHPExcel -> getActiveSheet()-> setCellValue('J1','FACTOR');
    $objPHPExcel -> getActiveSheet()-> setCellValue('K1','BODEGA');
    $objPHPExcel -> getActiveSheet()-> setCellValue('L1','UM');
    $objPHPExcel -> getActiveSheet()-> setCellValue('M1','SEGUM');
    $objPHPExcel -> getActiveSheet()-> setCellValue('N1','PRECIO LISTA');

	if($canal == 'todos'){
		$querysel = "SELECT  EQ_CLIENTE, EQ_CANAL, EQ_DCANAL, EQ_COD, NVL(EQ_BARCOD,0) AS EQ_BARCOD,
				EQ_DESC, EQ_CLICOD, EQ_CLIBAR, EQ_CLIDES, EQ_FACTOR, EQ_LOCPAD, EQ_UM,
				EQ_SEGUM, EQ_PRLIST
				FROM ".TBL_ZEQ."
				ORDER BY EQ_COD";
	}else{
		$querysel = "SELECT  EQ_CLIENTE, EQ_CANAL, EQ_DCANAL, EQ_COD, NVL(EQ_BARCOD,0) AS EQ_BARCOD,
				EQ_DESC, EQ_CLICOD, EQ_CLIBAR, EQ_CLIDES, EQ_FACTOR, EQ_LOCPAD, EQ_UM,
				EQ_SEGUM, EQ_PRLIST
				FROM ".TBL_ZEQ."
				WHERE EQ_CANAL='$canal'
				ORDER BY EQ_COD";
	}
	
	
	$rss	= querys($querysel, $tipobd_totvs, $conexion_totvs);
	//echo "query : ". $querysel;
	//die();
	$fila = 2;
	while($v = ver_result($rss, $tipobd_totvs)){
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':'.'N'.$fila)->applyFromArray($estilo);
		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$fila)->getNumberFormat()->setFormatCode('####');		
		$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->getNumberFormat()->setFormatCode('####');		
		$objPHPExcel->getActiveSheet()->getStyle('H'.$fila)->getNumberFormat()->setFormatCode('####');		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,trim($v['EQ_CLIENTE']));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,trim($v['EQ_CANAL']));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,trim($v['EQ_DCANAL']));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,trim($v['EQ_COD']));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,trim($v['EQ_BARCOD']));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,trim($v['EQ_DESC']));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,trim($v['EQ_CLICOD']));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,trim($v['EQ_CLIBAR']));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,trim($v['EQ_CLIDES']));
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,trim($v['EQ_FACTOR']));
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,trim($v['EQ_LOCPAD']));
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,trim($v['EQ_UM']));
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,trim($v['EQ_SEGUM']));
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,trim($v['EQ_PRLIST']));
		
		$fila++;		
	}

}

reporteExcel();

?>