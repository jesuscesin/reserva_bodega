<?php
error_reporting(E_ALL);
require_once "conexion.php";
require_once "config.php";
require 'PHPExcel-1.8/Classes/PHPExcel.php';
//require_once "page.ext";

function pedido_procesado($bodega){
    global $objPHPExcel;


	$estilo = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$bordes = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$negrita = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

    $objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');
    $objPHPExcel->getActiveSheet()->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');
	
	$objPHPExcel -> getActiveSheet()-> setCellValue('A1','CODIGO');	
    $objPHPExcel -> getActiveSheet()-> setCellValue('B1','BARRA');
    $objPHPExcel -> getActiveSheet()-> setCellValue('C1','DESCRIPCION');
    $objPHPExcel -> getActiveSheet()-> setCellValue('D1','GRUPO');
    
    $fontStyleArray = array(
        'font'  => array(
            'color' => array('rgb' => 'FFFFFF'),
            'size'  => 12
    ));

    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($fontStyleArray);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($fontStyleArray);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($fontStyleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($fontStyleArray);
    

   
    $arrPopulate = populateExcel($bodega);

	$numRows = sizeof($arrPopulate);
	$x = 2;

	for ($i = 0; $i <= $numRows-1; $i++) {

		///////////////////////////////////
		//$valArticulo = trim($arrPopulate[0]["ARTICULO"]);
		//$valLinea = trim($arrPopulate[0]["DLINEA"]);
		$vCODIGO = $arrPopulate[$i]["CODIGO"];
		$vBARRA = $arrPopulate[$i]["BARRA"];
		$vDESCRIPCON = $arrPopulate[$i]["DESCRIPCION"];
		$vGRUPO = $arrPopulate[$i]["GRUPO"];

		$objPHPExcel -> getActiveSheet()-> setCellValueExplicit('A'.$x,trim($vCODIGO),PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel -> getActiveSheet()-> setCellValueExplicit('B'.$x,trim($vBARRA),PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel -> getActiveSheet()-> setCellValue('C'.$x,$vDESCRIPCON);
		$objPHPExcel -> getActiveSheet()-> setCellValue('D'.$x,$vGRUPO);
		$x++;

	}				

		

}

function populateExcel($bodega){
    global $tipobd_totvs, $conexion_totvs; 

	$querysel = "SELECT 
        B1_COD,
        B1_CODBAR,
        B1_DESC,
        B1_GRUPO
    FROM SB1010
    WHERE B1_LOCPAD LIKE '%$bodega%'
        AND B1_CODBAR NOT LIKE '%NO%'
        AND B1_CODBAR <> 'ZZZZZZZZZZZZZZZ'
        AND B1_CODBAR <> '0'
        AND B1_CODBAR <> '00'
        AND B1_CODBAR <> '000'
        AND B1_CODBAR <> '0000'
        AND B1_CODBAR <> 'LANZAMIENTO DE'
        AND B1_CODBAR <> 'SE'
        AND B1_CODBAR <> 'XX-FALTANTE-ART'
        AND B1_CODBAR <> 'XX-MERC. RECEPC'
        AND B1_CODBAR <> 'XXXXXXX'
        AND B1_CODBAR <> 'YYYYYYY'
        AND B1_CODBAR <> ' '";
    //echo $querysel;
    $rss = querys($querysel,$tipobd_totvs,$conexion_totvs);
    while($fila=ver_result($rss,$tipobd_totvs)){
        $arr[]=array(
            "CODIGO"        =>trim($fila["B1_COD"]),
            "BARRA"         =>trim($fila["B1_CODBAR"]),
            "DESCRIPCION"   =>$fila["B1_DESC"],
            "GRUPO"         =>$fila["B1_GRUPO"]
        );
    }

	return $arr;
}

function reporteExcel($bodega){
    global $objPHPExcel;
    
	$objPHPExcel = new PHPExcel();
	$objPHPExcel -> getProperties() -> setCreator("Mathias Tapia G") ->setTitle("Articulos x Bodega") ->setDescription("Planilla de Articulos por Bodega");
	
	pedido_procesado($bodega);

    $hoy = date('Ymd');
	$sysTime = date("his");

    $filename = $hoy."_".$sysTime."-BODEGA_".$bodega.".xlsx";
	
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Disposition: attachment;filename='.$filename);
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter -> save('php://output');
	
}

function verArticulos($bodega){
    global $tipobd_totvs, $conexion_totvs;
      
    $querysel = "SELECT 
        B1_COD,
        B1_CODBAR,
        B1_DESC,
        B1_GRUPO
    FROM SB1010
    WHERE B1_LOCPAD LIKE '%$bodega%'
        AND B1_CODBAR NOT LIKE '%NO%'
        AND B1_CODBAR <> 'ZZZZZZZZZZZZZZZ'
        AND B1_CODBAR <> '0'
        AND B1_CODBAR <> '00'
        AND B1_CODBAR <> '000'
        AND B1_CODBAR <> '0000'
        AND B1_CODBAR <> 'LANZAMIENTO DE'
        AND B1_CODBAR <> 'SE'
        AND B1_CODBAR <> 'XX-FALTANTE-ART'
        AND B1_CODBAR <> 'XX-MERC. RECEPC'
        AND B1_CODBAR <> 'XXXXXXX'
        AND B1_CODBAR <> 'YYYYYYY'
        AND B1_CODBAR <> ' '";
    //echo $querysel;
    $rss = querys($querysel,$tipobd_totvs,$conexion_totvs);
    while($fila=ver_result($rss,$tipobd_totvs)){
        $art[]=array(
            "CODIGO"        =>trim($fila["B1_COD"]),
            "BARRA"         =>trim($fila["B1_CODBAR"]),
            "DESCRIPCION"   =>$fila["B1_DESC"],
            "GRUPO"         =>$fila["B1_GRUPO"]
        );
    }
    //$dpto["success"]=true;
    echo json_encode($art);
}

if(isset($_GET["getPlanilla"])){
	$bodega = $_GET["bodega"];
    reporteExcel($bodega);
}

if(isset($_GET["getArticulos"])){
	$bodega = $_GET["bodega"];
    verArticulos($bodega);
}


?>