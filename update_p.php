<?php
require_once "conexion.php";
require_once "config.php";


function actualiza_precios(){
    global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT distinct RD_RUT, RD_COD_MCH, RD_CONV  FROM Z2B_RECEP_DEVOLUCIONES 
				WHERE  D_E_L_E_T_<>'*' AND RD_ESTADO='20' 
				AND RD_ORIGEN<>'ECOMMERCE' AND RD_CONV<>0";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while ($v = ver_result($rss, $tipobd_totvs)){
		
		$rut 	= $v["RD_RUT"];
		$codigo = $v["RD_COD_MCH"];
		$conv = $v["RD_CONV"];
		$ultimo_precio = busca_ultimo_precio_facturado($rut, $codigo);
		$pr_conv = $ultimo_precio*$conv;
		
		$queryup = "UPDATE Z2B_RECEP_DEVOLUCIONES SET RD_ULT_PR=$pr_conv WHERE RD_COD_MCH='$codigo'  AND RD_RUT='$rut' ";
		$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
		
		echo "query : ".$queryup."<br>";
	
	}
	
}
function busca_ultimo_precio_facturado($rut, $codigo){
	global $tipobd_totvs, $conexion_totvs;
	
	
	$querysel = "SELECT MAX(D2_EMISSAO),NVL(MAX(D2_PRCVEN),0) as PR_ULT FROM SD2010 WHERE D2_CLIENTE ='$rut' 
					AND D2_EMISSAO BETWEEN '20221115' AND '20230210'
					AND D2_COD ='$codigo' and D2_SERIE='FEA'";
	// echo $querysel;	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss,$tipobd_totvs);
	$ultimo_precio = $v["PR_ULT"];
	
	if($ultimo_precio == 0){
		$querysel = "SELECT MAX(D2_EMISSAO),nvl(MAX(D2_PRCVEN),0) as PR_ULT FROM SD2010 WHERE D2_CLIENTE ='$rut' 
					AND D2_COD ='$codigo' and D2_SERIE='FEA'";
	// echo $querysel;
	
		$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
		$v = ver_result($rss,$tipobd_totvs);
		$ultimo_precio = $v["PR_ULT"];
		
		return $ultimo_precio;
	}else{
		return $ultimo_precio;
		
	}
	
	
}
function actualiza_conversion(){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT RD_RUT, RD_COD_MCH, RD_CONV  FROM Z2B_RECEP_DEVOLUCIONES 
				WHERE  D_E_L_E_T_<>'*' AND RD_ESTADO='20' 
				AND RD_ORIGEN<>'ECOMMERCE' AND RD_CONV=0";	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while ($v = ver_result($rss, $tipobd_totvs)){
		
		$rut 	= $v["RD_RUT"];
		$codigo = trim($v["RD_COD_MCH"]);
		$conv = busca_conv($codigo);
		
		$queryup = "UPDATE Z2B_RECEP_DEVOLUCIONES SET RD_CONV=$conv WHERE RD_COD_MCH='$codigo' AND RD_CONV=0";
		$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
		echo $queryup."<br>";
	}
}
function busca_conv($sku_monarch){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$querysel = "SELECT NVL(MAX(B1_CONV),0) AS B1_CONV FROM SB1010 WHERE B1_COD='$sku_monarch'";
	// echo $querysel;
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$conv = $v["B1_CONV"];
	
	return $conv;
	
}
function resto(){
	
	$x = 78;
	$y = 2;
	$r = fmod($x, $y);
	
	echo "resto : ".$r;
	
}
function actualiza_cant_entrada(){
    global $tipobd_totvs,$conexion_totvs;
    
    $querysel = "SELECT BO_SECUENCIA FROM Z2B_TRASPASO_BODEGA WHERE BO_FECHA>'20230731' group by BO_SECUENCIA";
     //echo $querysel;
        //die();
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    while ($v = ver_result($rss, $tipobd_totvs)){
        $secuencia = $v["BO_SECUENCIA"];
        
        $querysel_2 = "SELECT BO_SECUENCIA, BO_COD, BO_CAMARTICULO, BO_CANTIDAD, R_E_C_N_O_ FROM Z2B_TRASPASO_BODEGA WHERE BO_SECUENCIA='$secuencia'";
        //echo $querysel_2;
        //die();
        $rss_2 = querys($querysel_2, $tipobd_totvs, $conexion_totvs);
         while ($v2 = ver_result($rss_2, $tipobd_totvs)){
           $secuencia_1 = $v2["BO_SECUENCIA"];
            $articulo_salida = $v2["BO_COD"];
            $articulo_entrada = $v2["BO_CAMARTICULO"];
            $cantidad = $v2["BO_CANTIDAD"];
            $recno = $v2["R_E_C_N_O_"];
            $factor_salida = busca_factor($articulo_salida);
            $factor_entrada = busca_factor($articulo_entrada);
            
            $cantidad_entrada = ($cantidad*$factor_salida)/ $factor_entrada;
            
            $queryup = "UPDATE Z2B_TRASPASO_BODEGA SET BO_CANTEN=$cantidad_entrada where R_E_C_N_O_=$recno AND BO_SECUENCIA='$secuencia_1'";
            $rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
            echo $queryup."<br>";
         }
        
        
    }
    
}
function busca_factor($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_FACTOR FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
   //echo $queryin."<br>";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){   
       $codigo=trim($row2['B1_FACTOR']);
    }

	if($codigo==""){
		return $codigo="0";
	}else{
		return $codigo;
	}
}

//resto();
// actualiza_precios();
// actualiza_conversion();
actualiza_cant_entrada();
?>