<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "conexion.php";
require_once "config.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
require 'PHPExcel-1.8/Classes/PHPExcel.php';
require_once "generar_insert.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";


function mostrar_planillas($status){ // (status)
	global $tipobd_totvs, $conexion_totvs;
	//global $tipobd_totvs_dev, $conexion_totvs;


	$querysel ="SELECT 
		OCD_NOMPLA,
    	OCD_XPEDIDO,
		MAX(OCD_FECPLA) AS FPLANILLA,
		MAX(OCD_FECPRO) AS FPROCESO,
		MAX(OCD_HORPRO) AS HPROCESO,
		COUNT(DISTINCT CASE WHEN OCD_XPEDIDO<>'***' THEN OCD_XPEDIDO END ) AS PEDIDOS,
		ROUND(SUM(OCD_XQUANT)/12,0) AS DOCENAS,
		COUNT(DISTINCT OCD_MLOCAL) AS LOCALES,
		MAX(OCD_STATUS) AS ESTADO,
		COUNT(DISTINCT OCD_PREST) AS PREST
	FROM ZTMP_OCDIC
	WHERE OCD_STATUS LIKE '%$status%'
	GROUP BY OCD_NOMPLA, OCD_XPEDIDO
	ORDER BY FPLANILLA DESC"; // WHERE OCD_STATUS LIKE '%$status%';

	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);

	while($v = ver_result($rss, $tipobd_totvs)){
	$nombre_planilla = trim($v["OCD_XPEDIDO"]);
		$mostrar[]=array(
			"OCD_NOMPLA"	=>trim($v["OCD_NOMPLA"]),
			"OCD_NOMPLA_DESCARGA" 	=> "<a target='_blank' href='http://192.168.100.112/tmp/xls/$nombre_planilla.xls'>$nombre_planilla</a>",
			"OCD_XPEDIDO"	=>trim($v["OCD_XPEDIDO"]),
			"FPLANILLA"	    =>formatDate($v["FPLANILLA"]),
			"FPROCESO"	    =>formatDate($v["FPROCESO"]),
			"HPROCESO"	    =>substr($v["HPROCESO"], 0, 2).":".substr($v["HPROCESO"], 2, 2).":".substr($v["HPROCESO"], 4, 2), 
			"PEDIDOS"	    =>$v["PEDIDOS"],
			"DOCENAS"	    =>$v["DOCENAS"],
			"LOCALES"	    =>$v["LOCALES"],
			"ESTADO"		=>$v["ESTADO"],
			"PREST"			=>$v["PREST"],
			//"ARCHIVO_PRECIOS"	=>"<a class='btn btn-block bg-gradient-warning btn-sm' href=''><i style='font-size:20px' class='fa'>&#xf1c3;</i></a>",
		);
	}

	echo json_encode($mostrar);

}



function existen_precios($nompla){
	global $tipobd_totvs,$conexion_totvs;	
	
	$querysel = "SELECT OCD_MCODART
	FROM ZTMP_OCDIC WHERE OCD_NOMPLA = '$nompla'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);

	while($v = ver_result($rss, $tipobd_totvs)){
		$cod_monarch 		= trim($v['OCD_MCODART']);
		// echo $cod_monarch."<br>";
		$querysel_1 = "SELECT COUNT(*) AS FILAS FROM DA1010 WHERE DA1_CODPRO='$cod_monarch' AND DA1_CODTAB='008'";
		// echo $querysel_1."<br>";
		$rss_1 = querys($querysel_1, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rss_1, $tipobd_totvs);
		$filas = $v1["FILAS"];
		if($filas == 0){
			//echo '<script language="javascript">alert("ERROR : CODIGO '.$cod.' NO EXISTEN EN BODEGA DE DESTINO");</script>';
			echo "<font>ERROR : UNO O MAS CODIGOS NO EXISTEN EN LISTA DE PRECIOS 008, REVISAR EL SIGUIENTE ARCHIVO CON ARTICULOS Y PRECIOS DE LA OLA <strong>$nompla</strong></font><br>";
                    //<br><a class='btn btn-danger' href='http://seguimiento.monarch.cl:8088/mch/ecommerce_monarch/ecommerce/archivos/proceso_dicotex_precios.php?ola=$ola'>Descargar archivo</a>";
			die();
		}
		
	}
	//return $filas;
}

function digitacion_totvs($nompla){
	global $tipobd_totvs, $conexion_totvs;
	
	existen_precios($nompla);
	 revisa_duplicados($nompla);
	revalidacion_precios($nompla);
    //die();
	
	$select = "SELECT OCD_NOMPLA, COUNT(DISTINCT OCD_OCOMPRA) AS LOCALES, OCD_STATUS, COUNT(DISTINCT OCD_PREST) AS LISTA_PRECIO
	FROM ZTMP_OCDIC WHERE OCD_NOMPLA = '$nompla' GROUP BY OCD_NOMPLA, OCD_STATUS";
	

	$rss = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila_oc = ver_result($rss, $tipobd_totvs);
		
	$prest = $fila_oc['LISTA_PRECIO'];

	if ($prest != 1){
		echo 'Existen diferencias en los precios';
		die();
	}else if ($prest == 1){
		graba_head_pedido($nompla);			
	}
	echo 'PLANILLA DIGITADA EN TOTVS';
}

