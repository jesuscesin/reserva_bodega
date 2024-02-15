<?php
session_start();
#require_once "PHPExcel/Classes/PHPExcel/IOFactory.php"; "PHPExcel-1.8/"
require_once "conexion.php";
require_once "config.php";
require 'PHPExcel-1.8/Classes/PHPExcel.php';
/*
SE UTILIZA LA SIGUIENTE CONEXION APUNTANDO A TOTVS 12 (ORACLE 19){
	//global $tipobd_totvs, $conexion_totvs 
}
*/




//global $tipobd_mysql_gm, $conexion_mysql_gm

///////////////////////////INSERTAR FORM//////////////////

function insertHomologa($gt, $anio, $mes, $depto, $semana_comercial, $sku, $local, $ventaUD, $ventaPesos, $stock, $codmch, $articulo, $linea, $sublinea, $marca, $categoria, $subcategoria, $mundo, $largo, $material, $temporada, $nomarchivo, $fechasubida, $horasubida, $responsable, $rows, $delet, $recno){
	global $tipobd_totvs, $conexion_totvs;


	$queryInsert = "INSERT INTO TOTVS.VENTAS_B2B(GT, ANIO, MES, DEPARTAMENTO, SEMANA_COMERCIAL, SKU, LOCAL, VENTA_UNIDADES, VENTA_PESOS, STOCK, COD_MCH, ARTICULO, LINEA, SUBLINEA, MARCA, CATEGORIA, SUBCATEGORIA, MUNDO, LARGO, MATERIAL, TEMPORADA, NOM_ARCHIVO, FECHA_SUBIDA, HORA_SUBIDA, RESPONSABLE, R_O_W_S_, D_E_L_E_T_, R_E_C_N_O_) 
    	VALUES('$gt', '$anio', '$mes', '$depto', '$semana_comercial', '$sku', '$local', $ventaUD, $ventaPesos, $stock, '$codmch', '$articulo', '$linea', '$sublinea', '$marca', '$categoria', '$subcategoria', '$mundo', '$largo', '$material', '$temporada', '$nomarchivo', '$fechasubida', '$horasubida', '$responsable', $rows, '$delet', $recno)";


	//echo "QUERY_INSERT: ".$queryInsert;
	//echo "<br>";
	//echo "------------ SALTO DE LINEA -----------------";

	$rss = querys($queryInsert,$tipobd_totvs,$conexion_totvs);

	
}

function borrarPlanilla($archivo){
	global $tipobd_totvs, $conexion_totvs;

	$query = "UPDATE TOTVS.VENTAS_B2B SET d_e_l_e_t_ = '*' WHERE nom_archivo = '$archivo'";
	$rss = querys($query,$tipobd_totvs,$conexion_totvs);

	
	echo "Archivo <strong>$archivo</strong> borrado con exito!!";

	cierra_conexion($tipobd_totvs, $conexion_totvs);
}

if(isset($_GET["borrarPlanilla"])){
	$archivo = $_GET["archivo"];
    borrarPlanilla($archivo);
}


/*
function descargarPlanilla(){
	// Remote download URL
	$remoteURL = 'http://192.168.100.71/b2b_homologate/archivos_subidos/homologacion_planillas/20230525_021905-homologate_final.xlsx';

	// Force download
	header("Content-type: application/x-file-to-save"); 
	header("Content-Disposition: attachment; filename=".basename($remoteURL));
	ob_end_clean();
	readfile($remoteURL);
}

//DESCARGA PLANILLA

if(isset($_GET["descargarPlanilla"])){

	$archivo = $_GET["archivo"];
	// Remote download URL
	$remoteURL = 'http://192.168.100.71/b2b_homologate/archivos_subidos/homologacion_planillas/20230525_021905-homologate_final.xlsx';

	// Force download
	header("Content-type: application/x-file-to-save"); 
	header("Content-Disposition: attachment; filename=".basename($remoteURL));
	ob_end_clean();
	readfile($remoteURL);

}

*/

