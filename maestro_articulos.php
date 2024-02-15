<?php  
require_once "conexion.php";
require_once "config.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}


function cmb_familia(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;

	
	$querysel = "SELECT B1_GFAMILI,B1_DGFAMIL FROM SB1010 
					WHERE D_E_L_E_T_<>'*'
                    AND SUBSTR(B1_GFAMILI,0,1) <> 'Z' AND B1_DGFAMIL <> 'ERROR'
					GROUP BY  B1_GFAMILI,B1_DGFAMIL
                    ORDER BY B1_GFAMILI";
					//echo $querysel;

	$rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);

	while($v = ver_result($rss, $tipobd_totvs_dev)){
		$cargar_familia[]=array(
					"B1_GFAMILI"	=>trim($v["B1_GFAMILI"]),
					"B1_DGFAMIL"	=>trim($v["B1_GFAMILI"])." - ".utf8_encode(trim($v["B1_DGFAMIL"]))
			);
	}

	echo json_encode($cargar_familia);
}

function cmb_um(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$querysel = "SELECT B1_UM FROM SB1010 
					WHERE D_E_L_E_T_<>'*' AND LENGTH(TRIM(B1_COD)) > 6
					group by B1_UM";
					//echo $querysel;
	$rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);
    
	while($v = ver_result($rss, $tipobd_totvs_dev)){
		$cargar_um[]=array(
					"B1_UM"	=>$v["B1_UM"]
			);
	}

	echo json_encode($cargar_um);
}

function cmb_segum(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$querysel = "SELECT B1_SEGUM FROM SB1010 
					WHERE D_E_L_E_T_<>'*'
                    AND B1_SEGUM <>' ' AND LENGTH(TRIM(B1_COD)) > 6
					group by B1_SEGUM
                    ORDER BY B1_SEGUM";
					//echo $querysel;
    $rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);
    
	while($v = ver_result($rss, $tipobd_totvs_dev)){
		$cargar_segum[]=array(
					"B1_SEGUM"	=>$v["B1_SEGUM"]
			);
	}

	echo json_encode($cargar_segum);
}