function graba_head_pedido($nompla){
	global $tipobd_totvs, $conexion_totvs;
	//global $tipobd_dico,$conexion_dico;
	$arrOrcom = orcom($nompla);

	//$recno = 	recno_detail();
    $hoy = 		date('Ymd');
	$rutcli = '895416002';
	
	
	
	$numRows = sizeof($arrOrcom);
	
	for ($i = 0; $i <= $numRows-1; $i++) {
		
		$lojacli = $arrOrcom[$i]["MLOCAL"];
		cliente($rutcli, $lojacli);
		global $a1_cod, $a1_nreduz, $a1_cond, $a1_naturez, $a1_tabela, $a1_grpven, $a1_loja, $a1_vend, $a1_mdescu1, $dconpag;
		global $valida_cli;			
		

		
			//$total_oc=contar_uni($oc);
				$filial = 	'01';
				$num = 		c5_num();
				$tipo = 	'N';
				$mtipven = 	'01';
				$cliente = 	$a1_cod;
				$uniresp = 	'9';
				$lojacli = 		$arrOrcom[$i]["MLOCAL"];  //OK
				$transp = 	' ';
				$local = 	$a1_nreduz;
				$tipocli = 	'A';
				$condpag = 	$a1_cond;
				$tabela = 	$a1_tabela;
				$vend1 = 	$a1_vend;
				$dconpag1=   trim($dconpag);
				$comis1 = 	'0';
				$entrega = 	$hoy;
				$orcom = 	$arrOrcom[$i]["ORCOM"]; //OK
				$mcantot = 	mcantot($orcom); // OK
				$desc1 = 	'0';
				$desc2 = 	'0';
				$desc3 = 	'0';
				$emissao = 	$arrOrcom[$i]["FECPRO"]; //OK
				$moeda = 	1;
				$tiplib = 	'1';
				$tiporem = 	0;
				$naturez = 	$a1_naturez;
				$txmoeda = 	1;
				$tpcarga = 	2;
				$docger = 	1;
				$gerawms = 	1;
				$fecent = 	1;
				$solopc = 	1;
				$liqprod = 	2;
				$userlgi = 	"CONECTOR";
				$userlga = 	"CONECTOR";
				$dte = 		1;
				$recno		= recno();
				$xagrupa = '1';
				$xintra ='1';
				$xdsitra='OPERACIÓN CONSTITUYE VENTA';
				// $xdsitra = utf8_encode($xdsitra_x);
				
				
				//echo $num.'<br>';
				//echo $recno.'<br>';
				$insert = "insert into SC5010
				(c5_filial, 	c5_num, 	c5_tipo, 	c5_mtipven, 	c5_cliente,	c5_uniresp,	c5_lojacli, 	c5_client,
				c5_lojaent, 	c5_transp,	c5_local,	c5_tipocli,	c5_condpag,	c5_tabela,	c5_vend1,	c5_dconpag,
				c5_comis1,	c5_entrega,	c5_orcom,	c5_mcantot,	c5_desc1,	c5_desc2,	c5_desc3,	c5_emissao,
				c5_moeda,	c5_tiplib,	c5_tiporem,	c5_naturez,	c5_txmoeda,	c5_tpcarga,	c5_docger,	c5_gerawms,
				c5_fecent,	c5_solopc,	c5_liqprod,	c5_dte, R_E_C_N_O_,C5_USERLGA,C5_XINDTRA,C5_XDSITRA,C5_XAGRUPA)
				values
				('$filial',	'$num',		'$tipo','$mtipven',	'$cliente',	'$uniresp',	'$lojacli',	'$cliente',
				'$lojacli', 	'$transp',	'$local',	'$tipocli',	'$condpag',	'$tabela',	'$vend1',	'$dconpag1',
				$comis1,	'$entrega',	'$orcom',	$mcantot,	$desc1, 	$desc2,		$desc3,		'$emissao',
				$moeda,		'$tiplib',	'$tiporem',	'$naturez',	$txmoeda,	'$tpcarga',	'$docger',	'$gerawms',
				'$fecent',	'$solopc',	'$liqprod',	'$dte', $recno,'$userlga','$xintra','$xdsitra','$xagrupa')";
				//echo $insert.'<br>';
				//die();
				$rs = querys($insert,$tipobd_totvs,$conexion_totvs);

				$queryUpdate = "UPDATE ZTMP_OCDIC SET OCD_PEDIDO = '$num', OCD_STATUS = '30' WHERE OCD_IDPLA = '$orcom'";		
				echo "<b>"."PEDIDO DIGITADO : ".$num."</b>";
				echo "<br>";
		
		if(oci_num_rows($rs)<>0 or oci_num_rows($rs)<>false){
			$update2 = "UPDATE Z2B_XCOMPRA SET B2D_STATUS='03', B2D_PEDIDO='$num' WHERE B2D_OCOMPRA = '$orcom'";
			$rsu2 = querys($update2, $tipobd_totvs, $conexion_totvs);

			$rsu = querys($queryUpdate, $tipobd_totvs, $conexion_totvs);

			graba_detail_pedido($num,$orcom);
					
		}


	}				
		
	
}