/*

if(isset($_POST["insertRequest"])){
	$pCliente = "895416002"; //DICOTEX
	$pCanal = "4801"; //DICOTEX
	$pCodart = $_POST["artcode"];	
	$pBarcod = $_POST["barcode"];	
	$pDesc = $_POST["desc"];
	$pInerpack = $_POST["inerpack"];
	$pVigencia = $_POST["vigencia"];
	$pDelet = ' ';	
	$pRecno = recno_tabla();
	$pPlu = 0;
	$pFeccarga	= date('Ymd');
	insertForm($pCliente, $pCanal, $pCodart, $pBarcod, $pDesc, $pInerpack, $pVigencia, $pDelet, $pRecno, $pPlu, $pFeccarga);
}


//LLENA TABLA DE ACUERDO A LAS COINCIDENCIAS DEL SUBSTR DE CODIGO ARTICULO
function get_artdata($artcode){
	global $tipobd_ptl,$conexion_ptl;

	//$sstrArtcode = substr("$artcode", 0, 6);
	$sstrArtcode = $artcode;
	$queryget = "SELECT ptc_codart, ptc_barcod, ptc_descripcion, ptc_inerpack, CASE ptc_vigencia WHEN 'S' THEN 'Vigente' WHEN 'N' THEN 'No Vigente' END AS ptc_vigencia, ";
	$queryget = $queryget.'COUNT(*) AS "FILA" FROM ptl_conversion ';
	$queryget = $queryget."WHERE ptc_codart LIKE '%$sstrArtcode%' AND ptc_barcod IS NOT NULL AND ptc_canal = '4801' GROUP BY ptc_codart, ptc_barcod, ptc_descripcion, ptc_inerpack, ptc_vigencia";

	$rss = querys($queryget,$tipobd_ptl,$conexion_ptl);
	while($v = ver_result($rss, $tipobd_ptl)){
		$datos[]=array(
					"PTC_CODART" 	=> $v["PTC_CODART"],
					"PTC_BARCOD"   		=> $v["PTC_BARCOD"], 
					"PTC_DESCRIPCION" 	=> $v["PTC_DESCRIPCION"], 
					"PTC_INERPACK" 		=> $v["PTC_INERPACK"],
					"PTC_VIGENCIA" 		=> $v["PTC_VIGENCIA"],
					"PTC_FILA" 		=> $v["FILA"]
				);
	}
	echo json_encode($datos);
}

if(isset($_GET["get_data"])){	
	$artcode = $_GET["get_data"];
	get_artdata($artcode);
}

/////////////////////////////////////////////////////////////////


//LENA INPUTS EN BASE A UN ID DE ARTICULO
function fill_inputs($artcode_id){
	global $tipobd_ptl,$conexion_ptl;
		
	$queryfill = "SELECT ptc_codart, ptc_barcod, ptc_descripcion, ptc_inerpack, CASE ptc_vigencia WHEN 'S' THEN 'Vigente' WHEN 'N' THEN 'No Vigente' END AS ptc_vigencia, r_e_c_n_o_, ";
	$queryfill = $queryfill.'COUNT(*) AS "FILA" FROM ptl_conversion ';
	$queryfill = $queryfill."WHERE ptc_codart = '$artcode_id' AND ptc_barcod IS NOT NULL AND ptc_canal = '4801' GROUP BY ptc_codart, ptc_barcod, ptc_descripcion, ptc_inerpack, ptc_vigencia, r_e_c_n_o_";

	$rss = querys($queryfill,$tipobd_ptl,$conexion_ptl);
	while($v = ver_result($rss, $tipobd_ptl)){
		$datos[]=array(
					"PTC_CODART" 	=> $v["PTC_CODART"],
					"PTC_BARCOD"   		=> $v["PTC_BARCOD"], 
					"PTC_DESCRIPCION" 	=> $v["PTC_DESCRIPCION"], 
					"PTC_INERPACK" 		=> $v["PTC_INERPACK"],
					"PTC_VIGENCIA" 		=> $v["PTC_VIGENCIA"],
					"PTC_FILA" 		=> $v["FILA"],
					"R_E_C_N_O_" 		=> $v["R_E_C_N_O_"],

				);
	}
	echo json_encode($datos);
}

if(isset($_GET["set_data"])){
	$artcode = $_GET["set_data"];
	fill_inputs($artcode);
}



function datos_subidos(){
	global $tipobd_ptl,$conexion_ptl;
	$querysel = "SELECT ptc_codart, ptc_barcod, ptc_descripcion, ptc_inerpack, CASE ptc_vigencia WHEN 'S' THEN 'Vigente' WHEN 'N' THEN 'No Vigente' END AS ptc_vigencia FROM ptl_conversion WHERE ptc_barcod IS NOT NULL AND ptc_canal = '4801' FETCH NEXT 1600 ROWS ONLY";

	$rss = querys($querysel,$tipobd_ptl,$conexion_ptl);
	while($v = ver_result($rss, $tipobd_ptl)){
		$datos[]=array(
					"PTC_CODART" 	=> $v["PTC_CODART"],
					"PTC_BARCOD"   		=> $v["PTC_BARCOD"], 
					"PTC_DESCRIPCION" 	=> $v["PTC_DESCRIPCION"], 
					"PTC_INERPACK" 		=> $v["PTC_INERPACK"],
					"PTC_VIGENCIA" 		=> $v["PTC_VIGENCIA"]
				);
	}
	echo json_encode($datos);
}


if(isset($_GET["read_data"])){
    datos_subidos();
}

function recno_tabla(){
	global $tipobd_ptl,$conexion_ptl;
    
    $querysel = "SELECT max(R_E_C_N_O_) as RECNO FROM ptl_conversion";
    $rss = querys($querysel, $tipobd_ptl, $conexion_ptl);
    $fila = ver_result($rss, $tipobd_ptl);
    if($fila["RECNO"]==null or $fila["RECNO"]==0){
        return 1;
    }else{
        return $fila["RECNO"]+1;
    }
}

*/


///////////////////////////////////GENERA ARCHIVO POBLADO////////////////////////////////////////////////////////////


