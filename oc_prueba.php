<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate");
require_once "config.php";
require_once "lib/gestordb.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}

global $tipobd_gr,$conexion_mysql;
global $tipobd_totvs, $conexion_totvs;

$resultado 		= selec_server('MYSQL_GRUPOMONARCH');//CONEXION BBDD UTILITARIOS
$tipobd_gr 		= $resultado[0];
$conexion_mysql = $resultado[1];

$resultado1 		= selec_server('TOTVS_MCH');//CONEXION BBDD TOTVS
$tipobd_totvs 		= $resultado1[0];
$conexion_totvs 	= $resultado1[1];

function lista_OpCompra(){//CARGA LISTA DE AUTOCOMPLETADO EN INPUT CONDICIONES DE PAGO

	global $tipobd_totvs,$conexion_totvs;	
	$keyword = "%".$_POST['palabra']."%";
	$queryOP = "SELECT 	E4_CODIGO AS CODIGO,
					 	E4_DESCRI AS DESCRIPCION
				FROM SE4010
				where D_E_L_E_T_ <> '*'
				and E4_CODIGO like '$keyword'
				and rownum <= 10
				ORDER BY R_E_C_N_O_";	
	$cargar = array();
	$rss = querys($queryOP, $tipobd_totvs, $conexion_totvs);
	$html= '';
	while($row = ver_result($rss, $tipobd_totvs)){
		$html .= "<li>".$row['CODIGO']." - ".$row['DESCRIPCION'];
	}
	echo json_encode($html, JSON_UNESCAPED_SLASHES);
}
function lista_proveedores(){//CARGA LISTA DE AUTOCOMPLETADO EN INPUT PROVEEDORES
	global $tipobd_totvs,$conexion_totvs;	
	$keyword = "%".$_POST['palabraPr']."%";
	$queryPro = "SELECT A2_COD AS CODIGO,
					 	A2_NOME AS NOMBRE
				FROM SA2010
				where D_E_L_E_T_ <> '*'
				and A2_COD like '$keyword'
				and rownum <= 10
				ORDER BY R_E_C_N_O_";	
	$rss = querys($queryPro, $tipobd_totvs, $conexion_totvs);
	$html="";
	$a =0;
	$b=['"','[',']'];
	while($row = ver_result($rss, $tipobd_totvs)){
		$primero = $row['CODIGO'];
		$segundo =  $row['NOMBRE'];
		$html .= '<li>'."<b>".$primero."</b>".' - '.$segundo;
		
		// $a++;
	}
	echo json_encode($html, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
function lista_solicitante(){//CARGA LISTA DE AUTOCOMPLETADO EN INPUT SOLICITANTE
	global $tipobd_gr,$conexion_mysql;	
	$keyword = "%".$_POST['palabraSol']."%";
	$querySol = "SELECT NOMBRE
				FROM test.DIRECTORIO_TELEFONOS_TEST 
				where D_E_L_E_T_ <> '*'
				AND NOMBRE LIKE '$keyword'
				ORDER BY NOMBRE
				limit 0,10";
	$rss = querys($querySol,$tipobd_gr,$conexion_mysql);
	$html ='';
	while($row = ver_result($rss, $tipobd_gr)){
		$html .= "<li>".$row['NOMBRE'];
	}
	echo json_encode($html, JSON_UNESCAPED_UNICODE);
}
function lista_articulo(){
	global $tipobd_totvs,$conexion_totvs;	
	$keyword = $_POST['palabraArti'];
	$queryArti = "SELECT B1_COD as CODIGO, B1_DESC as DESCRIPCION 
				  FROM TOTVS.SB1010 
				  where B1_COD like '%$keyword%'
				  and  D_E_L_E_T_ <> '*'
				  and rownum  <= 10";
	//echo $queryArti;
	$rss = querys($queryArti, $tipobd_totvs, $conexion_totvs);
	$html='';
	while($row = ver_result($rss, $tipobd_totvs)){
		$primero = $row['CODIGO'];
		$segundo =  $row['DESCRIPCION'];

		$html .= "<li>"."<b>".$primero." </b>".' '.$segundo;
	}
	echo json_encode($html,JSON_UNESCAPED_SLASHES);
}
function carga_direccion($rut){
	global $tipobd_totvs,$conexion_totvs;
	$cargar = array();
	// $rempla = array(".","-");
	// $a = str_replace($rempla,"",$rut);
	$queryDire = "SELECT  A2_END as DIRECCION, 
						  A2_DESBAI as COMUNA,
						  A2_MUN AS REGION,
				  		  A2_TEL AS CONTACTO,
				  		  A2_COND AS CONDICION,
				  		  A2_NOME AS DESCRIPCION,
						  A2_COD AS RUT
				  FROM TOTVS.SA2010
				  where   D_E_L_E_T_ <> '*'
				  and A2_COD = '$rut'";
	//echo $queryDire;
	$rss = querys($queryDire, $tipobd_totvs, $conexion_totvs);
	while($row = ver_result($rss, $tipobd_totvs)){
		$cargar[]=array(
			"DIRECCION" 	=> $row["DIRECCION"],
			"COMUNA"		=> $row["COMUNA"],
			"REGION"		=> $row["REGION"],
			"CONTACTO"		=> $row["CONTACTO"],
			"CONDICION"		=> $row["CONDICION"],
			"DESCRIPCION"	=> $row["DESCRIPCION"],		
			"RUT"			=> $row["RUT"]			
		);
		//$html = trim($row['DIRECCION'])." - ".trim($row['COMUNA']);
	}
	echo json_encode($cargar, JSON_UNESCAPED_UNICODE);
}
function carga_articulo($cod){
	global $tipobd_totvs,$conexion_totvs;
	$cargar = array();
	$queryDire = "SELECT  	B1_DESC AS DESCRIPCION,
							B1_UM AS MEDIDA
				  FROM TOTVS.SB1010
				  where   D_E_L_E_T_ <> '*'
				  and B1_COD = '$cod'";
	//echo $queryDire;
	$rss = querys($queryDire, $tipobd_totvs, $conexion_totvs);
	while($row = ver_result($rss, $tipobd_totvs)){
		$cargar[]=array(
			"DESCRIPCION" => $row["DESCRIPCION"],
			"MEDIDA"	=> $row["MEDIDA"]			
		);
		//$html = trim($row['DIRECCION'])." - ".trim($row['COMUNA']);
	}
	echo json_encode($cargar, JSON_UNESCAPED_UNICODE);
}
function carga_conPago(){//CARGA TABLA EN MODAL
    global $tipobd_gr,$conexion_mysql;

    $queryspag = "SELECT E4_CODIGO AS CODIGO,
                         E4_DESCRI AS DESCRIPCION
                  FROM TOTVS.SE4010_V7
                  where D_E_L_E_T_ <> '*'
                  order by R_E_C_N_O_ asc";
	$rss = querys($queryspag, $tipobd_gr, $conexion_mysql);
	while($v = ver_result($rss,$tipobd_gr)){    
		$condiciones[]=array(					
					"CODIGO" 	    =>$v["CODIGO"],
					"DESCRIPCION" 	=>$v["DESCRIPCION"],					
			);    
        //echo $queryspag;
	}
	echo json_encode($condiciones);
}
function guarda_enca_orden(){
	global $tipobd_totvs,$conexion_totvs;
	global $numOrd;
	$numOrd 		= correlativo();
	//ENCABEZADO OREDEN DE COMPRA
	$filasEN = json_decode($_POST['valor'], true);
	$a =array("-",".");
	foreach($filasEN as $filaEN){
		$fchEmis 		= str_replace("-","",$filaEN["fechem"]);
		$codProv 		= str_replace($a,"",$filaEN["proveedor"]);
		$descProv 		= $filaEN["desPro"];
		$codConPa 		= $filaEN["conpago"];
		$descConPa 		= $filaEN["selPago"];
		$contacto 		= $filaEN["contacto"];
		$direcc 		= $filaEN["pentrega"];
		$moneda 		= $filaEN["selMoneda"];
		$solicita 		= $filaEN["solicita"];
		$fechDesp		= fecha_despacho($fchEmis);
		$observaciones 	= $filaEN["observaciones"];
		$tipo 			= $filaEN["tipo"];
		$tipoDesc 		= $filaEN["tipoDesc"];
		if(($contacto == "" || $contacto == null)){
			$contacto = " ";
		}
		if(($observaciones == "" || $observaciones == null)){
			$observaciones = " ";
		}

		//echo $numOrd."-----".$fchEmis." ".$codProv." ".$descProv." ".$descConPa." ".$contacto." ".$direcc." ".$moneda." ".$solicita."-----";
		$queryENCA = "INSERT INTO OC_ENCABE(OCE_NUMORD, OCE_FCHEMIS, OCE_PROVCOD, OCE_PROVDESC, OCE_CODCONPA, OCE_DESCONPA, OCE_CONTACTO, OCE_DIREC, OCE_SOLICI, OCE_FCHDESP, OCE_OBSERVACIONES, OCE_TIPO, OCE_TIPODESC) 
					  VALUES('$numOrd', '$fchEmis', '$codProv', '$descProv', '$codConPa', '$descConPa', '$contacto', '$direcc', '$solicita', '$fechDesp', '$observaciones', '$tipo', '$tipoDesc')";
		echo $queryENCA;
		$rss = querys($queryENCA, $tipobd_totvs, $conexion_totvs);			
	}
}
function guarda_detalle_orden(){
	global $tipobd_totvs,$conexion_totvs;
	$filas = json_decode($_POST['valores'], true);
	$ct = 0;	
	$fil = count($filas);	
	$numOrd = correlativo_tabla();
	
	foreach($filas as $fila){
		$estado = existe_orden_tabla($numOrd);
		//echo "-------".$numOrd."|----|".$estado."---------";
		if($estado == 1){
			$nArticulo 		= $fila["nArticulo"];
			$codProdu 		= $fila["codProdu"];
			$descProdu 		= $fila["descProdu"];
			$uniProd 		= $fila["uniProd"];
			$cantiProd 		= $fila["cantiProd"];
			$prcUniProdu	= $fila["prcUniProdu"];
			$descuPor 		= $fila["descuPor"];
			$descuVal 		= $fila["descuVal"];
			$totalUni 		= $fila["totalUni"];
		
			//echo $nArticulo." ".$codProdu." ".$descProdu." ".$uniProd." ".$cantiProd." ".$prcUniProdu." ".$descuPor." ".$totalUni." ".$numOrd."------";
			$iva = (((int)$totalUni) * 0.19);
			$total = (((int)$totalUni) + ((int)$iva));

			$queryDetalle = "INSERT INTO OC_DETALLE(OCD_NUMORD, OCD_CODPROD, OCD_DESPROD, OCD_UNI, OCD_CANT, OCD_VALTUNI, OCD_VALNETO, OCD_DESCU, OCD_IVA, OCD_VALTOTAL, OCD_DESCUVAL) 
					 				 VALUES('$numOrd', '$codProdu', '$descProdu', '$uniProd', '$cantiProd', '$prcUniProdu', '$totalUni', '$descuPor', '$iva', '$total', '$descuVal')";
			echo $queryDetalle;
			$rss = querys($queryDetalle, $tipobd_totvs, $conexion_totvs);
			if($ct < $fil-1){
				$ct++;
				//echo "----".$ct."----";
			} else{
				modificar_estado($numOrd);
				correo_orden($numOrd);
			}
		}
		
	}
}
function modificar_estado($numOrd){
	global $tipobd_totvs,$conexion_totvs;
	$queryMOD = "UPDATE OC_ENCABE
						SET OCE_ACTIVO = 'no'
						Where OCE_NUMORD = '$numOrd'";
	$rss = querys($queryMOD, $tipobd_totvs, $conexion_totvs);
}
function existe_orden_tabla($numOrd){
	global $tipobd_totvs,$conexion_totvs;
	$queryExiTbl = "SELECT count(*) as FILAS,
				 OCE_ACTIVO as ESTADO
				 from OC_ENCABE 
				 where OCE_NUMORD = '$numOrd'
				 group by OCE_ACTIVO";
	$rss = querys($queryExiTbl, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rss, $tipobd_totvs);
	if($fila['ESTADO'] == 'si'){
		return 1;
	} else {
		return 0;
	}
}
function existe_orden($numOrd){
	global $tipobd_totvs,$conexion_totvs;
	$queryExi = "SELECT count(*) as FILAS
				 from OC_ENCABE 
				 where OCE_NUMORD = '$numOrd'
				 GROUP BY OCE_ACTIVO";
	$rss = querys($queryExi, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rss, $tipobd_totvs);
	if($fila['FILAS'] > 0){
		return 1;
	} else {
		return 0;
	}
}
function fecha_despacho($fecha){

	$dia= substr($fecha, 6, 2);
	$mes= substr($fecha, 4, 2);
	$year= substr($fecha, 0, 4);
	$fecha_nueva=mktime(0,0,0,$mes,$dia+7,$year);// asigno nuevo valor al día
	$fec_entrega = date ("Ymd",$fecha_nueva);
	return $fec_entrega;
}
function validador(){
	$cargar[] = array(
		"NUMERO" => correlativo()
	);

	echo json_encode($cargar, JSON_UNESCAPED_UNICODE);
}
function correlativo(){
	global $tipobd_totvs,$conexion_totvs;
	$queryCorrela = "SELECT SUBSTR(max(OCE_NUMORD),3,4) AS NUMERO FROM OC_ENCABE";
	$rss = querys($queryCorrela, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss,$tipobd_totvs);
	$secuencia = $v["NUMERO"];
	$secuencia = $secuencia+1;
	$secuencia = str_pad($secuencia,4,'0', STR_PAD_LEFT);
	$año = substr(date("Y"),2,2);

	return $año.$secuencia;
}
function correlativo_tabla(){
	global $tipobd_totvs,$conexion_totvs;
	$queryCorrela = "SELECT SUBSTR(max(OCD_NUMORD),3,4) AS NUMERO FROM OC_DETALLE";
	$rss = querys($queryCorrela, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss,$tipobd_totvs);
	$secuencia = $v["NUMERO"];
	$secuencia = $secuencia+1;
	$secuencia = str_pad($secuencia,4,'0', STR_PAD_LEFT);
	$año = substr(date("Y"),2,2);

	return $año.$secuencia;
}

if(isset($_GET["ver"])){carga_proveedores();}
if(isset($_POST["palabra"])){lista_OpCompra();}
if(isset($_POST["palabraPr"])){lista_proveedores();}
if(isset($_POST["palabraSol"])){lista_solicitante();}
if(isset($_POST["palabraArti"])){lista_articulo();}
if(isset($_GET["cargar"])){
	$rut = $_GET['oc_proveedor'];
	carga_direccion($rut);
}
if(isset($_POST["valor"])){guarda_enca_orden();}
if(isset($_POST["valores"])){guarda_detalle_orden();}
if(isset($_GET["cargador"])){
	validador();
}
if(isset($_GET["cargaArti"])){

	$cod = $_GET["oc_codArticulo"];
	carga_articulo($cod);
}


function correo_orden($numOrd){
	global $tipobd_totvs,$conexion_totvs;
	global $html2;
	// $user       = $_SESSION["user"];
	// $usercod    = $_SESSION["usercod"];
	// $seguridad  = $_SESSION["seguridad"];
	$html="";
	$queryCorreo = "SELECT  OCD_NUMORD as N_ORDEN,
							OCE_PROVDESC AS PROVEEDOR,
							OCE_FCHEMIS AS FECHA_EMISION,
							OCE_DESCONPA AS CONDICION_PAGO,
							sum(OCD_VALTOTAL) as TOTAL_NETO, 
							sum(OCD_IVA) as TOTAL_IVA,
							OCE_OBSERVACIONES AS OBSERVACIONES,
							OCE_OBSAPROBADOR AS COMENTARIO        
					from OC_DETALLE
					inner join OC_ENCABE ON OCD_NUMORD=OCE_NUMORD
					where OCD_NUMORD = '$numOrd'
					GROUP BY OCD_NUMORD, OCE_PROVDESC, OCE_FCHEMIS, OCE_DESCONPA, OCE_OBSERVACIONES, OCE_OBSAPROBADOR" ;
	$rss = querys($queryCorreo,$tipobd_totvs,$conexion_totvs);
	while($row = ver_result($rss, $tipobd_totvs)){		
		$nOrden 	= $row['N_ORDEN'];
		$proveedor 	= $row['PROVEEDOR'];
		$fecha	 	= formatDate($row['FECHA_EMISION']);
		$condiPag 	= $row['CONDICION_PAGO'];
		$neto 		= number_format($row['TOTAL_NETO']);
		$iva 		= number_format($row['TOTAL_IVA']);
		$observ 	= $row["OBSERVACIONES"];
		$comentario = $row["COMENTARIO"];
	}

	$html .= "<table style='border: 1px solid'><tbody><tr>";
	$html .= "<td style='border: 1px solid'>Numero de Orden</td>" ;
	$html .= "<td style='border: 1px solid' align='right'>$nOrden</td></tr>";
	$html .= "<tr><td style='border: 1px solid'>Proveedor</td>";
	$html .= "<td style='border: 1px solid'>$proveedor</td></tr>";
	$html .= "<tr><td style='border: 1px solid'>Fecha Emisión</td>";
	$html .= "<td style='border: 1px solid' align='right'>$fecha</td></tr>";
	$html .= "<tr><td style='border: 1px solid'>Cond. Pago</td>";
	$html .= "<td style='border: 1px solid'>$condiPag</td></tr>";
	$html .= "<tr><td style='border: 1px solid'>Total Neto</td>";
	$html .= "<td style='border: 1px solid' align='right'>$neto</td></tr>";
	$html .= "<tr><td style='border: 1px solid'>Total IVA</td>";
	$html .= "<td style='border: 1px solid' align='right'>$iva</td></tr></tbody></table>";
	if(($comentario != " " || $observ != " ")){
	$html .= "<table style='border: 1px solid'><tbody><tr><td style='border: 1px solid' colspan = '2'><b>$comentario</b><br>$observ</td></tr></tbody></table>";
	}

	$query2Correo = "SELECT OCD_DESPROD AS DESCRIPCION,
							OCD_CANT AS CANTIDAD,
							OCD_VALTOTAL AS TOTAL
						FROM OC_DETALLE
						WHERE OCD_NUMORD = '$numOrd'";
	$rss2 = querys($query2Correo,$tipobd_totvs,$conexion_totvs);	

	$html .= "<table style='border: 1px solid'><thead><tr><th>Artículo</th><th>Cantidad</th><th>Valor Total</th></tr></thead><tbody>";
	while($v = ver_result($rss2, $tipobd_totvs)){
		$descri = $v["DESCRIPCION"];
		$canti = $v["CANTIDAD"];
		$total = number_format($v["TOTAL"]);
		//echo "---".$descri."----".$canti."----".$total."----";
		$html .= "<tr><td style='border: 1px solid'>$descri</td>";
		$html .= "<td style='border: 1px solid' align='right'>$canti</td>";
		$html .= "<td style='border: 1px solid' align='right'>$total</td></tr>";

	}
	$html .= "</tbody></table><br>";

	$html .= "<button type='button' style='border-radius: 10px; background-color: #218838; color: #EEEEEE; margin-right: 15px; border-color: transparent;'>Aprobar</button>";
	$html .= "<button type='button' style='border-radius: 10px; background-color: #C82333; color: #EEEEEE; border-color: transparent;'>Rechazar</button>";


	$to = "egutierrez@grupomonarch.cl";
	$asunto = "ORDEN DE COMPRA N° ".$numOrd;
	$adjunto = "";
	$adjunto2= "";

	envia_correo($to, $asunto, $html, $adjunto, $adjunto2);
}




class OC_CONSULTAS {

	public function Oc_encabezado($numOC){
		global $tipobd_totvs,$conexion_totvs;
		$queryEncaPDF = "SELECT  OCE_FCHEMIS AS FECHA,
        						OCE_PROVDESC AS PROVEEDOR,
        						OCE_PROVCOD AS RUT,
        						OCE_DIREC AS DIRECCION,
        						OCE_CONTACTO AS CONTACTO,
        						OCE_DESCONPA AS CONDIPAGO,
        						OCE_SOLICI AS SOLICITANTE,
								OCE_FCHDESP AS FECHADESPACHO,
								OCE_OBSERVACIONES AS OBSERVAC,
								OCE_TIPO AS TIPO,
								OCE_TIPODESC AS DESCTIPO
						FROM OC_ENCABE
						WHERE OCE_NUMORD = '$numOC'";
		$rss = querys($queryEncaPDF, $tipobd_totvs, $conexion_totvs);
		$cargar=[];
		while($v = ver_result($rss,$tipobd_totvs)){ 
			$cargar[]=array(					
						"FECHA" 		=>$v["FECHA"],
						"PROVEEDOR" 	=>$v["PROVEEDOR"],
						"RUT"			=>$v["RUT"],
						"DIRECCION"		=>$v["DIRECCION"],
						"CONTACTO"		=>$v["CONTACTO"],
						"CONDIPAGO"		=>$v["CONDIPAGO"],
						"SOLICITANTE"	=>$v["SOLICITANTE"],
						"FECHADESPACHO"	=>$v["FECHADESPACHO"],						
						"OBSERVAC"		=>$v["OBSERVAC"],
						"TIPO"			=>$v["TIPO"],
						"TIPODESC"		=>$v["DESCTIPO"]
				); 
		}
		return $cargar;	
	}

	public function Oc_detalle($numOC){
		global $tipobd_totvs,$conexion_totvs;
		$queryDetPDF = "SELECT  OCD_DESPROD AS PRODUCTO,
        						OCD_CANT AS CANTIDAD,
        						OCD_VALTUNI AS UNITARIO,
        						OCD_DESCU AS DECUENTO,
        						OCD_VALNETO AS SUBTOTAL,
        						OCD_IVA AS IVA,
        						OCD_VALTOTAL AS TOTAL
						FROM OC_DETALLE
						WHERE OCD_NUMORD = '$numOC'";
		 //--------------------------------------------//
		//FALTA SUMAR EL IVA Y LOS NETOS PARA EL TOTAL//
	   //--------------------------------------------//
		$rss = querys($queryDetPDF, $tipobd_totvs, $conexion_totvs);
		$cargar=[];
		while($v = ver_result($rss,$tipobd_totvs)){ 
			$cargar[]=array(					
						"PRODUCTO" 	=>$v["PRODUCTO"],
						"CANTIDAD" 	=>$v["CANTIDAD"],
						"UNITARIO"	=>$v["UNITARIO"],
						"DECUENTO"	=>$v["DECUENTO"],
						"SUBTOTAL"	=>$v["SUBTOTAL"],
						"IVA"		=>$v["IVA"],
						"TOTAL"		=>$v["TOTAL"]
				); 
		}
		return $cargar;
	}

	function OC_solicitante($nombre){//CARGA 
		global $tipobd_totvs,$conexion_totvs;	
		$querySol = "SELECT  NOMBRE as NOMBRE, 
							 CARGO as CARGO 
					 FROM TOTVS.OC_DIRECTORIO 
					 WHERE NOMBRE = '$nombre'";
		$rss = querys($querySol, $tipobd_totvs, $conexion_totvs);
		$cargar =[];
		//echo $querySol;
		//echo $rss;
		while($row = ver_result($rss,$tipobd_totvs)){
			$cargar[]=array(					
				"NOMBRE" 	=>$row["NOMBRE"],
				"CARGO" 	=>$row["CARGO"]				
			);
			echo $cargar; 
		}
		//echo $cargar["NOMBRE"]."--".$cargar["CARGO"];
		return $cargar;
	}

}




?>