function graba_detail_pedido($num,$orcom){
	global $tipobd_totvs,$conexion_totvs;


	$hoy = 		date('Ymd');
	$select = "SELECT
		OCD_MCODART AS COD_MONARCH,
		OCD_MDESCRI AS DESCRI,
		OCD_MPRUNIT AS PRUNIT,
		OCD_MLOCAL AS LOJA,
		OCD_FECPRO AS FECHA,
		OCD_NOMPLA AS PLANILLA,
		CASE
			WHEN OCD_XFACTOR = 1 THEN SUM(OCD_XFACTOR * OCD_XQUANT) 
			WHEN OCD_XFACTOR >= 2 THEN SUM((OCD_XFACTOR/OCD_XFACTOR) * OCD_XQUANT)        
		END AS UNIDADES
	FROM ZTMP_OCDIC WHERE OCD_IDPLA = '$orcom'
	GROUP BY OCD_MCODART, OCD_MDESCRI, OCD_MPRUNIT, OCD_MLOCAL, OCD_XFACTOR,OCD_FECPRO,OCD_NOMPLA
	ORDER BY NLSSORT(OCD_MCODART,'NLS_SORT=BINARY_AI')";
	
	//echo "GRABA DETALLE:".$select;
	//die();
	
	$rss = querys($select, $tipobd_totvs, $conexion_totvs); /////HASTA AQUI ESTA OK

	//HASTA AQUI ESTA OK

	$resulta =false;
	if($rss){	 
		$bod_item = 0; //?????????????????????? 
		while($fila = ver_result($rss, $tipobd_totvs)){


			$oc = '**';
			//$barra = trim($fila['CODIGO_BARRA_MONARCH']);
			$codigo_articulo = trim($fila['COD_MONARCH']);
			$fecha = trim($fila['FECHA']);
			//$linea_id = $fila['R_E_C_N_O_'];
					
			articulo($codigo_articulo); //consulta datos propios del artículo o producto como, codigo monarch, descripción, bodega, factor de convesión
			global $b1_cod, $b1_desc, $b1_um, $b1_locpad, $b1_segum, $b1_conv, $b1_grupo, $b1_cc, $b1_itemcc, $b1_clvl, $b1_conta,$b1_codbar, $b1_factor;
			global $da1_prcven;
			
			
			
			$clirut = '895416002';
			$canal = '4801';
			$lojacli = $fila['LOJA'];
			$planilla = $fila['PLANILLA'];
			cliente($clirut, $lojacli);
			global $a1_cod, $a1_nreduz, $a1_cond, $a1_naturez, $a1_tabela, $a1_grpven, $a1_loja, $a1_vend, $a1_mdescu1, $dconpag;
			global $valida_cli;

			$precio_lista = lista_precios($codigo_articulo);//precio rescatado de lista de precios dicotex (008)
			$prcDesc = ($precio_lista * $a1_mdescu1) / 100;
			$unsven = $fila['UNIDADES'] * 1;	//unidades vendidas, cantidad cajas * capacidad por caja
			$qtdven = $unsven;		//cantidad vendida, unidades vendidas * factor de conversión (bipack o tripack)
			$prcven = $precio_lista - $prcDesc;
			$valor = $prcven * $qtdven;		//total, cantidad vendida * precio venta
			$pru2um = $prcven * $b1_conv;	//precio unitario bipack o tripack

			
			$filial = 	'01';			
			$bod_item =	$bod_item+1; /////////////////////////////////////////////////////////////////////OK
			$item = 	correlativo($bod_item,2); /////////////////////////////////////////////////////////////////////OK
			$c6item = 	str_pad($item,2,'0', STR_PAD_LEFT);
			$zitem = 	correlativo($bod_item,4);
			$zitem = 	str_pad($zitem,4,'0', STR_PAD_LEFT);
			$produto = 	trim($codigo_articulo);
			$um = 		$b1_um;
			$unsven = 	$qtdven;
			$qtdven = 	$qtdven;
			$prunit = 	$precio_lista;
			$pru2um = 	$pru2um;
			$descuento = $a1_mdescu1;
			$segum = 	$b1_segum;
			$prcven = 	round($prcven,2);
			$valor = 	round($valor);
			$local = 	$b1_locpad;
			$tes = 		'501';				
			$conta = 	$b1_conta;
			$entreg = 	$fecha;
			$cc = 		$b1_cc;
			$itemcta = 	$b1_itemcc;
			$clvl = 	$b1_clvl;
			$mcanal = 	$canal;
			$grupo = 	$b1_grupo;
			$cf = 		'511';
			$cli = 		$clirut;
			$valor_descuento = 		round($prcDesc);											////////
			$loja = 	$lojacli;
			$num = 		$num;
			$descri = 	$b1_desc;
			$tpop = 	'F';
			$geranf = 	'S';
			$sugentr = 	$fecha;
			$bkpprun = 	$precio_lista;
			$rateio = 	2;
			$codbar = 	trim($b1_codbar);
			$unempq = 	0;
			$capac = 	0;
			$recno = 	recno_detail();
			$recno_zc6 = recno_tabla();
			$userlga = "AUTOCONECTOR";
			
			
			$insert = "insert into SC6010
			 (c6_filial,	c6_item,        c6_produto,		c6_um,          c6_unsven,      c6_qtdven,      c6_prunit,
			c6_pru2um,	c6_descont,						c6_segum,	c6_prcven,         	c6_valor,       c6_local,       c6_tes,         c6_conta,
			c6_entreg,      c6_cc,          c6_itemcta,         	c6_clvl,        c6_mcanal,      c6_grupo,       c6_cf,
			c6_cli,      c6_valdesc   ,c6_loja,        c6_num,         	c6_descri,      c6_tpop,        c6_geranf,      c6_sugentr,
			c6_bkpprun,	c6_rateio,	c6_codbar,		c6_unempq,	c6_capac,      	r_e_c_n_o_,C6_PRCLIST, C6_FACTOR,C6_USERLGA)
			values
			('$filial',	'$c6item',	'$produto',		'$um',		$unsven,	$qtdven,	$prunit,
			$pru2um, '$descuento',	'$segum',	$prcven,		$valor,		'$local',	'$tes',		'$conta',
			'$entreg',	'$cc',		'$itemcta',		'$clvl',	'$mcanal',	'$grupo',	'$cf',
			'$cli',	$valor_descuento	,'$loja',	'$num',			'$descri',	'$tpop',	'$geranf',	'$sugentr',
			$bkpprun,	'$rateio',	'$codbar',		$unempq,	'$capac',	$recno, $precio_lista,$b1_factor,'$userlga')";
			
			// echo "SC6 : ".$insert.'<br>';
			$rs = querys($insert,$tipobd_totvs,$conexion_totvs);
			
			//insert en ZC6
			$tabla=TBL_ZC6010;
			$mfield=genera_estructura($tabla);	
			$mfield['ZC6_FILIAL']['value']='01';
			$mfield['ZC6_ZITEM']['value']=$zitem;///REVISAR
			$mfield['ZC6_OC']['value']=$orcom;
			$mfield['ZC6_CLIENT']['value']=$a1_cod;
			$mfield['ZC6_CANAL']['value']='4801';
			$mfield['ZC6_LOCAL']['value']=$a1_loja;
			$mfield['ZC6_DLOCAL']['value']=$a1_nreduz;
			$mfield['ZC6_ITEM']['value']=$c6item;
			$mfield['ZC6_INTCOD']['value']=$produto;
			$mfield['ZC6_CODBAR']['value']=$b1_codbar;
			$mfield['ZC6_PRCVEN']['value']=$prcven;
			$mfield['ZC6_CANT']['value']=$unsven;
			$mfield['ZC6_VALOR']['value']=$valor;
			$mfield['ZC6_LOCPAD']['value']=$b1_locpad;
			$mfield['ZC6_UM']['value']=$b1_um;
			$mfield['ZC6_SEGUM']['value']=$b1_segum;
			$mfield['ZC6_INTDES']['value']=$b1_desc;
			$mfield['ZC6_FACTOR']['value']=$b1_factor;
			$mfield['ZC6_CLICOD']['value']=$produto;
			$mfield['ZC6_CLIUPC']['value']=$b1_codbar;
			$mfield['ZC6_CLIDES']['value']=$b1_desc;
			$mfield['ZC6_FECOC']['value']=$fecha;
			$mfield['ZC6_FEMIS']['value']=$fecha;
			$mfield['ZC6_ENTREG']['value']=$fecha;
			$mfield['ZC6_ARCHIV']['value']=$planilla;
			$mfield['ZC6_OKDIGI']['value']='S';
			$mfield['ZC6_NUM']['value']=$num;
			$mfield['ZC6_CONTA']['value']=$b1_conta;
			$mfield['ZC6_ITEMCT']['value']=$b1_itemcc;
			$mfield['ZC6_CLVL']['value']=$b1_clvl;
			$mfield['ZC6_GRUPO']['value']=$b1_grupo;
			$mfield['ZC6_CC']['value']=$b1_cc;
			$mfield['ZC6_USUARI']['value']='AUTOCONECTOR';
			$mfield['R_E_C_N_O_']['value']=$recno_zc6;
			$mfield['ZC6_DCTO']['value']=$a1_mdescu1;
			$mfield['ZC6_PRCOC']['value']=$precio_lista;
	
	$sql=genera_insert($tabla,$mfield);
	$result = querys($sql,$tipobd_totvs,$conexion_totvs);
			

		}
	}
}
function recno_tabla(){
	global $tipobd_totvs,$conexion_totvs;
	
	$select = "SELECT nvl(MAX(R_E_C_N_O_),0)+1 AS R_E_C_N_O_ FROM ".TBL_ZC6010."";
	$rs = querys($select,$tipobd_totvs,$conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$recno = $fila['R_E_C_N_O_'];

	return $recno;

	cierra_conexion($tipobd_totvs,$conexion_totvs);
}
function revalidacion_precios($planilla){
	global $tipobd_totvs, $conexion_totvs;
	
	
	$glncliente='895416002';
	$querysel = "SELECT
		OCD_MCODART AS COD_MONARCH,
		OCD_MDESCRI AS DESCRI,
		OCD_MPRUNIT AS PRUNIT,
		OCD_MLOCAL,
		ROUND((SELECT DA1_PRCVEN FROM DA1010 WHERE DA1_CODTAB='008' AND D_E_L_E_T_<>'*' AND DA1_CODPRO=OCD_MCODART)) AS PRECIO_LISTA_MCH,
		ROUND((select AIB_PRCCOM from AIB300@LK_DICOTEX WHERE AIB_CODPRO=OCD_MCODART)) AS PRECIO_LISTA_DCX,
		OCD_MLOCAL AS LOJA,
		OCD_FECPRO AS FECHA,
		CASE
			WHEN OCD_XFACTOR = 1 THEN SUM(OCD_XFACTOR * OCD_XQUANT) 
			WHEN OCD_XFACTOR >= 2 THEN SUM((OCD_XFACTOR/OCD_XFACTOR) * OCD_XQUANT)        
		END AS UNIDADES
		
	FROM ZTMP_OCDIC WHERE OCD_NOMPLA= '$planilla'
	GROUP BY OCD_MCODART, OCD_MDESCRI, OCD_MPRUNIT, OCD_MLOCAL, OCD_XFACTOR,OCD_FECPRO
	ORDER BY NLSSORT(OCD_MCODART,'NLS_SORT=BINARY_AI')
	";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		
		
		$codigo_monarch = $v["COD_MONARCH"];
		$glnloja = $v["OCD_MLOCAL"];
		
		cliente($glncliente, $glnloja);
		global  $a1_mdescu1;
		$precio_mch 		= $v["PRECIO_LISTA_MCH"];
		$precio_dicotex 	= $v["PRECIO_LISTA_DCX"];
		$precio_con_descuento = round($precio_mch - ($precio_mch*$a1_mdescu1/100));
		
		if($precio_con_descuento != $precio_dicotex){
			echo "HAY DIFERENCIAS DE PRECIOS EN ARTICULO <br><strong>$codigo_monarch</strong> AL VALIDAR PLANILLA";
			die();
		}
		
		
	}
}
function revisa_duplicados($nompla){
	global $tipobd_totvs, $conexion_totvs;
	

	$querysel = "SELECT OCD_MCODART		
					FROM ZTMP_OCDIC 
					WHERE OCD_NOMPLA= '$nompla'
					group by OCD_MCODART
					order by OCD_MCODART";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		
		
		$codigo_monarch = trim($v["OCD_MCODART"]);
		
		$quersel_1 = "SELECT COUNT(*) AS FILAS FROM DA1010 WHERE DA1_CODTAB='008' AND DA1_CODPRO='$codigo_monarch' AND D_E_L_E_T_<>'*'";
		$rss1 = querys($quersel_1, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rss1, $tipobd_totvs);
		$filas = $v1["FILAS"];
		
		if($filas > 1 ){
			echo "ARTICULO <strong>$codigo_monarch</strong> DUPLUCADO EN LISTA DE PRECIOS 008";
			die();
		}
		
		
	}
	
}
function articulo($codigo_articulo){

	//OCD_MCODART == $codigo_articulo;

	global $tipobd_totvs, $conexion_totvs;
	//global $tipobd_totvs,$conexion_totvs;
	global $validez_art;
	global $b1_cod, $b1_desc, $b1_um, $b1_locpad, $b1_segum, $b1_conv, $b1_grupo, $b1_cc, $b1_itemcc, $b1_clvl, $b1_conta,$b1_codbar,$b1_factor;
	global $da1_prcven;
	
	$count = "SELECT COUNT(*) AS NUMFILAS FROM SB1010 WHERE b1_cod='$codigo_articulo' AND d_e_l_e_t_<>'*'"; //b1_codbar='$articulo' and
	
	//echo $count;
	$rsc = querys($count, $tipobd_totvs, $conexion_totvs);
	$filac = ver_result($rsc, $tipobd_totvs);

	if($filac['NUMFILAS'] == 1){
		
		$query = "SELECT TRIM(B1_COD) AS B1_COD, 
			B1_DESC, B1_UM, B1_LOCPAD, B1_SEGUM, 
			NVL(B1_CONV,1) AS CONV, 
			B1_GRUPO, B1_CC, B1_ITEMCC, B1_CLVL, B1_CONTA, B1_CODBAR,B1_FACTOR
		FROM SB1010 WHERE B1_COD='$codigo_articulo' AND d_e_l_e_t_<>'*'";//b1_codbar='$articulo' and
		
	  //  echo $query;	
		$rs = querys($query, $tipobd_totvs, $conexion_totvs);
		$fila=ver_result($rs, $tipobd_totvs);
		$b1_cod		= $fila['B1_COD'];
		$b1_desc 	= $fila['B1_DESC'];
		$b1_um		= $fila['B1_UM'];
		$b1_locpad	= $fila['B1_LOCPAD'];
		$b1_segum	= $fila['B1_SEGUM'];
		$b1_conv	= $fila['CONV'];
		$b1_grupo	= $fila['B1_GRUPO'];
		$b1_cc		= $fila['B1_CC'];
		$b1_itemcc	= $fila['B1_ITEMCC'];
		$b1_clvl	= $fila['B1_CLVL'];
		$b1_conta	= $fila['B1_CONTA'];
		$b1_codbar	= $fila['B1_CODBAR'];
		$b1_factor	= $fila['B1_FACTOR'];
		
		//listaprecio("('U05','005')", $b1_cod); //SE CAMBIA LA LISTA DE PRECIOS!
		//listaprecio("('PV5','I05')", $b1_cod);	
	}elseif($filac['NUMFILAS'] >= 2){
		
		$validez_art = "DA"; //artículo duplicado en tabla producto
		
		$b1_cod		= "DA";
		$b1_desc 	= "DA";
		$b1_um		= "DA";
		$b1_locpad	= "DA";
		$b1_segum	= "DA";
		$b1_conv	= "DA";
		$b1_grupo	= "DA";
		$b1_cc		= "DA";
		$b1_itemcc	= "DA";
		$b1_clvl	= "DA";
		$b1_codbar	= "DA";
		$b1_conta	= 0;
		$b1_factor	= 0;
	}else{
		$validez_art = "NA"; //articulo no existe en tabla producto
		
		$b1_cod		= "NA";
		$b1_desc 	= "NA";
		$b1_um		= "NA";
		$b1_locpad	= "NA";
		$b1_segum	= "NA";
		$b1_conv	= "NA";
		$b1_grupo	= "NA";
		$b1_cc		= "NA";
		$b1_itemcc	= "NA";
		$b1_clvl	= "NA";
		$b1_codbar	= "NA";
		$b1_conta	= 0;
		$b1_factor	= 0;
		
		$da1_prcven	= 0;
	}
}