function pedido_procesado($nom_archivo){
    global $objPHPExcel;


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
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);


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
	$objPHPExcel->getActiveSheet()->getStyle('K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('N1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('O1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('P1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('Q1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('R1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('S1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('T1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	$objPHPExcel->getActiveSheet()->getStyle('U1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9c6c3');
	
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
	$objPHPExcel -> getActiveSheet()-> setCellValue('K1','COD_MCH');
	$objPHPExcel -> getActiveSheet()-> setCellValue('L1','ARTICULO');
	$objPHPExcel -> getActiveSheet()-> setCellValue('M1','LINEA');
	$objPHPExcel -> getActiveSheet()-> setCellValue('N1','SUBLINEA');
	$objPHPExcel -> getActiveSheet()-> setCellValue('O1','MARCA');
	$objPHPExcel -> getActiveSheet()-> setCellValue('P1','CATEGORIA');
	$objPHPExcel -> getActiveSheet()-> setCellValue('Q1','SUBCATEGORIA');
	$objPHPExcel -> getActiveSheet()-> setCellValue('R1','MUNDO');
	$objPHPExcel -> getActiveSheet()-> setCellValue('S1','LARGO');
	$objPHPExcel -> getActiveSheet()-> setCellValue('T1','MATERIAL');
	$objPHPExcel -> getActiveSheet()-> setCellValue('U1','TEMPORADA');

	$arrPopulate = populate_excel($nom_archivo);

	$numRows = sizeof($arrPopulate);
	$x = 2;

	for ($i = 0; $i <= $numRows-1; $i++) {

		///////////////////////////////////
		//$valArticulo = trim($arrPopulate[0]["ARTICULO"]);
		//$valLinea = trim($arrPopulate[0]["DLINEA"]);
		$vGT = $arrPopulate[$i]["GT"];
		$vAnio = $arrPopulate[$i]["ANIO"];
		$vMes = $arrPopulate[$i]["MES"];
		$vDepto = $arrPopulate[$i]["DEPARTAMENTO"];
		$vComercialWeek = $arrPopulate[$i]["SEMANA_COMERCIAL"];
		$vSKU = $arrPopulate[$i]["SKU"];
		$vLocal = $arrPopulate[$i]["LOCAL"];
		$vVentaUD = $arrPopulate[$i]["VENTA_UNIDADES"];
		$vVentaPesos = $arrPopulate[$i]["VENTA_PESOS"];
		$vStock = $arrPopulate[$i]["STOCK"];
		$vCodMCH = $arrPopulate[$i]["COD_MCH"];

		$vArticulo = $arrPopulate[$i]["ARTICULO"]; // THIS

		$vLinea = $arrPopulate[$i]["LINEA"]; // THIS
		$vSublinea = $arrPopulate[$i]["SUBLINEA"];
		$vMarca = $arrPopulate[$i]["MARCA"]; // THIS
		$vCategoria = $arrPopulate[$i]["CATEGORIA"]; // THIS
		$vSubcategoria = $arrPopulate[$i]["SUBCATEGORIA"]; // THIS
		$vMundo = $arrPopulate[$i]["MUNDO"];
		$vLargo = $arrPopulate[$i]["LARGO"];
		$vMaterial = $arrPopulate[$i]["MATERIAL"]; // THIS
		$vTemporada = $arrPopulate[$i]["TEMPORADA"]; // THIS

		$objPHPExcel -> getActiveSheet()-> setCellValue('A'.$x,$vGT);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('B'.$x,$vAnio);
		$objPHPExcel -> getActiveSheet()-> setCellValue('C'.$x,$vMes);
		$objPHPExcel -> getActiveSheet()-> setCellValue('D'.$x,$vDepto);
		$objPHPExcel -> getActiveSheet()-> setCellValue('E'.$x,$vComercialWeek);
		$objPHPExcel -> getActiveSheet()-> setCellValue('F'.$x,$vSKU);
		$objPHPExcel -> getActiveSheet()-> setCellValue('G'.$x,$vLocal);
		$objPHPExcel -> getActiveSheet()-> setCellValue('H'.$x,$vVentaUD);
		$objPHPExcel -> getActiveSheet()-> setCellValue('I'.$x,$vVentaPesos);
		$objPHPExcel -> getActiveSheet()-> setCellValue('J'.$x,$vStock);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('K'.$x,$vCodMCH);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('L'.$x,$vArticulo);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('M'.$x,$vLinea);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('N'.$x,$vSublinea);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('O'.$x,$vMarca);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('P'.$x,$vCategoria);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('Q'.$x,$vSubcategoria);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('R'.$x,$vMundo);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('S'.$x,$vLargo);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('T'.$x,$vMaterial);	
		$objPHPExcel -> getActiveSheet()-> setCellValue('U'.$x,$vTemporada);
		$x++;

	}				

}


function populate_excel($nom_archivo){
    global $tipobd_totvs, $conexion_totvs; 

	$querySel = "SELECT 
		GT, ANIO, MES, DEPARTAMENTO, SEMANA_COMERCIAL,
		SKU, LOCAL, VENTA_UNIDADES, VENTA_PESOS, STOCK,
		NVL(COD_MCH, 0) AS COD_MCH, NVL(ARTICULO, 0) AS ARTICULO, NVL(LINEA, 0) AS LINEA,
		NVL(SUBLINEA, 0) AS SUBLINEA, NVL(MARCA, 0) AS MARCA, NVL(CATEGORIA, 0) AS CATEGORIA,
		NVL(SUBCATEGORIA, 0) AS SUBCATEGORIA, NVL(MUNDO, 0) AS MUNDO, NVL(LARGO, 0) AS LARGO,
		NVL(MATERIAL, 0) AS MATERIAL, NVL(TEMPORADA, 0) AS TEMPORADA
	FROM TOTVS.VENTAS_B2B WHERE NOM_ARCHIVO = '$nom_archivo'";
	$rss = querys($querySel, $tipobd_totvs, $conexion_totvs);

	while($v = ver_result($rss, $tipobd_totvs)){
		$venta[]= array(
			"GT"				=>$v["GT"],
			"ANIO"				=>$v["ANIO"],
			"MES"				=>$v["MES"],
			"DEPARTAMENTO"		=>$v["DEPARTAMENTO"],
			"SEMANA_COMERCIAL"	=>$v["SEMANA_COMERCIAL"],
			"SKU"				=>$v["SKU"],
			"LOCAL"				=>$v["LOCAL"],
		    "VENTA_UNIDADES"	=>$v["VENTA_UNIDADES"],
		    "VENTA_PESOS"		=>$v["VENTA_PESOS"],
			"STOCK"				=>$v["STOCK"],
			"COD_MCH"			=>$v["COD_MCH"],
			"ARTICULO"			=>$v["ARTICULO"],
			"LINEA"				=>$v["LINEA"],
			"SUBLINEA"			=>$v["SUBLINEA"],
			"MARCA"				=>$v["MARCA"],
			"CATEGORIA"			=>$v["CATEGORIA"],
			"SUBCATEGORIA"		=>$v["SUBCATEGORIA"],
			"MUNDO"				=>$v["MUNDO"],
			"LARGO"				=>$v["LARGO"],
			"MATERIAL"			=>$v["MATERIAL"],
			"TEMPORADA"			=>$v["TEMPORADA"]
			///SE PUEDEN INCLUIR COLUMNAS METADATA
		);
	}

	return $venta;

}

function reporteExcel($archivo){
    global $objPHPExcel;
    
	$objPHPExcel = new PHPExcel();
	$objPHPExcel -> getProperties() -> setCreator("Mathias Tapia G") ->setTitle("Ventas B2B") ->setDescription("Planilla Ventas B2B");
	
	pedido_procesado($archivo);
	
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Disposition: attachment;filename='.$archivo);
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter -> save('php://output');
	
}

if(isset($_GET["getPlanilla"])){
	$archivo = $_GET["archivo"];
	reporteExcel($archivo);

}

/////ARRIBA GENERA ARCHIVO FINAL//////////////////////////////////////////

///////////////////************POR HACER******************////////////////////////////////
///////////////////////////SUBIDA DE ARCHIVOS INPUT TYPE=FILE////////////////////////////
function subirArchivo(){

	$hoy = date('Ymd');
	$sysTime = date("his");

	$dir_subida = '/var/www/html/grupomonarch/portal/archivos_subidos/homologacion_planillas/';
	$fichero_subido = $dir_subida.$hoy."_".$sysTime."-".basename($_FILES['inputFileCargaMasiva']['name']);
	$tmpNombre = $_FILES['inputFileCargaMasiva']['tmp_name'];	
	//echo '<pre>';
	if (move_uploaded_file($_FILES['inputFileCargaMasiva']['tmp_name'], $fichero_subido)) {
		
		$error = $_FILES['inputFileCargaMasiva']['error'];		 
		$type  = $_FILES['inputFileCargaMasiva']['type'];

		if($error == 1){
			echo "TAMAÑO ARCHIVO EXCEDE MAXIMO PERMITIDO";
		}else{
			echo "El fichero es valido y subido con Exito !\n<br>";
			echo "Tipo Archivo : ".$type."<br>";
		}	

	}

}

function leer_archivo($archivo){
    global $tipobd_totvs, $conexion_totvs; 

	$hoy = date('Ymd');
	$sysTime = date("his");

	//homologate_base-20230525_101200


	$path="/var/www/html/grupomonarch/portal/archivos_subidos/homologacion_planillas/";
    $nombreArchivo=$path.$archivo;
	// Cargo la hoja de cÃ¡lculo
	$objPHPExcel = PHPExcel_IOFactory::load($nombreArchivo);
	
	//Asigno la hoja de calculo activa
	$objPHPExcel->setActiveSheetIndex(0);
	//Obtengo el numero de filas del archivo
	$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	echo "NUMROW".$numRows;
	
	//die();
	
	//$hoy = '20201231';
	//$querydel = "DELETE FROM Z2B_E_MERCADOLIBRE";
	//$rsd = db_exec($conexion2,$querydel);
	for ($i = 2; $i <= $numRows; $i++) {

		$colGT = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$colAnio = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		
		$colMes = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(); //SE CAPTURA EL MES DESDE LA SEMANA COMERCIAL CON SUBSTR

		$colDepto = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();

		$colSemanaCom = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(); 

		$cellMes = substr($colSemanaCom, 0, 2);


		$colSKU = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue(); 
		$colLocal = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue(); 
		$colVentaUd = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue(); 
		$colVentaPesos = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue(); 
		$colStock = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue(); 
			
		$tGT = trim(utf8_decode($colGT));
		$tAnio = trim($colAnio);
		$tMes = trim($cellMes);
		$tDepto = trim($colDepto);
		$tSemanaCom = trim(utf8_decode($colSemanaCom));
		$tSKU = trim($colSKU);
		$tLocal = trim($colLocal);
		$tVentaUd = trim($colVentaUd);
		
		$tVentaPesos = trim($colVentaPesos);
		$tVentaPesos = round($tVentaPesos, 0, PHP_ROUND_HALF_UP);
		
		$tStock = trim($colStock);

		if ($tStock == ' ' || $tStock == '' || $tStock == null){
			$tStock = 0;
		}
		

		$codmch = busca_cod_mch($tSKU);

		///////////////////////////////////
		$arrHomologa = busca_homologa($codmch);

		$valArticulo = trim($arrHomologa[0]["ARTICULO"]);
		$valLinea = trim($arrHomologa[0]["DLINEA"]);

		



		$valMarca = trim($arrHomologa[0]["DMARCA"]);

		if ($valMarca == 'HEAT HOLDERS'){
			$arrMetadata = busca_metadata($valArticulo, $codmch);
		}else{
			$subCodmch_00 = substr($codmch, 0, 2);
			if ($subCodmch_00 == '00'){
				$subCodmch = substr($codmch, 2, 4);
			}else{
				$subCodmch_0 = substr($codmch, 0, 1);
				if ($subCodmch_0 == '0'){
					$subCodmch = substr($codmch, 1, 5);
				}else{
					$subCodmch = substr($codmch, 0, 6);
				}	
			}							
			$arrMetadata = busca_metadata($valArticulo, $subCodmch);
		}


		$valCategoria = trim($arrHomologa[0]["DCATEGORIA"]);
		$valSubcategoria = '';
		$valGrupo = trim($arrHomologa[0]["SUBCATEGORIA"]);		


		switch ($valGrupo){
			case '1010':
				$valSubcategoria = 'CALCETINES';
				break;
			case '1030':
				$valSubcategoria = 'CALZA Y BUCANERA GRUESA';
				break;
			case '1070':
				$valSubcategoria = 'ACCESORIOS CALCETINES';
				break;
			case '2010':
				$valSubcategoria = 'PANTYHOSE';
				break;
			case '2020':
				$valSubcategoria = 'BALLERINA TRAMA';
				break;
			case '2030':
				$valSubcategoria = 'MEDIAS Y MINIMEDIAS';
				break;
			case '2040':
				$valSubcategoria = 'CALZA Y BUCANERA';
				break;
			case '2050':
				$valSubcategoria = 'CHEMISETTE';
				break;
			case '2055':
				$valSubcategoria = 'POLERA';
				break;
			case '2060':
				$valSubcategoria = 'ROPA INTERIOR';
				break;
			case '2070':
				$valSubcategoria = 'ACCESORIOS TRAMA';
				break;
			case '3010':
				$valSubcategoria = 'BALLERINA ALGODON';
				break;
			case '3020':
				$valSubcategoria = 'BALLERINAS LANA';
				break;
			case '5100':
				$valSubcategoria = 'BODY BEBE';
				break;
			case '5200':
				$valSubcategoria = 'CONJUNTO BEBE';
				break;
			case '5300':
				$valSubcategoria = 'PIJAMAS';
				break;
			case '6010':
				$valSubcategoria = 'SLIP';
				break;
			case '6020':
				$valSubcategoria = 'BOXER';
				break;
			case '6030':
				$valSubcategoria = 'CALZONCILLO';
				break;
			case '6040':
				$valSubcategoria = 'CAMISETA';
				break;
			case '6050':
				$valSubcategoria = 'COLALESS';
				break;
			case '6060':
				$valSubcategoria = 'CUADROS';
				break;
			case '6070':
				$valSubcategoria = 'HOTS PANTS';
				break;
			case '6080':
				$valSubcategoria = 'PETOS Y SOSTENES';
				break;
			case '7010':
				$valSubcategoria = 'PIJAMAS LARGOS'; //DUPLICADO
				break;
			case '7020':
				$valSubcategoria = 'PIJAMAS LARGOS'; //DUPLICADO
				break;
			case '7030':
				$valSubcategoria = 'CAMISOLAS';
				break;
			case '9010':
				$valSubcategoria = 'OVILLOS';
				break;
			default:
				echo "ERROR CON B1_GRUPO";
				break;
					
		}

		$valSublinea = trim($arrMetadata[0]["SUBLINEA"]);
		if ($valSublinea == null || $valSublinea == '' || $valSublinea == ' '){
			$valSublinea = 'TBD';
		}else{
			$valSublinea = trim($arrMetadata[0]["SUBLINEA"]);
		}

		$valMundo = trim($arrMetadata[0]["MUNDO"]);
		if ($valMundo == null || $valMundo == '' || $valMundo == ' '){
			$valMundo = 'TBD';
		}else{
			$valMundo = trim($arrMetadata[0]["MUNDO"]);
		}

		$valLargo = trim($arrMetadata[0]["LARGO"]);
		if ($valLargo == null || $valLargo == '' || $valLargo == ' '){
			$valLargo = 'TBD';
		}else{
			$valLargo = trim($arrMetadata[0]["LARGO"]);
		}

		$valMaterial = trim($arrHomologa[0]["DMATERIAL"]);
		$valTemporada = trim($arrHomologa[0]["DTEMPORADA"]);


		$valNomarchivo = $archivo;
		$valFechaSubida = $hoy;
		$valHoraSubida = $sysTime;
		$valRows = $numRows-1;
		
		$user = $_SESSION['user'];

		//print($user);

		$valDelet = ' ';
		$valRecno = recno_tabla();

		insertHomologa($tGT, $tAnio, $tMes,	$tDepto, $tSemanaCom,
			$tSKU, $tLocal,	$tVentaUd,	$tVentaPesos, $tStock,
			$codmch, $valArticulo, $valLinea,
			$valSublinea, $valMarca, $valCategoria, $valSubcategoria,
			$valMundo, $valLargo, $valMaterial, $valTemporada,
			$valNomarchivo, $valFechaSubida, $valHoraSubida, $user, $valRows, $valDelet, $valRecno);

	
		//UTLIZAR Y VALIDAR existe_sku() para tabla ptl_conversion; Es necesaria?
	/*
		if(existe_sku($tCodart)){
			updateForm($tCodart, $tBarcod, $tDescripcion, $tInerpack, $vFeccarga);
		}else{
			$vRecnoInsert = recno_tabla();
			insertHomologa($tCliente, $tCanal, $tCodart, $tBarcod, $tDescripcion, $tInerpack, $vVigencia, $vDelet, $vRecnoInsert, $vPlu, $vFeccarga);				 
		}
	*/

	}
	cierra_conexion($tipobd_totvs, $conexion_totvs);
}

/*
	CREAR FUNCION
	function equivalencia_sku(){
		$gt = planilla.$GT
		
		switch ($gt){
			case 'FALABELLA':
				$query = SELECT * FROM B2B_FALABELLA;
				break;
			case 'RIPLEY':
				$query = SELECT * FROM B2B_RIPLEY;
				break;
			case 'PARIS':
				$query = SELECT * FROM B2B_PARIS;
				break;
			default:
				break;
		}

		return $query;

	}


*/

function busca_cod_mch($sku){

	global $tipobd_ptl,$conexion_ptl;

	$querysel = "SELECT TRIM(equ_intcod) AS EQU_INTCOD FROM PTL_EQUICOD WHERE equ_clicod = '$sku' AND ROWNUM = 1";
	//echo $querycount."<br>";
	$rss= querys($querysel,$tipobd_ptl,$conexion_ptl);
	$codmch = ver_result($rss, $tipobd_ptl);


	echo $codmch["EQU_INTCOD"]; echo "<br>";

	if($codmch["EQU_INTCOD"]!=null or $codmch["EQU_INTCOD"]!=0){
        return $codmch["EQU_INTCOD"];
	}else{
		return 1;
	}

	

}




function busca_homologa($b1cod){ //INCORPORAR LOS NVL 
	
	global $tipobd_totvs,$conexion_totvs;

	$querysel = "SELECT 	
		NVL(B1_COD,0) AS COD_MCH,
		NVL(SUBSTR(B1_COD, 0, 6),0) AS ARTICULO,
		NVL(B1_LINEA,0) AS LINEA,
		NVL((SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z2' AND X5_CHAVE=B1_LINEA AND D_E_L_E_T_<>'*'),0) AS DLINEA,
		NVL(B1_MARCA,0) AS MARCA,
		NVL((SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z1' AND X5_CHAVE=B1_MARCA AND D_E_L_E_T_<>'*'),0) AS DMARCA,
		NVL(B1_GFAMILI,0) AS CATEGORIA, 
		NVL(B1_DGFAMIL,0) AS DCATEGORIA,
		NVL(B1_GRUPO,0) AS SUBCATEGORIA,
		NVL(B1_COMPOSI,0) AS MATERIAL,
		NVL((SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA = 'Z3' AND X5_CHAVE=B1_COMPOSI AND D_E_L_E_T_<>'*'),0) AS DMATERIAL,
		NVL(B1_TEMPORA,0) AS TEMPORADA,
		NVL((SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z4' AND X5_CHAVE=B1_TEMPORA AND D_E_L_E_T_<>'*'),0) AS DTEMPORADA
	FROM SB1010
	WHERE D_E_L_E_T_<>'*' AND B1_COD = '$b1cod' AND SUBSTR(B1_GFAMILI,0,1) <> 'Z'
	GROUP BY  B1_COD, B1_LINEA, B1_MARCA, B1_GFAMILI, B1_DGFAMIL, B1_GRUPO, B1_COMPOSI, B1_TEMPORA
	ORDER BY B1_LINEA";


	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cli[]= array(
			"COD_MCH"		=>$v["COD_MCH"],
			"ARTICULO"		=>$v["ARTICULO"],
			"LINEA"			=>$v["LINEA"],
			"DLINEA"		=>$v["DLINEA"],
			"MARCA"			=>$v["MARCA"],
			"DMARCA"		=>$v["DMARCA"],
			"CATEGORIA"		=>$v["CATEGORIA"],
		    "DCATEGORIA"	=>$v["DCATEGORIA"],
		    "SUBCATEGORIA"	=>$v["SUBCATEGORIA"],
			"MATERIAL"		=>$v["MATERIAL"],
			"DMATERIAL"		=>$v["DMATERIAL"],
			"TEMPORADA"		=>$v["TEMPORADA"],
            "DTEMPORADA"	=>$v["DTEMPORADA"]			  
					  
		);
	}


	return $cli;




}

function busca_metadata($articulo, $cod_articulo){
	global $tipobd_totvs,$conexion_totvs;

	$querysel = "SELECT COD_ARTICULO, ARTICULO, SUBLINEA, MUNDO, LARGO FROM Z2B_VENTASB2B_METADATA WHERE ARTICULO = '$articulo' AND COD_ARTICULO = '$cod_articulo'";


	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$art[]= array(
			"COD_ARTICULO"	=>$v["COD_ARTICULO"],
			"ARTICULO"		=>$v["ARTICULO"],
			"SUBLINEA"		=>$v["SUBLINEA"],
			"MUNDO"			=>$v["MUNDO"],
			"LARGO"			=>$v["LARGO"],
		);
	}


	return $art;
}


//EXISTE CODART 
function existe_sku($codart){
    global $tipobd_ptl,$conexion_ptl;
	
	$querycount = "SELECT COUNT(*) AS FILAS FROM ptl_conversion WHERE ptc_codart = '$codart'";
	//echo $querycount."<br>";
	$rsc= querys($querycount,$tipobd_ptl,$conexion_ptl);
	$fila = ver_result($rsc, $tipobd_ptl);
	if($fila["FILAS"]>0){
	    return true;
	}else{
	    return false;
	}

	cierra_conexion($tipobd_ptl, $conexion_ptl);
}	


function recno_tabla(){
	global $tipobd_totvs, $conexion_totvs;

    
    $querysel = "SELECT max(R_E_C_N_O_) as RECNO FROM TOTVS.VENTAS_B2B";
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    $fila = ver_result($rss, $tipobd_totvs);
    if($fila["RECNO"]==null or $fila["RECNO"]==0){
        return 1;
    }else{
        return $fila["RECNO"]+1;
    }
}


//SUBE EL ARCHIVO.
if(isset($_FILES['inputFileCargaMasiva']['name'])){
	
	$nombre_archivo = $_FILES['inputFileCargaMasiva']['name'];
	$hoy = date('Ymd');
	$sysTime = date("his");
	
	$finalFilename = $hoy."_".$sysTime."-".$nombre_archivo;
    subirArchivo();
	//$nombre_archivo = 'corona.csv';
	leer_archivo($finalFilename);

}


function get_data(){
	global $tipobd_totvs, $conexion_totvs;
	
	$user = $_SESSION['user'];


	

	if ($user == 'admin'){
		$queryget = "SELECT DISTINCT nom_archivo, fecha_subida, hora_subida, NVL(responsable, '¿?') AS responsable, r_o_w_s_, COUNT(GT) AS ROWSDB
		FROM TOTVS.VENTAS_B2B WHERE d_e_l_e_t_ <> '*' GROUP BY NOM_ARCHIVO, FECHA_SUBIDA, HORA_SUBIDA, RESPONSABLE, R_O_W_S_ ORDER BY fecha_subida DESC, hora_subida DESC";
	}else if ($user == 'treyes'){
		$queryget = "SELECT DISTINCT nom_archivo, fecha_subida, hora_subida, NVL(responsable, '¿?') AS responsable, r_o_w_s_, COUNT(GT) AS ROWSDB
		FROM TOTVS.VENTAS_B2B WHERE d_e_l_e_t_ <> '*' GROUP BY NOM_ARCHIVO, FECHA_SUBIDA, HORA_SUBIDA, RESPONSABLE, R_O_W_S_ ORDER BY fecha_subida DESC, hora_subida DESC";
	}else{
		$queryget = "SELECT DISTINCT nom_archivo, fecha_subida, hora_subida, NVL(responsable, '¿?') AS responsable, r_o_w_s_, COUNT(GT) AS ROWSDB
		FROM TOTVS.VENTAS_B2B WHERE d_e_l_e_t_ <> '*' AND responsable = '$user' GROUP BY NOM_ARCHIVO, FECHA_SUBIDA, HORA_SUBIDA, RESPONSABLE, R_O_W_S_ ORDER BY fecha_subida DESC, hora_subida DESC";
	}

	$rss = querys($queryget,$tipobd_totvs,$conexion_totvs);

	
	while($v = ver_result($rss, $tipobd_totvs)){
		$datos[]=array(
					"NOM_ARCHIVO" 	=> $v["NOM_ARCHIVO"],
					"FECHA_SUBIDA"  => formatDate($v["FECHA_SUBIDA"]), 
					"HORA_SUBIDA" 	=> substr($v["HORA_SUBIDA"], 0, 2).":".substr($v["HORA_SUBIDA"], 2, 2).":".substr($v["HORA_SUBIDA"], 4, 2), 
					"RESPONSABLE"	=> $v["RESPONSABLE"],
					"ROWS"	 		=> $v["R_O_W_S_"],
					"ROWSDB" 		=> $v["ROWSDB"]
				);
	}
	echo json_encode($datos);
}


if(isset($_GET["getData"])){	
	get_data();
}


///////////////////************POR HACER ARRIBA******************////////////////////////////////








//////////////////////////////////FUNCIONES ARRIBA/////////////////////////////////
function cmb_cliente(){
	global $tipobd_dico,$conexion_dico;
	
	$querysel = "SELECT DISTINCT EQU_RUT,EQU_DCANAL FROM PTL_EQUICOD WHERE EQU_CANAL<>' '";
	$rss = querys($querysel, $tipobd_dico, $conexion_dico);
	while($v = ver_result($rss, $tipobd_dico)){
		$cli[]= array(
			"CANAL"		=>$v["EQU_RUT"],
            "DCANAL"	=>$v["EQU_DCANAL"]			  
					  
		);
	}
	 echo json_encode($cli);
}
function cmb_canal($rut_cliente){
	global $tipobd_dico,$conexion_dico;
	
	$querysel = "SELECT DISTINCT EQU_CANAL,EQU_DCANAL FROM PTL_EQUICOD WHERE EQU_RUT='$rut_cliente'";
	$rss = querys($querysel, $tipobd_dico, $conexion_dico);
	while($v =ver_result($rss, $tipobd_dico)){
		$ca[]= array(
			"CANAL"		=>$v["EQU_CANAL"],
            "DCANAL"	=>$v["EQU_DCANAL"]			  
					  
		);
	}
	 echo json_encode($ca);
}
function cargarInput($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT B1_COD,B1_DESC,B1_UM,B1_CODBAR,B1_LOCPAD,B1_CONV
					FROM SB1010 WHERE B1_COD LIKE '%$articulo%' AND D_E_L_E_T_<>'*'";
					//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
					$articulo = $v["B1_COD"];
		$cargar["CODIGO"]=array(
					"COD"		=>$v["B1_COD"],
					"DESCR"		=>utf8_encode($v["B1_DESC"]),
					"UM"		=>$v["B1_UM"],
					"CODBAR"	=>$v["B1_CODBAR"],
					"BODEGA"	=>$v["B1_LOCPAD"],
					"CONV"		=>$v["B1_CONV"]
			);
	}

	echo json_encode($cargar);
}
function informe_pedidos($articulo,$cliente){
	global $tipobd_dico,$conexion_dico;
	
	$querysel = "SELECT   EQU_RUT,EQU_CANAL,EQU_CLICOD, EQU_INTCOD,EQU_ALTCOD, EQU_BARCOD, EQU_CLIDES,R_E_C_N_O_,EQU_UNIMED,EQU_CONV,EQU_BODEGA
					FROM PTL.PTL_EQUICOD WHERE EQU_CLICOD LIKE '%$articulo%' and  EQU_CANAL='$cliente'
					AND D_E_L_E_T_<>'*'";
		//echo $querysel." <br>";
	  $rss = querys($querysel, $tipobd_dico, $conexion_dico);
    while($v=ver_result($rss, $tipobd_dico)){	
			$informe[]=array(
							"RUT" 			=> trim($v["EQU_RUT"]),
							"CANAL"			 	=> trim($v["EQU_CANAL"]),
							"SKU_TIENDA" 		=> trim(utf8_encode($v["EQU_CLICOD"])),
							"ARTICULO"		=> trim($v["EQU_INTCOD"]),
							"ALT_COD" 		=> trim($v["EQU_ALTCOD"]),
							"BARRA" 		=> trim($v["EQU_BARCOD"]),
							"DESCRIP" 		=> utf8_encode($v["EQU_CLIDES"]),							
							"RECNO" 			=> $v["R_E_C_N_O_"],
							"UM" 			=> $v["EQU_UNIMED"],
							"CONV" 			=> $v["EQU_CONV"],
							"BODEGA" 			=> $v["EQU_BODEGA"],
							"RECNO" 			=> $v["R_E_C_N_O_"]
							
							);
	}
	echo json_encode($informe);
}
function editar_codigos($articulo,$canal){
	global $tipobd_dico,$conexion_dico;
	
	$articulo 	= utf8_decode($articulo);
	$canal 		= utf8_decode($canal);
	
	$querysel = "SELECT EQU_RUT,EQU_CANAL,EQU_CLICOD,EQU_INTCOD,EQU_ALTCOD,EQU_BARCOD,EQU_CLIDES,EQU_UNIMED,EQU_CONV,EQU_BODEGA,EQU_PROP
					FROM PTL.PTL_EQUICOD
				WHERE EQU_CLICOD =  '$articulo' AND EQU_CANAL = '$canal'";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_dico, $conexion_dico);
	while($v = ver_result($rss, $tipobd_dico)){
		$editar[]=array(
			"RUT" 			=> trim($v["EQU_RUT"]),			  
			"CANAL" 		=> trim($v["EQU_CANAL"]),
			"SKU_TIENDA"	=> trim(utf8_encode($v["EQU_CLICOD"])),
			"ARTICULO" 		=> trim($v["EQU_INTCOD"]),
			"ALT_COD" 		=> trim($v["EQU_INTCOD"]),
			"BARRA" 		=> trim($v["EQU_BARCOD"]),			  
			"DESCRIP" 		=> trim(utf8_encode($v["EQU_CLIDES"])),			  
			"UM"	 		=> trim($v["EQU_UNIMED"]),			  
			"CONV"	 		=> trim($v["EQU_CONV"]),			  
			"PROPIEDAD"	 	=> trim($v["EQU_PROP"]),			  
			"BODEGA" 		=> trim($v["EQU_BODEGA"])			  
			//"ANO" 		=> $v["ANO"],
			//"ARCHIVO" => "<a href='./archivos_de_venta/$archivo'>Ver archivo</a>"
		);
	}
//	  echo "<pre>";
//    print_r($editar);
//    echo "</pre>";
	echo json_encode($editar);
}



function insert_codigo($post){
	global $tipobd_dico,$conexion_dico;
	
	$hoy = date('Ymd');
    $cliente 	 		 = trim($post["cmb_cliente"]);
	$canal		 		 = trim($post["cmb_canal"]);
	$sku_cli		 	 = trim(utf8_decode($post["sku_cliente"]));
	$articulo	 		 = trim($post["articulo"]);
	$alt_cod	 		 = trim($post["articulo_alt"]);
    $barra	 	 		 = trim($post["cod_barra"]);
	$um 		 		 = trim($post["um"]);
	$conv 		 		 = trim($post["conv"]);
	$bodega 		 	 = trim($post["bodega"]);
	$descripcion 		 = trim($post["descripcion"]);
	$propiedad 			 = trim($post["propiedad"]);
	$delete = ' ';
	$recno	= recno_tabla();
	
	if($canal == '4001'){$dcanal = 'FALABELLA';}
	elseif($canal == '4801'){$dcanal = 'DICOTEX';}
	elseif($canal == '4003'){$dcanal = 'RIPLEY';}
	
    
	$queryin = "INSERT INTO PTL_EQUICOD (EQU_RUT,EQU_CANAL,EQU_DCANAL,EQU_CLICOD,EQU_ALTCOD,EQU_CLIDES,EQU_INTCOD,EQU_UNIMED,EQU_CONV,
				EQU_INTDES,EQU_BARCOD,EQU_BODEGA,EQU_PROP,EQU_OBSERV,D_E_L_E_T_,R_E_C_N_O_)
				VALUES('$cliente','$canal','$dcanal','$sku_cli','$alt_cod','$descripcion','$articulo','$um','$conv',
						'$descripcion','$barra','$bodega','$propiedad','$hoy',' ',$recno)";
	$rsi = querys($queryin, $tipobd_dico, $conexion_dico);
	

    //echo $queryin;
    if(db_num_fil($rsi,$tipobd_dico)<>0 or ddb_num_fil($rsi,$tipobd_dico)<>null){
        echo "ARTICULO $articulo - $recno GUARDADO CON EXITO !";
    }else{
        echo "ERROR: ARTICULO $articulo - $recno NO GUARDADO!";
    }
    // echo "<pre>";
    //print_r($_POST);
    //echo "<pre/>";
}

function update_codigo($post){
	global $tipobd_dico,$conexion_dico;
	
	$sku_tienda		= trim(utf8_decode($post["sku_cliente"]));
	$canal			= trim($post["cmb_canal"]);
	$articulo 		= trim($post["articulo"]);
	$alt_cod	 	= trim($post["articulo_alt"]);
    $barra		 	= trim($post["cod_barra"]);
	$um			 	= trim($post["um"]);
    $conv 			= trim($post["conv"]);
    $bodega			= trim($post["bodega"]);
    $propiedad		= trim($post["propiedad"]);
    $descrip		= trim(utf8_decode($post["descripcion"]));
	
	$queryup = "update PTL_EQUICOD set  EQU_ALTCOD= '$alt_cod', EQU_INTCOD='$articulo',EQU_PROP='$propiedad',
				EQU_BARCOD= '$barra', EQU_CONV='$conv', EQU_BODEGA='$bodega', EQU_CLIDES='$descrip'
				WHERE EQU_CLICOD='$sku_tienda' and EQU_CANAL='$canal'";
	//echo $queryup;
	$rsu = querys($queryup, $tipobd_dico, $conexion_dico);
	if(db_num_fil($rsu,$tipobd_dico)<>0 or db_num_fil($rsu,$tipobd_dico)<>null){
        echo "SKU TIENDA $sku_tienda ACTUALIZADA CON EXITO !";
    }else{
        echo "ERROR: SKU TIENDA $sku_tienda NO ACTUALIZADA !";
    }
}
function existe_codigo($sku_tienda,$canal){
	global $tipobd_dico,$conexion_dico;
	
	$sku_tienda = trim(utf8_decode($sku_tienda));
	
	$querysel = "SELECT count(*) as FILAS FROM PTL_EQUICOD where EQU_CLICOD='$sku_tienda' and EQU_CANAL='$canal'";
	$rss = querys($querysel, $tipobd_dico, $conexion_dico);
	//echo $querysel;
	$fila = ver_result($rss, $tipobd_dico);
    if($fila["FILAS"]>0){
        return true;
    }else{
        return false;
    }
}
function cargar_articulo($recno){
	global $tipobd_dico,$conexion_dico;
	
    $querysel = "SELECT * FROM PTL_EQUICOD2 WHERE R_E_C_N_O_= $recno";
    $rss = querys($querysel, $tipobd_dico, $conexion_dico);
    while($v = ver_result($rss, $tipobd_dico)){
        $cargar[]=array(
            "RUT"               =>$v["EQU_RUT"],
            "CANAL"             =>$v["EQU_CANAL"],
            "CLICOD"            =>$v["EQU_CLICOD"],
            "CLIDES"            =>$v["EQU_CLIDES"],
            "INTCOD"            =>$v["EQU_INTCOD"],
            "ALTCOD"            =>$v["EQU_ALTCOD"],
            "INTDES"            =>$v["EQU_INTDES"],
            "UNIMED"            =>$v["EQU_UNIMED"],
            "CONV"              =>$v["EQU_CONV"],
            "COD_BARRA"         =>$v["EQU_BARCOD"],
            "BODEGA"            =>$v["EQU_BODEGA"]      
                        
        );
    }
    echo json_encode($cargar);
}
function eliminar_linea($recno){
	global $tipobd_dico,$conexion_dico;
	
	$queryup ="UPDATE PTL_EQUICOD SET D_E_L_E_T_='*' WHERE R_E_C_N_O_='$recno'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_dico, $conexion_dico);
	
	
	if(db_num_fil($rsu,$tipobd_dico)<>0 or db_num_fil($rsu,$tipobd_dico)<>null){
			echo "ARTICULO ELIMINARDA CON EXITO !";
		}else{
			echo "ERROR: OC NO ELIMINADA!";
		}    
}
//============================================================================
if(isset($_GET["cargar"])){
    $articulo 	= $_GET["articulo"];
    $canal 		= $_GET["canal"];
    editar_codigos($articulo,$canal);
}
if(isset($_POST['informe'])){
	$articulo	 = $_POST['articulo'];
	$cliente	 = $_POST['rcliente'];
	
		informe_pedidos($articulo,$cliente);
}
if(isset($_POST["insertar"])){
	$articulo 	= $_POST["sku_cliente"];
    $canal 		= $_POST["cmb_canal"];
	
	if(existe_codigo($articulo,$canal)){
		update_codigo($_POST);
	}else{
        insert_codigo($_POST);
	}
}
if(isset($_GET["cmb_cliente"])){
    cmb_cliente();
} 
if(isset($_GET["cmb_canal"])){
    $cliente = $_GET["valor"];
    cmb_canal($cliente);
}
if(isset($_GET["eliminar_articulo"])){
	
	$recno = $_GET["recno"];	
    eliminar_linea($recno);	
}
if(isset($_GET["cargar_inputs"])){
   $articulo = $_GET['articulo'];
    cargarInput($articulo);
}



////////////////RECORRE ARRAY////////////////////////
/*

$data = busca_homologa();

print_r($data[0]["COD_MCH"]);
echo "<br>";
print_r($data[0]["ARTICULO"]);


*/

/*
$testSKU = '5597404';
$hoy = date('Ymd');
$sysTime = date("his");
$codmch = busca_cod_mch($testSKU);

///////////////////////////////////
$arrHomologa = busca_homologa($codmch);

$valArticulo = $arrHomologa[0]["ARTICULO"];
$valLinea = $arrHomologa[0]["DLINEA"];
$valMarca = $arrHomologa[0]["DMARCA"];
$valCategoria = $arrHomologa[0]["DCATEGORIA"];
$valMaterial = $arrHomologa[0]["DMATERIAL"];
$valTemporada = $arrHomologa[0]["DTEMPORADA"];

print_r($arrHomologa);echo "<br>";

print "vArt: ".$valArticulo; echo "<br>";
print "vLinea: ".$valLinea; echo "<br>";
print "vMarca: ".$valMarca; echo "<br>";
print "vCategoria: ".$valCategoria; echo "<br>";
print "vMaterial: ".$valMaterial; echo "<br>";
print "vTemporada: ".$valTemporada; echo "<br>";
print "FechaSYS: ".$hoy; echo "<br>";
print "HoraSYS: ".$sysTime; echo "<br>";
*/


