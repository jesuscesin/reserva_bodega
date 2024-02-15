<?php
header('Content-Type: text/html; charset=UTF-8');
require '../PHPExcel-1.8/Classes/PHPExcel.php';
require_once "../conexion.php";
require_once "../config.php";

function reporteExcel(){
	global $tipobd_totvs, $conexion_totvs; 
	global $objPHPExcel;
	
	//$fecha = date('mY');
	$objPHPExcel = new PHPExcel();
	$objPHPExcel -> getProperties() -> setCreator("Gonzalo Puyol") ->setDescription("Archivo Pedido");

	//Ancho de las columnas
	
	$planilla = $_GET["planilla"];
	
	archivo_picking();
	//detalle_ola();
	
	
	
	$ano = date('Y');
	$dia = date('d');
	$mes_number = date('m');
	$mes_number = $mes_number;
	$mes= date('m');
        if ($mes=="01") $mes="ENERO";
        if ($mes=="02") $mes="FEBRERO";
        if ($mes=="03") $mes="MARZO";
        if ($mes=="04") $mes="ABRIL";
        if ($mes=="05") $mes="MAYO";
        if ($mes=="06") $mes="JUNIO";
        if ($mes=="07") $mes="JULIO";
        if ($mes=="08") $mes="AGOSTO";
        if ($mes=="09") $mes="SEPTIEMBRE";
        if ($mes=="10") $mes="OCTUBRE";
        if ($mes=="11") $mes="NOVIEMBRE";
        if ($mes=="12") $mes="DICIEMBRE";
	
	
		
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Disposition: attachment;filename="Validacion Precios MCH - Dicotex'.$planilla.'.xlsx"');
	header('Cache-Control: max-age=0');
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	//$objWriter -> save('./picking_ola/'.$ola.'-'.$dia.$mes_number.$ano.'-Archivo_pedido_'.$mes.'_'.$ano.'.xlsx');
	$objWriter -> save('php://output');
	
	
}
function busca_proveedor($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT 
				B1_PROVEED,
			   (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z0' AND X5_CHAVE=B1_PROVEED AND D_E_L_E_T_<>'*') AS DPROVEED
			FROM SB1010
			WHERE D_E_L_E_T_<>'*'
			AND B1_COD = '$articulo'
			group by B1_PROVEED
			ORDER BY B1_PROVEED";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rss, $tipobd_totvs);
	$proveedor = trim($fila['B1_PROVEED']);
	return $proveedor;
			
}
function archivo_picking(){
	
	global $tipobd_totvs,$conexion_totvs;
	global $objPHPExcel;
	
	$planilla = $_GET["planilla"];
	
	$estilo = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$bordes = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$negrita = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
	//=====================================================================================
	//=============================FALABELLA MCH==================================================
	//=====================================================================================
	
	$objPHPExcel -> setActiveSheetIndex(0);
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
	
	$objPHPExcel->getActiveSheet()->getStyle('A4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('B4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('C4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('D4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('E4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('F4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('G4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('H4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('I4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');
	$objPHPExcel->getActiveSheet()->getStyle('J4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7da6e8');

	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($estilo);
	$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
	$objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($bordes);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A3','Planilla '.$planilla);	
	$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($negrita);
	$objPHPExcel -> getActiveSheet()->setTitle("PEDIDO");
	//$objPHPExcel -> getActiveSheet()-> setCellValue('A4','PEDIDO');
	$objPHPExcel -> getActiveSheet()-> setCellValue('A4','CODIGO MONARCH');
	$objPHPExcel -> getActiveSheet()-> setCellValue('B4','DESCRIPCION');
	$objPHPExcel -> getActiveSheet()-> setCellValue('C4','CANTIDAD');
	$objPHPExcel -> getActiveSheet()-> setCellValue('D4','PRECIO MCH');
	$objPHPExcel -> getActiveSheet()-> setCellValue('E4','PRECIO DICO');
	$objPHPExcel -> getActiveSheet()-> setCellValue('F4','DIFERENCIA');
	$objPHPExcel -> getActiveSheet()-> setCellValue('G4','FACTOR');
	$objPHPExcel -> getActiveSheet()-> setCellValue('H4','ESTADO');
	$objPHPExcel -> getActiveSheet()-> setCellValue('I4','ESTADO 2');


	
	$querysel = "SELECT 
				OCD_MCODART, OCD_MDESCRI,
				 (SELECT B1_FACTOR FROM SB1010 WHERE B1_COD=OCD_MCODART AND D_E_L_E_T_<>'*') AS FACTOR ,
				 SUM(OCD_XQUANT) AS OCD_XQUANT,
				OCD_MPRUNIT, NVL(OCD_XPRUNIT,0) AS OCD_XPRUNIT, 
				NVL(OCD_MPRUNIT,0) - NVL(OCD_XPRUNIT,0)  AS DIFERENCIA
				
			FROM ZTMP_OCDIC WHERE OCD_NOMPLA = '$planilla' 
			GROUP BY OCD_MCODART, OCD_MDESCRI, OCD_MPRUNIT, OCD_XPRUNIT
			ORDER BY NLSSORT(OCD_MCODART,'NLS_SORT=BINARY_AI')";
	$rss	= querys($querysel, $tipobd_totvs, $conexion_totvs);
	// echo "query : ". $querysel;
	// die();
	$fila = 5;
	while($v = ver_result($rss, $tipobd_totvs)){
		$articulo 		= trim($v['OCD_MCODART']);
		$descripcion 	= $v['OCD_MDESCRI'];
		$factor 		= $v['FACTOR'];
		$cant_orig 		= $v['OCD_XQUANT'];
		$precio_mch 	= $v['OCD_MPRUNIT'];
		$precio_dicotex = $v['OCD_XPRUNIT'];
		$diferencia = $v['DIFERENCIA'];
		//$proveedor 		= busca_proveedor($articulo);
		//$precio_dicotex = busca_precio_dico($articulo);
		//$precio_mch 	= busca_precio_mch($articulo);
		//$conv			= busca_conv($articulo);
		//$diferencia			= ($precio_mch*$conv)-($precio_dicotex);
		
		
		//if($proveedor == '06'){	$lista_precios = '051';}else{$lista_precios= '050';}
		
	
		
		
		
		// echo "TIPO DICO ".$tipobd_totvs."<br>";
		// echo "CONEXION DICO ".$conexion_totvs."<br>";
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->getNumberFormat()->setFormatCode('####');
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':'.'J'.$fila)->applyFromArray($estilo);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':'.'J'.$fila)->applyFromArray($negrita);
		//$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,trim($v['PTE_PEDIDO']));
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$articulo);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,utf8_encode(trim($descripcion))); 
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$cant_orig);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$precio_mch);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$precio_dicotex);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$diferencia);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$factor);
		if($precio_mch < 1 or $precio_dicotex < 1){
			
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,'SIN PRECIO');
			$objPHPExcel->getActiveSheet()->getStyle('H'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FF0A0A');
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,'OK');
			
		}
		if($diferencia > 0 or $diferencia < 0){	
		
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,'DIFERENCIA DE PRECIO');
			$objPHPExcel->getActiveSheet()->getStyle('I'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFCD33');
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,'OK');
		}
		
		//$objPHPExcel->getActiveSheet()->getStyle('E'.$fila)->applyFromArray($negrita);
		
		
		$fila++;		
	}
	//$objPHPExcel->getActiveSheet()->getStyle('C'.$fila.':'.'C'.$fila)->applyFromArray($bordes);	
	//$objPHPExcel->getActiveSheet()->getStyle('C'.$fila.':'.'C'.$fila)->applyFromArray($estilo);
	//$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,'=SUM(C5'.':C'.($fila-1).')');//multiplicacion
	//
	//$objPHPExcel->getActiveSheet()->getStyle('D'.$fila.':'.'D'.$fila)->applyFromArray($bordes);	
	//$objPHPExcel->getActiveSheet()->getStyle('D'.$fila.':'.'D'.$fila)->applyFromArray($estilo);
	//$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,'=SUM(D5'.':D'.($fila-1).')');//multiplicacion
	
	
	
	
}
function busca_precio_dico($articulo){
	global $tipobd_totvs_dico,$conexion_totvs_dico;
	
	$query_dico = "SELECT NVL(MAX(AIB_PRCCOM),0) AS AIB_PRCCOM FROM AIB300 WHERE trim(AIB_CODPRO)='$articulo' AND AIB_CODTAB='001'";
	$rsp_dico = querys($query_dico, $tipobd_totvs_dico, $conexion_totvs_dico);
	$v1 = ver_result($rsp_dico, $tipobd_totvs_dico);
	
	$precio_dico = $v1["AIB_PRCCOM"];
	return $precio_dico;
}
function busca_precio_mch($articulo){
	global  $tipobd_totvs,$conexion_totvs;
	
		$queryp = "SELECT NVL(MAX(DA1_PRCVEN),0) AS DA1_PRCVEN FROM DA1010 WHERE trim(DA1_CODPRO)='$articulo' AND DA1_CODTAB='050'";
		$rsp = querys($queryp, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rsp, $tipobd_totvs);
		$precio = $v1["DA1_PRCVEN"];	
	return $precio;
}

function busca_conv($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$querysel = "SELECT B1_CONV FROM SB1010 WHERE B1_COD='$articulo'";
	// echo $querysel;
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$conv = $v["B1_CONV"];
	
	return $conv;
	
}














//==============================================================================
//==============================================================================
//==============================================================================
reporteExcel();
?>