function mcantot($ocompra){
	global $tipobd_totvs, $conexion_totvs;

	$select = "SELECT SUM(OCD_XQUANT) AS MCANTOT
	FROM ZTMP_OCDIC WHERE OCD_OCOMPRA = '$ocompra'";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$mcantot = $fila['MCANTOT'];
	return $mcantot;
	
}

function deleteSC($nompla){
	global $tipobd_totvs, $conexion_totvs;

	$arrOrcom = orcom($nompla);
	$numRows = sizeof($arrOrcom);

	for ($i = 0; $i <= $numRows-1; $i++) {
		$orcom = $arrOrcom[$i]["ORCOM"];

		$c_num = getNum($orcom); 

		$delSC6 = "DELETE FROM SC6010 WHERE C6_NUM = '$c_num'";
		$rsd6 = querys($delSC6, $tipobd_totvs, $conexion_totvs);
		//echo "delete sc6 : ".$delSC6."<br>";
		
		$delSC5 = "DELETE FROM SC5010 WHERE C5_NUM = '$c_num'";
		$rsd5 = querys($delSC5, $tipobd_totvs, $conexion_totvs);
		//echo "delete sc5 : ".$delSC5."<br>";
		
		$del_zc6 = "DELETE FROM ".TBL_ZC6010." WHERE ZC6_NUM = '$c_num'";
		$rsZC6 = querys($del_zc6, $tipobd_totvs, $conexion_totvs);
		//echo "delete sc5 : ".$delSC5."<br>";
		
		echo "NUMERO DE PEDIDO : ".$c_num." ELIMINADO <br>";
	}

	$updateOCDIC = "UPDATE ZTMP_OCDIC SET OCD_STATUS = '1', OCD_PEDIDO = ' ' WHERE OCD_NOMPLA = '$nompla'"; //setear estado "disponible para reprocesar"
	$rsuODIC = querys($updateOCDIC, $tipobd_totvs, $conexion_totvs);
	//echo "UPDATE OC: ". $updateOCDIC."<br>";
}