function cmb_proveerdor(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$querysel = "SELECT 
				B1_PROVEED,
			   (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z0' AND X5_CHAVE=B1_PROVEED AND D_E_L_E_T_<>'*') AS DPROVEED
			FROM SB1010
			WHERE D_E_L_E_T_<>'*' AND (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z0' AND X5_CHAVE=B1_PROVEED AND D_E_L_E_T_<>'*') IS NOT NULL
			group by B1_PROVEED
			ORDER BY B1_PROVEED";
					//echo $querysel;
    $rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);

    while($v = ver_result($rss, $tipobd_totvs_dev)){
		$cargar_proveedor[]=array(
					"B1_COD"	=>trim($v["B1_PROVEED"]),
					"DPROVEED"	=>trim($v["B1_PROVEED"])." - ".trim($v["DPROVEED"])
			);
	}

	echo json_encode($cargar_proveedor);
}

function cmb_marca(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$querysel = "SELECT 
				B1_MARCA,
			   (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z1' AND X5_CHAVE=B1_MARCA AND D_E_L_E_T_<>'*') AS DMARCA
			FROM SB1010
			WHERE D_E_L_E_T_<>'*' AND (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z1' AND X5_CHAVE=B1_MARCA AND D_E_L_E_T_<>'*') IS NOT NULL
			group by B1_MARCA
			ORDER BY B1_MARCA";
					//echo $querysel;
    $rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);

    while($v = ver_result($rss, $tipobd_totvs_dev)){
		$cargar_marca[]=array(
					"B1_MARCA"	=>trim($v["B1_MARCA"]),
					"DMARCA"	=>trim($v["B1_MARCA"])." - ".trim($v["DMARCA"])
			);
	}

	echo json_encode($cargar_marca);
}

function cmb_linea(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$querysel = "SELECT 	
                B1_LINEA,
                (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z2' AND X5_CHAVE=B1_LINEA AND D_E_L_E_T_<>'*') AS DLINEA
            FROM SB1010
            WHERE D_E_L_E_T_<>'*' AND (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z2' AND X5_CHAVE=B1_LINEA AND D_E_L_E_T_<>'*') <> ' '
            GROUP BY B1_LINEA
            ORDER BY B1_LINEA";
					//echo $querysel;
    $rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);

    while($v = ver_result($rss, $tipobd_totvs_dev)){
		$cargar_linea[]=array(
					"B1_LINEA"	=>trim($v["B1_LINEA"]),
					"DLINEA"	=>$v["B1_LINEA"]." - ".utf8_encode($v["DLINEA"])
			);
	}

	echo json_encode($cargar_linea);
}

function cmb_temporada(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$querysel = "SELECT 	B1_TEMPORA,
				(SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z4' AND X5_CHAVE=B1_TEMPORA AND D_E_L_E_T_<>'*') AS DTEMPORADA
				FROM SB1010
				WHERE D_E_L_E_T_<>'*' AND (SELECT X5_DESCRI FROM SX5010 WHERE X5_TABELA='Z4' AND X5_CHAVE=B1_TEMPORA AND D_E_L_E_T_<>'*') IS NOT NULL
				GROUP BY B1_TEMPORA
				ORDER BY B1_TEMPORA";
					//echo $querysel;
    $rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);

    while($v = ver_result($rss, $tipobd_totvs_dev)){
		$cargar_temporada[]=array(
					"B1_TEMPORA"	=>trim($v["B1_TEMPORA"]),
					"DTEMPORADA"	=>utf8_encode($v["DTEMPORADA"])
			);
	}

	echo json_encode($cargar_temporada);
}

function recnoZB1(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
    
    $querysel = "SELECT MAX(R_E_C_N_O_) AS RECNO FROM Z2B_SB1010";
    $rss = querys($querysel, $tipobd_totvs_dev, $conexion_totvs_dev);
    $fila = ver_result($rss, $tipobd_totvs_dev);
    if($fila["RECNO"]==null or $fila["RECNO"]==0){
        return 1;
    }else{
        return $fila["RECNO"];
    }
}

function sequZB1(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
    
    $querysel = "SELECT MAX(ZB1_SEQU) AS SEQU FROM Z2B_SB1010";
    $rss = querys($querysel, $tipobd_totvs_dev, $conexion_totvs_dev);
    $fila = ver_result($rss, $tipobd_totvs_dev);
    if($fila["SEQU"]==null or $fila["SEQU"]==0){
        return 1;
    }else{
        return $fila["SEQU"]+1;
    }
}

function array_articulo($b1cod, $descripcion, $bodega, $familia, 
						$clase, $conversion, $barra, $proveedor,
						$marca, $linea, $composicion, $temporada,
						$tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno){
	global $tipobd_totvs_dev, $conexion_totvs_dev;

	$existe = existe_articulo($b1cod);

	if ($existe == 0){

		$cargar_articulo = array(
            "B1_COD"        => trim($b1cod),
            "DESCRIPCION"   => trim($descripcion),
            "BODEGA"        => trim($bodega),
            "FAMILIA"       => trim($familia),
            "CLASE"         => trim($clase),
            "CONVERSION"    => trim($conversion),
            "BARRA"         => trim($barra),
            "PROVEEDOR"     => trim($proveedor),
            "MARCA"         => trim($marca),
            "LINEA"         => trim($linea),
            "COMPOSICION"   => trim($composicion),
            "TEMPORADA"     => trim($temporada),            
            "TIPO"     		=> trim($tipo),            
            "UM"     		=> trim($um),            
            "SEGUM"     	=> trim($segum),            
            "MAQUINA"     	=> trim($maquina),
            "CONTA"     	=> trim($conta),
            "CONTAV"     	=> trim($contav),
            "CONTAC"     	=> trim($contac),
            "ITEMCC"     	=> trim($itemcc),
            "CC"     		=> trim($cc),
            "CLVL"     		=> trim($clvl),
			"RECNO"			=> trim($recno)
        );

        return $cargar_articulo;

	}

}




function existe_articulo($b1cod){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$response = 0;

	$querycount = "SELECT COUNT(B1_COD) AS FILAS FROM SB1010 WHERE B1_COD = '$b1cod'";
	$rsc = querys($querycount,$tipobd_totvs_dev,$conexion_totvs_dev);	

	while ($row = ver_result($rsc, $tipobd_totvs_dev)) {                
        $response = $row['FILAS'];
    }

	if($response == 0){
		return $response = 0;
	}else{
		return $response;
	}

	cierra_conexion($tipobd_totvs_dev, $conexion_totvs_dev);

}

function existe_barra($barra){
	global $tipobd_totvs_dev, $conexion_totvs_dev;
	
	$response = 0;

	$querycount = "SELECT COUNT(B1_CODBAR) AS FILAS FROM SB1010 WHERE B1_CODBAR = '$barra'";
	$rsc = querys($querycount,$tipobd_totvs_dev,$conexion_totvs_dev);	

	while ($row = ver_result($rsc, $tipobd_totvs_dev)) {                
        $response = $row['FILAS'];
    }

	if($response == 0){
		return $response = 0;
	}else{
		return $response;
	}

	cierra_conexion($tipobd_totvs_dev, $conexion_totvs_dev);
}


if(isset($_GET["existe_articulo"])){	

	$origen = $_GET["origen"];
	$bodega = $_GET["bodega"];
	$estilo = $_GET["estilo"];
	$familia = $_GET["familia"];
	$clase = $_GET["clase"];
	$descripcion = $_GET["descripcion"];
	$talla = $_GET["talla"];
	$color = $_GET["color"];
	$conversion = $_GET["conversion"];
	$proveedor = $_GET["proveedor"];
	$marca = $_GET["marca"];
	$linea = $_GET["linea"];
	$composicion = $_GET["composicion"];
	$temporada = $_GET["temporada"];

	$tipo = $_GET["tipo"];
	$um = $_GET["um"];
	$segum = $_GET["segum"];
	$maquina = $_GET["maquina"];


	$conta = $_GET["conta"];
	$contav = $_GET["contav"];
	$contac = $_GET["contac"];
	$itemcc = $_GET["itemcc"];
	$cc = $_GET["cc"];
	$clvl = $_GET["clvl"];

	$getBarras = $_GET["barras"];

	
	$recno = $_GET["recno"];

	$articulos_array = array();

	switch ($talla) {
		case 1:
			$t_04 = $estilo . "-04-" . $color; 				
				$existe = existe_articulo($t_04);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_04, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
				
			$t_06 = $estilo . "-06-" . $color; 
				$existe = existe_articulo($t_06);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);	
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_06, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_08 = $estilo . "-08-" . $color; 
				
				$existe = existe_articulo($t_08);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_08, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_10 = $estilo . "-10-" . $color; 
				
				$existe = existe_articulo($t_10);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_10, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 2:
			$t_S = $estilo . "-S-" . $color; 
				$existe = existe_articulo($t_S);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_S, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_M = $estilo . "-M-" . $color; 
				$existe = existe_articulo($t_M);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_M, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_L = $estilo . "-L-" . $color; 
				$existe = existe_articulo($t_L);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_L, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_XL = $estilo . "-XL-" . $color; 
				$existe = existe_articulo($t_XL);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_XL, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_XS = $estilo . "-XS-" . $color; 
				$existe = existe_articulo($t_XS);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_XS, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 3:
			$t_10 = $estilo . "-10-" . $color; 
				$existe = existe_articulo($t_10);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_10, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_12 = $estilo . "-12-" . $color; 
				$existe = existe_articulo($t_12);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_12, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_14 = $estilo . "-14-" . $color; 
				$existe = existe_articulo($t_14);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_14, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_16 = $estilo . "-16-" . $color; 
				$existe = existe_articulo($t_16);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_16, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 4:
			$t_3_4 = $estilo . "-3/4-" . $color; 
				$existe = existe_articulo($t_3_4);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_3_4, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_5_6 = $estilo . "-5/6-" . $color; 
				$existe = existe_articulo($t_5_6);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_5_6, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_7_8 = $estilo . "-7/8-" . $color; 
				$existe = existe_articulo($t_7_8);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_7_8, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_9_10 = $estilo . "-9/10-" . $color; 
				$existe = existe_articulo($t_9_10);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_9_10, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_9_12 = $estilo . "-9/12-" . $color; 
				$existe = existe_articulo($t_9_12);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_9_12, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 5:
			$t_U = $estilo . "-U-" . $color; 
				$existe = existe_articulo($t_U);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_U, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 6:
			$t_0_2 = $estilo . "-0/2-" . $color; 
				$existe = existe_articulo($t_0_2);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_0_2, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_0_4 = $estilo . "-0/4-" . $color; 
				$existe = existe_articulo($t_0_4);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_0_4, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_0_6 = $estilo . "-0/6-" . $color; 
				$existe = existe_articulo($t_0_6);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_0_6, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_0_8 = $estilo . "-0/8-" . $color; 
				$existe = existe_articulo($t_0_8);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_0_8, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 7:
			$t_ADU = $estilo . "-ADU-" . $color; 
				$existe = existe_articulo($t_ADU);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_ADU, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_J = $estilo . "-J-" . $color; 
				$existe = existe_articulo($t_J);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_J, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_JR = $estilo . "-JR-" . $color; 
				$existe = existe_articulo($t_JR);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_JR, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 8:
			$t_35 = $estilo . "-35-" . $color; 
				$existe = existe_articulo($t_35);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_35, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_36 = $estilo . "-36-" . $color; 
				$existe = existe_articulo($t_36);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_36, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_37 = $estilo . "-37-" . $color; 
				$existe = existe_articulo($t_37);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_37, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_38 = $estilo . "-38-" . $color; 
				$existe = existe_articulo($t_38);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_38, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 9:
			$t_0_1 = $estilo . "-0/1-" . $color; 
				$existe = existe_articulo($t_0_1);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_0_1, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_1_2 = $estilo . "-1/2-" . $color; 
				$existe = existe_articulo($t_1_2);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_1_2, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_1_4 = $estilo . "-1/4-" . $color; 
				$existe = existe_articulo($t_1_4);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_1_4, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 10:
			$t_10M = $estilo . "-10M-" . $color; 
				$existe = existe_articulo($t_10M);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_10M, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_11M = $estilo . "-11M-" . $color; 
				$existe = existe_articulo($t_11M);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_11M, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_10_12 = $estilo . "-10/12-" . $color; 
				$existe = existe_articulo($t_10_12);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_10_12, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		case 11:
			$t_6 = $estilo . "-6-" . $color; 
				$existe = existe_articulo($t_6);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_6, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_7 = $estilo . "-7-" . $color; 
				$existe = existe_articulo($t_7);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_7, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_8 = $estilo . "-8-" . $color; 
				$existe = existe_articulo($t_8);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_8, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_9 = $estilo . "-9-" . $color; 
				$existe = existe_articulo($t_9);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_9, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_10 = $estilo . "-10-" . $color; 
				$existe = existe_articulo($t_10);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_10, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			$t_J = $estilo . "-J-" . $color; 
				$existe = existe_articulo($t_J);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_J, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
		default:
			$t_H = $estilo; 
				$existe = existe_articulo($t_H);
				if ($existe == 0){
					$getBarras = $getBarras + 1;
					$barra = genera_codigo($getBarras);
					$recno = $recno + 1;
					$articulos_array[] = array_articulo($t_H, $descripcion, $bodega, $familia, $clase, $conversion, $barra, $proveedor, $marca, $linea, $composicion, $temporada, $tipo, $um, $segum, $maquina,
						$conta, $contav, $contac, $itemcc, $cc, $clvl, $recno);
				}
			break;
	}

	echo json_encode($articulos_array);


}

function insertar_traspaso($post){
    global $tipobd_totvs_dev,$conexion_totvs_dev;//apunta  a la 232 BD TOTVS

/*
	--FACTOR = TBD
	--CC = TBD
	--ITEMCC = TBD
	--CONTA = TBD
	--CONTAV = TBD
	--CONTAC = TBD
	--CLVL = TBD
*/
	$filial = '01';
	
	$articulo           = trim($post["articulo"]);  //B1_COD
	$descripcion        = trim($post["descripcion"]); //B1_DESC

	$tipo 				=  trim($post["tipo"]);
	$um 				=  trim($post["um"]);

	$bodega            	= trim($post["bodega"]); //B1_LOCPAD
	$clase            	= trim($post["clase"]); // B1_GRUPO

	$factor = 1;

	$segum 				= trim($post["segum"]);
	
	$conversion         = trim($post["conversion"]); //B1_CONV

	

	$barra            	= trim($post["barra"]); //B1_CODBAR

	
	
	$proveedor         	= trim($post["proveedor"]); //B1_PROVEED
	$marca            	= trim($post["marca"]); //B1_MARCA
	$linea         		= trim($post["linea"]); //B1_LINEA
	$composicion        = trim($post["composicion"]); //B1_COMPOSI
	$temporada         	= trim($post["temporada"]); //B1_TEMPORA

	$maquina 			= trim($post["maquina"]);

	$familia         	= trim($post["familia"]); //B1_GFAMILI
	switch ($familia){
		case "1000":
			$dfamilia = 'CALCETINES';
			break;
		case "2000":
			$dfamilia = 'TRAMA';
			break;
		case "3000":
			$dfamilia = 'BALLERINAS';
			break;
		case "5000":
			$dfamilia = 'ROPA BEBE';
			break;
		case "6000":
			$dfamilia = 'ROPA INTERIOR';
			break;
		case "7000":
			$dfamilia = 'PIJAMAS';
			break;
		case "8000":
			$dfamilia = 'TRAJE DE BAÑOS';
			break;
		case "9000":
			$dfamilia = 'ACCESORIOS Y MANUALIDADES';
			break;
		default:
			$dfamilia = 'ERROR EN SWITCH';
			break;
	}

	$conta 				=  trim($post["ctacontable"]);
	$contav 			=  trim($post["ctaventa"]);
	$contac 			=  trim($post["ctacosto"]);
	$itemcc 			=  trim($post["itemgasto"]);
	$cc					=  trim($post["centrocosto"]);
	$clvl				=  trim($post["clasevalor"]);

	$recno 				=  trim($post["recno"]);

	$delet = ' ';

	$tipocod = 'EAN13';

	$status = 'IN';

	$user = $_SESSION['user'];

	$sequ = sequZB1();

	$queryin = "INSERT INTO Z2B_SB1010(
		ZB1_FILIAL, ZB1_COD, ZB1_DESC, ZB1_TIPO, ZB1_UM, ZB1_LOCPAD, ZB1_GRUPO, 
		ZB1_FACTOR, ZB1_SEGUM, ZB1_CONV, ZB1_CC, ZB1_ITEMCC, ZB1_CODBAR, ZB1_CLVL, 
		ZB1_PROVEED, ZB1_MARCA, ZB1_LINEA, ZB1_COMPOSI, ZB1_TEMPORA, ZB1_MAQUINA, 
		ZB1_GFAMILI, ZB1_DGFAMIL, ZB1_CONTA, ZB1_CONTAV, ZB1_CONTAC, R_E_C_N_O_, D_E_L_E_T_,
		ZB1_TIPOCOD, ZB1_STATUS, ZB1_USER, ZB1_SEQU
	)
	VALUES (
		'$filial', '$articulo', '$descripcion', '$tipo', '$um', '$bodega', '$clase',
		$factor, '$segum', $conversion, '$cc', '$itemcc', '$barra', '$clvl',
		'$proveedor', '$marca', '$linea', '$composicion', '$temporada', '$maquina',
		'$familia', '$dfamilia', '$conta', '$contav', '$contac', $recno, '$delet',
		'$tipocod', '$status', '$user', $sequ
		)";

	$rsi = querys($queryin,$tipobd_totvs_dev,$conexion_totvs_dev);
}

function generaInsert($frm) {
    $numFilas = intval($frm['asg-max']);
    
    for ($i = 1; $i <= $numFilas; $i++) {
        $asgData = [];
        
        if (isset($frm['asg-articulo-'.$i], $frm['asg-descripcion-'.$i], $frm['asg-bodega-'.$i], $frm['asg-familia-'.$i], $frm['asg-clase-'.$i], $frm['asg-tipo-'.$i], $frm['asg-barra-'.$i], $frm['asg-proveedor-'.$i], $frm['asg-marca-'.$i], $frm['asg-linea-'.$i], $frm['asg-composicion-'.$i], $frm['asg-temporada-'.$i], $frm['asg-maquina-'.$i], $frm['asg-um-'.$i], $frm['asg-segum-'.$i], $frm['asg-conversion-'.$i])) {
            
            $asgData['articulo'] = $frm['asg-articulo-'.$i];
            $asgData['descripcion'] = $frm['asg-descripcion-'.$i];
            $asgData['bodega'] = $frm['asg-bodega-'.$i];
            $asgData['familia'] = $frm['asg-familia-'.$i];
            $asgData['clase'] = $frm['asg-clase-'.$i];
            $asgData['tipo'] = $frm['asg-tipo-'.$i];
            $asgData['barra'] = $frm['asg-barra-'.$i];
            $asgData['proveedor'] = $frm['asg-proveedor-'.$i];
            $asgData['marca'] = $frm['asg-marca-'.$i];
            $asgData['linea'] = $frm['asg-linea-'.$i];
            $asgData['composicion'] = $frm['asg-composicion-'.$i];
            $asgData['temporada'] = $frm['asg-temporada-'.$i];
            $asgData['maquina'] = $frm['asg-maquina-'.$i];
            $asgData['um'] = $frm['asg-um-'.$i];
            $asgData['segum'] = $frm['asg-segum-'.$i];
            $asgData['conversion'] = $frm['asg-conversion-'.$i];
            
			$asgData['ctacontable'] = $frm['asg-ctacontable-'.$i];
			$asgData['ctaventa'] = $frm['asg-ctaventa-'.$i];
			$asgData['ctacosto'] = $frm['asg-ctacosto-'.$i];
			$asgData['itemgasto'] = $frm['asg-itemgasto-'.$i];
			$asgData['centrocosto'] = $frm['asg-centrocosto-'.$i];
			$asgData['clasevalor'] = $frm['asg-clasevalor-'.$i];
			
			$asgData['recno'] = $frm['asg-recno-'.$i];
            
            insertar_traspaso($asgData);
        }else{
			echo "ESTOY EN ELSE";
		}
    }
}



if(isset($_POST["insertar"])){
    generaInsert($_POST);
}

function cargarInput($estilo){
	global $tipobd_totvs_dev,$conexion_totvs_dev;
	
	$querysel = "SELECT DISTINCT B1_DESC, B1_GFAMILI, B1_GRUPO, B1_LOCPAD, 
					B1_PROVEED, B1_MARCA, B1_LINEA, B1_COMPOSI, B1_TEMPORA, B1_MAQUINA, 
					B1_CC, B1_ITEMCC, B1_CONTA, B1_CONTAV, B1_CONTAC, B1_CLVL
				FROM SB1010 WHERE B1_COD LIKE '%$estilo%' AND D_E_L_E_T_<>'*'";					
	$rss = querys($querysel, $tipobd_totvs_dev, $conexion_totvs_dev);

	while($v = ver_result($rss, $tipobd_totvs_dev)){					
		$cargar["CODIGO"]=array(
					"DESCRI"	=>trim($v["B1_DESC"]),
					"FAMILIA"	=>trim($v["B1_GFAMILI"]),
					"CLASE"		=>trim($v["B1_GRUPO"]),
					"BODEGA"	=>trim($v["B1_LOCPAD"]),
					"PROVEEDOR"	=>trim($v["B1_PROVEED"]),
					"MARCA"		=>trim($v["B1_MARCA"]),
					"LINEA"		=>trim($v["B1_LINEA"]),
					"COMPOSI"	=>trim($v["B1_COMPOSI"]),
					"TEMPORA"	=>trim($v["B1_TEMPORA"]),
					"MAQUINA"	=>trim($v["B1_MAQUINA"]),
					"CC"		=>trim($v["B1_CC"]),
					"ITEMCC"	=>trim($v["B1_ITEMCC"]),
					"CONTA"		=>trim($v["B1_CONTA"]),
					"CONTAV"	=>trim($v["B1_CONTAV"]),
					"CONTAC"	=>trim($v["B1_CONTAC"]),
					"CLVL"		=>trim($v["B1_CLVL"])
			);
	}

	echo json_encode($cargar);
}

if(isset($_GET["cargar"])){
	$estilo = $_GET['estilo'];
	$estilo =  substr($estilo, 0, 6); 

	cargarInput($estilo);
}

function genera_barra(){
	global $tipobd_totvs_dev, $conexion_totvs_dev;


	$querysel = "SELECT MAX(SUBSTR(ZB1_CODBAR,8,5)) AS SUB
		FROM Z2B_SB1010 
		WHERE substr(ZB1_COD,0,1) <>'Z' 
		AND ZB1_TIPO='PT'
		AND ZB1_TIPOCOD='EAN13'
		AND ZB1_COD<>ZB1_CODBAR";
    $rss = querys($querysel,$tipobd_totvs_dev,$conexion_totvs_dev);

	$v = ver_result($rss, $tipobd_totvs_dev);
	
	$sub = $v["SUB"];

	return $sub;

}

function genera_codigo($sub){	
	
	$codigo = "";

	$codigo_barra = '7805813'.$sub;
	
	$posiciones_pares = substr($codigo_barra,0,1)+substr($codigo_barra,2,1)+substr($codigo_barra,4,1)+substr($codigo_barra,6,1)+substr($codigo_barra,8,1)+substr($codigo_barra,10,1);
	$posiciones_inpares = substr($codigo_barra,1,1)+substr($codigo_barra,3,1)+substr($codigo_barra,5,1)+substr($codigo_barra,7,1)+substr($codigo_barra,9,1)+substr($codigo_barra,11,1);
	
	$imparesx3 =  $posiciones_inpares*3;
	$suma = $imparesx3+$posiciones_pares;
	
	$round_decena = ceil($suma / 10) * 10;
	
	$dig_verif = $round_decena-$suma;
	
	$codigo = $codigo_barra.$dig_verif;

	return $codigo;
}

function input_barra(){

	$codbar = genera_barra();

	$barra_array[]=array(
		"CODBARRA"	=>trim($codbar)
	);

	echo json_encode($barra_array);
}

if(isset($_GET["carga_barra"])){
    input_barra();
}

function input_recno(){

	$recno = recnoZB1();

	$recno_array[]=array(
		"RECNO"	=>trim($recno)
	);

	echo json_encode($recno_array);
}

if(isset($_GET["carga_recno"])){
    input_recno();
}

function input_sequ(){

	$sequ = sequZB1();

	$sequ_array[]=array(
		"SEQU"	=>trim($sequ)
	);

	echo json_encode($sequ_array);
}

if(isset($_GET["carga_sequ"])){
    input_sequ();
}

if(isset($_GET["codigo_barra"])){
    genera_codigo();
}


if(isset($_GET["cargar_familia"])){	
    cmb_familia();
}


if(isset($_GET["cargar_um"])){	
    cmb_um();
}

if(isset($_GET["cargar_segum"])){	
    cmb_segum();
}


if(isset($_GET["cargar_proveerdor"])){	
    cmb_proveerdor();
}

if(isset($_GET["cargar_marca"])){	
    cmb_marca();
}

if(isset($_GET["cargar_linea"])){	
    cmb_linea();
}

if(isset($_GET["cargar_temporada"])){	
    cmb_temporada();
}
?>
