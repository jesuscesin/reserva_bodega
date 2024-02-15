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

function insertMetadata($cod_art, $articulo, $sublinea, $mundo, $largo, $delet, $recno){
	global $tipobd_totvs, $conexion_totvs;

	                     
	$queryInsert = "INSERT INTO TOTVS.Z2B_VENTASB2B_METADATA(COD_ARTICULO, ARTICULO, SUBLINEA, MUNDO, LARGO, D_E_L_E_T_, R_E_C_N_O_)
    	VALUES('$cod_art', '$articulo', '$sublinea', '$mundo', '$largo', '$delet', $recno)";
	$rss = querys($queryInsert,$tipobd_totvs,$conexion_totvs);
	
}

function subirArchivo(){

	$hoy = date('Ymd');
	$sysTime = date("his");

	$dir_subida = '/var/www/html/grupomonarch/portal/archivos_subidos/ventasb2b_metadata/';
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


	$path="./archivos_subidos/ventasb2b_metadata/";
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

		$colArticulo = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$colSublinea = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		
		$colMundo = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(); 
		$colLargo = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();

		
		$tCodArticulo = trim(utf8_decode($colArticulo));

		$tArtLength = strlen($tCodArticulo);
		$tArticulo  = 'TBD';

		if ($tArtLength > 6){
			$tArticulo = substr($tCodArticulo, 0, 6);
		}else if($tArtLength == 6){
			$tArticulo = $tCodArticulo;
		}else if($tArtLength == 5){
			$tArticulo = '0'.$tCodArticulo;
		}else if($tArtLength == 4){
			$tArticulo = '00'.$tCodArticulo;
		}


		$tSublinea = trim($colSublinea);
		$tMundo = trim($colMundo);
		$tLargo = trim($colLargo);
				
		$valDelet = ' ';
		$valRecno = recno_tabla();

		insertMetadata($tCodArticulo, $tArticulo, $tSublinea, $tMundo, $tLargo, $valDelet, $valRecno);

	}
	cierra_conexion($tipobd_totvs, $conexion_totvs);
}

function recno_tabla(){
	global $tipobd_totvs, $conexion_totvs;

    
    $querysel = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 as RECNO FROM TOTVS.Z2B_VENTASB2B_METADATA";
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    $fila = ver_result($rss, $tipobd_totvs);

    if($fila["RECNO"]==null or $fila["RECNO"]==0){
        return 1;
    }else{
        return $fila["RECNO"];
    }
}

function busca_cod_mch($sku){

	global $tipobd_ptl,$conexion_ptl;

	$querysel = "SELECT equ_intcod FROM PTL_EQUICOD WHERE equ_clicod = '$sku'";
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




//SUBE EL ARCHIVO.
if(isset($_FILES['inputFileCargaMasiva']['name'])){
	
	$nombre_archivo = $_FILES['inputFileCargaMasiva']['name'];
	$hoy = date('Ymd');
	$sysTime = date("his");
	
	$finalFilename = $hoy."_".$sysTime."-".$nombre_archivo;

	subirArchivo();
	leer_archivo($finalFilename);

}