function getNum($orcom){
	global $tipobd_totvs, $conexion_totvs;
	
	$select = "SELECT TRIM(OCD_PEDIDO) AS C_NUM FROM ZTMP_OCDIC WHERE OCD_OCOMPRA = '$orcom'
				GROUP BY OCD_PEDIDO";

	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$num = $fila['C_NUM'];
	return $num;

}


function orcom($nompla){
	global $tipobd_totvs, $conexion_totvs;
	
	$select = "SELECT 
    	DISTINCT OCD_OCOMPRA, OCD_FECPRO, OCD_MLOCAL, OCD_MDLOCAL
	FROM ZTMP_OCDIC WHERE OCD_NOMPLA = '$nompla'
	ORDER BY OCD_MLOCAL";
	$rss = querys($select,$tipobd_totvs,$conexion_totvs);
	while($fila=ver_result($rss,$tipobd_totvs)){
		$arr[]=array(
			"ORCOM"        =>$fila["OCD_OCOMPRA"],
			"FECPRO"       =>$fila["OCD_FECPRO"],
			"MLOCAL"       =>$fila["OCD_MLOCAL"],
			"MDLOCAL"      =>$fila["OCD_MDLOCAL"],
		);
	}
	return $arr;

}




function c5_num(){
	global $tipobd_totvs, $conexion_totvs;
	//global $conexion3;
	
	//$select = "select max(to_number(c5_num))+1 as num from SC5010 where c5_num between '100000' and '199999' and d_e_l_e_t_ <> '*'";
	$select = "SELECT MAX(TO_NUMBER(C5_NUM))+1 AS NUM FROM SC5010 WHERE C5_NUM BETWEEN '200000' AND '599999'";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$num = $fila['NUM'];
	return $num;
}
function recno_detail(){
	global $tipobd_totvs, $conexion_totvs;
	//global $conexion3;
	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS CORRELATIVO FROM SC6010";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$recno = $fila['CORRELATIVO'];
	return $recno;
	
}
function contar_uni($ola){
	global $tipobd_dico,$conexion_dico;
	
	$select = "select SUM(VTA_CANTIDAD) as TOTAL FROM MVE_VENTAS WHERE VTA_PTLID=$ola";
	$rs = querys($select, $tipobd_dico, $conexion_dico);
	$fila = ver_result($rs, $tipobd_dico);
	$num = $fila['TOTAL'];
	return $num;
}

