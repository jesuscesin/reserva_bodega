<?php
header('Content-Type: text/html; charset=UTF-8');
require 'PHPExcel-1.8/Classes/PHPExcel.php';
require_once "conexion.php";
require_once "config.php";

function reporteExcel(){
	global $conexion;
    global $objPHPExcel;
	global $conexion2;
    
	$objPHPExcel = new PHPExcel();
	$objPHPExcel -> getProperties() -> setCreator("Mathias Tapia G") ->setTitle("Homologar Base") ->setDescription("Planilla Homologacion Base");
	
	pedido_procesado();
	
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Disposition: attachment;filename="homologate_base.xlsx"');
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
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
    $objPHPExcel->getActiveSheet()->getStyle('J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	
    /*
    $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->applyFromArray($estilo);
	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
	$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($bordes);
	*/
    
	$objPHPExcel -> getActiveSheet()-> setCellValue('A1','GT');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B1','ANIO');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C1','MES');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D1','DEPARTAMENTO');
    $objPHPExcel -> getActiveSheet()-> setCellValue('E1','SEMANA_COMERCIAL');
    $objPHPExcel -> getActiveSheet()-> setCellValue('F1','SKU');
    $objPHPExcel -> getActiveSheet()-> setCellValue('G1','LOCAL');
    $objPHPExcel -> getActiveSheet()-> setCellValue('H1','VENTA_UNIDADES');
    $objPHPExcel -> getActiveSheet()-> setCellValue('I1','VENTA_PESOS');
    $objPHPExcel -> getActiveSheet()-> setCellValue('J1','STOCK');

    $objPHPExcel -> getActiveSheet()-> setCellValue('A2','FALABELLA');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B2','2023');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C2','07');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D2','J06');
    $objPHPExcel -> getActiveSheet()-> setCellValue('E2','07.1');
    $objPHPExcel -> getActiveSheet()-> setCellValue('F2','16720512');
    $objPHPExcel -> getActiveSheet()-> setCellValue('G2','LA CALERA');
    $objPHPExcel -> getActiveSheet()-> setCellValue('H2','0');
    $objPHPExcel -> getActiveSheet()-> setCellValue('I2','5'); // SETEADO EN 0
    $objPHPExcel -> getActiveSheet()-> setCellValue('J2','6');					
    
    //////////////////PRUEBAS SUM///////////////////////////
    /*

    $objPHPExcel -> getActiveSheet()-> setCellValue('A3','FALABELLA');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B3','2023');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C3','MAYO');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D3','J06');
    $objPHPExcel -> getActiveSheet()-> setCellValue('E3','2023053');
    $objPHPExcel -> getActiveSheet()-> setCellValue('F3','16720512');
    $objPHPExcel -> getActiveSheet()-> setCellValue('G3','LA CALERA');
    $objPHPExcel -> getActiveSheet()-> setCellValue('H3','0');
    $objPHPExcel -> getActiveSheet()-> setCellValue('I3','5');
    $objPHPExcel -> getActiveSheet()-> setCellValue('J3','6');		

    $objPHPExcel -> getActiveSheet()-> setCellValue('A4','FALABELLA');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B4','2023');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C4','MAYO');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D4','J06');
    $objPHPExcel -> getActiveSheet()-> setCellValue('E4','2023053');
    $objPHPExcel -> getActiveSheet()-> setCellValue('F4','16720512');
    $objPHPExcel -> getActiveSheet()-> setCellValue('G4','LA CALERA');
    $objPHPExcel -> getActiveSheet()-> setCellValue('H4','0');
    $objPHPExcel -> getActiveSheet()-> setCellValue('I4','5');
    $objPHPExcel -> getActiveSheet()-> setCellValue('J4','6');		
    

    ///ESTO FUNCIONA {
        $rangeSumI = PHPExcel_Calculation_MathTrig::SUM($objPHPExcel->getActiveSheet()->rangeToArray('I2:I4'));
        $rangeSumJ = PHPExcel_Calculation_MathTrig::SUM($objPHPExcel->getActiveSheet()->rangeToArray('J2:J4'));  
        $rangeSum = $rangeSumI + $rangeSumJ;
    }
    $objPHPExcel->getActiveSheet()->getStyle('K5')->applyFromArray($estilo);
    $objPHPExcel->getActiveSheet()->setCellValue('K5',$rangeSum);//multiplicacion	

    */
}

reporteExcel();

?>