function recno(){
	global $tipobd_totvs, $conexion_totvs;
	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS CORRELATIVO FROM SC5010";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$recno = $fila['CORRELATIVO'];
	return $recno;
}


function cliente($glncliente, $glnloja){
	global $tipobd_totvs, $conexion_totvs;

	global $a1_cod, $a1_nreduz, $a1_cond, $a1_naturez, $a1_tabela, $a1_grpven, $a1_loja, $a1_vend, $a1_mdescu1, $dconpag;
	global $valida_cli;
	
	$count = "SELECT COUNT(*) AS NUMFILAS FROM SA1010, SE4010
	WHERE A1_COND=E4_CODIGO AND A1_COD='$glncliente' AND A1_LOJA = '$glnloja' AND  SA1010.D_E_L_E_T_ <> '*' AND SE4010.D_E_L_E_T_ <> '*'";//
	$rs = querys($count, $tipobd_totvs, $conexion_totvs);
	$filac = ver_result($rs, $tipobd_totvs);
	if($filac['NUMFILAS'] == 1){
		$query = "SELECT A1_COD, A1_NREDUZ, A1_COND, A1_NATUREZ, A1_TABELA, A1_GRPVEN, A1_LOJA, A1_VEND, A1_MDESCU1, E4_DESCRI
		FROM  SA1010, SE4010
		WHERE A1_COND=E4_CODIGO AND A1_COD='$glncliente' AND A1_LOJA = '$glnloja' AND  SA1010.D_E_L_E_T_ <> '*' AND SE4010.D_E_L_E_T_ <> '*'";//
	
		//echo $query.'<br>';
		$rs = querys($query, $tipobd_totvs, $conexion_totvs);
		//OBTENER RESULTADO
		$fila=ver_result($rs, $tipobd_totvs);
		$a1_cod 	= $fila['A1_COD'];
		$a1_nreduz 	= $fila['A1_NREDUZ'];
		$a1_cond 	= $fila['A1_COND'];
		$a1_naturez 	= $fila['A1_NATUREZ'];
		$a1_tabela 	= $fila['A1_TABELA'];
		$a1_grpven 	= $fila['A1_GRPVEN'];
		$a1_loja	= $fila['A1_LOJA'];
		$a1_vend	= $fila['A1_VEND'];
		$a1_mdescu1 = $fila['A1_MDESCU1'];
		$dconpag	= $fila['E4_DESCRI'];
		$valida_cli = "*";
	}else{
		$valida_cli = "N";
	}
}


function actualizar_precios(){
	global$tipobd_totvs,$conexion_totvs;
	
	$querycount = "SELECT count(*) as FILAS FROM ZTMP_OCDIC 
					WHERE OCD_PREST='EP'";
	$rsc = querys($querycount, $tipobd_totvs,$conexion_totvs);
	$vc = ver_result($rsc, $tipobd_totvs);
	$filas = $vc["FILAS"];
	// echo "FILA 1 : ".$filas."<br>";
	if($filas > 0){
		
			$querysel = "SELECT OCD_NOMPLA,OCD_MCODART
								FROM ZTMP_OCDIC 
								WHERE OCD_PREST='EP'
								ORDER BY OCD_MCODART";
			$rss = querys($querysel, $tipobd_totvs,$conexion_totvs);
			while($v = ver_result($rss, $tipobd_totvs)){
				$planilla = $v["OCD_NOMPLA"];
				$articulo = $v["OCD_MCODART"];
				$precio = lista_precios($articulo);
				
				$queryup = "UPDATE ZTMP_OCDIC SET  OCD_MPRUNIT='$precio' WHERE  OCD_NOMPLA='$planilla' AND OCD_MCODART='$articulo'";
				// echo "QUERYUP 1 : ".$queryup."<br>";
				$rsu = querys($queryup, $tipobd_totvs,$conexion_totvs);
				
			}
	}
	
	$querysel ="SELECT OCD_NOMPLA FROM ZTMP_OCDIC 
				WHERE OCD_STATUS=1
				GROUP BY OCD_NOMPLA
				ORDER BY OCD_NOMPLA";
	$rss = querys($querysel,  $tipobd_totvs,$conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$planilla = $v["OCD_NOMPLA"];
		//echo "OLA 1 : ".$ola."<br>";
		$querysel1 = "SELECT OCD_NOMPLA,OCD_MPRUNIT, OCD_XPRUNIT,
						OCD_MPRUNIT - OCD_XPRUNIT AS DIFERENCIA  FROM ZTMP_OCDIC 
						WHERE OCD_STATUS=1
						AND OCD_NOMPLA = '$planilla' 
						AND OCD_PREST='EP'";
		$rss1 = querys($querysel1,  $tipobd_totvs,$conexion_totvs);
		while($v1 = ver_result($rss1, $tipobd_totvs)){
			
			$planilla = $v1["OCD_NOMPLA"];
			$diferencia = $v1["DIFERENCIA"];
			
			if($diferencia == 0){
				$queryup_1 = "UPDATE ZTMP_OCDIC SET OCD_PREST='OK' WHERE OCD_NOMPLA='$planilla'";
				$rsu_1 = querys($queryup_1,  $tipobd_totvs,$conexion_totvs);
				echo "QUERYUP 2: ".$queryup_1."<br>";
				
			}
			
		}
			

		
		
	}
}


function correlativo($var,$largo){
	//echo $var.'__'.$largo.'<br>';
        $limite[0] = '1'.rellena('',$largo,'0','D');
        $numero[0] =  $var - $limite[0];
        $divide[0] = 10;
        $can_let = 0;
        for ($i = 1; $i <= $largo; $i++) {
                $limite[$i] = substr($limite[0],0,($i*-1)) * pow(26,$i);
                $numero[$i] = $numero[$i-1] - $limite[$i];
                $divide[$i] = $divide[$i-1] * 10;
                if ($numero[$i-1] >= 0){$can_let = $i;}
        }
        if ($numero[$largo] >= 0){$can_let = ($largo+1);}
        switch (true) {
		case $can_let == ($largo+1):
    //                  echo "el numero sobre pasa el limite <br>";
			$retorna = '';
			break;
		
		case $can_let == 0:
    //              	echo "el numero no necesita letras <br>";
			$retorna = $var;
			break;
		
		default:
			$hasta = $can_let - 1;
			$h = $hasta;
			$d = 0;
			$retorna='';
			for ($x = 0; $x <= $hasta ; $x++) {
				$val1 = ($numero[$hasta] / ($limite[$h]/$divide[$d]) +1);
				$mod1 = $numero[$hasta] % ($limite[$h]/$divide[$d]);
				if ($x == 0){
					$resta = 0;
					$htres = "0";
				}else{
					$divi = $limite[$h] / $divide[$d];
					$resta = (int) (($numero[$hasta] / $divi)/26);
					$resta = ($resta * 26);
				}
				$decimal = $mod1;
				$post_letra[$x] = $val1-$resta;
				$h--;
				$d++;
				$decim = rellena($decimal,($largo-$can_let),'0','D');
				$retorna = $retorna.chr(64+((int) ($post_letra[$x])));
			}
			$retorna = substr($retorna.$decim,0,$largo);
        }
        return($retorna);
}
function rellena($variable,$largo,$caracter,$direccion){
        $cont = strlen($variable);
        for ($i = $cont; $i < $largo; $i++) {
                switch ($direccion) {
                    case 'I':
                                $variable = $caracter.$variable;
                        break;
                    case 'D':
                                $variable = $variable.$caracter;
                        break;
                        default:
                                $variable = $variable;
                }
        }
        return ($variable);
}



function lista_precios($cod_monarch){
	global $tipobd_totvs, $conexion_totvs;
	//global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT NVL(MAX(DA1_PRCVEN),0) AS DA1_PRCVEN FROM DA1010 WHERE DA1_CODPRO='$cod_monarch' AND DA1_CODTAB='008'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rss, $tipobd_totvs);
	$pr_venta = $fila['DA1_PRCVEN'];
	return $pr_venta;
}




  


function getRecno(){
    global $tipobd_totvs,$conexion_totvs;
    //global $conexion3;
    
    $querysel = "SELECT NVL(MAX(R_E_C_N_O_),0) AS RECNO FROM SC9010";
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    $fila = ver_result($rss, $tipobd_totvs);
    if($fila["RECNO"]==null or $fila["RECNO"]==0){
        return 1;
    }else{
        return $fila["RECNO"]+1;
    }
}


function pedidos_digitados($ola){
	global $tipobd,$conexion_ptl;
	
	$querysel = "SELECT PTP_OLA,'DICOTEX' AS CLIENTE,PTP_NUMTOTVS 
						FROM PTL_PICKING
						WHERE PTP_OLA=$ola
						AND PTP_NUMTOTVS<>' ' 
						GROUP BY PTP_OLA,PTP_NUMTOTVS
						ORDER BY PTP_NUMTOTVS";
	$rss = querys($querysel, $tipobd, $conexion_ptl); //VOLVER A CONEXION PTL MONARCH
	$tabla = "<table BORDER CELLPADDING=5 CELLSPACING=0 border-collapse=collapse style='font-size:11px'>";
	$i = 0;
	  $tabla=$tabla."<tr>";
		$tabla=$tabla."<th bgcolor='#4286f4'>#</th>";
		$tabla=$tabla."<th bgcolor='#4286f4'>OLA</th>";
		$tabla=$tabla."<th bgcolor='#4286f4'>CLIENTE</th>";
		$tabla=$tabla."<th bgcolor='#4286f4'>PEDIDO</th>";
	  $tabla=$tabla."</tr>";
	while($fila=ver_result($rss, $tipobd)){
		//echo "<pre>";print_r($fila);echo "</pre>";
	  $i = $i+1;
	  $ola		 					= $fila['PTP_OLA'];
	  $cliente			     		= utf8_encode(trim($fila['CLIENTE']));
	  $pedido		 		 		= $fila['PTP_NUMTOTVS'];
	  $tabla=$tabla."<tr>";
		$tabla=$tabla."<td>".$i."</td>";
		$tabla=$tabla."<td>".$ola."</td>";
		$tabla=$tabla."<td>".$cliente."</td>";
		$tabla=$tabla."<td>".$pedido."</td>";
	  $tabla=$tabla."</tr>";
	  
	}
    $tabla=$tabla."</table>";
	return($tabla);  
}
//getUsers();
/*MAIN*/

if(isset($_POST["procesaC5"])){

	$nompla = $_POST["nompla"];

    digitacion_totvs($nompla);
}
if(isset($_POST["deleteNOMPLA"])){
   
	$nompla = $_POST["planilla"];

	deleteSC($nompla);
    
}
if(isset($_GET["mostrarPlanillas"])){
	$status = $_GET["estado"];

	if ($status == 0){
		$status = ' ';
	}else{
		$status = $_GET["estado"];
	}

	mostrar_planillas($status);
}


if(isset($_GET["actualiza_precios"])){
   
   
    actualizar_precios();
}


?>