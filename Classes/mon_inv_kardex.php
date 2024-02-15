<?php
session_start();
if (!isset($_SESSION['cod_usuario']))
   {  header("Location: index.php"); }
if (trim($_SESSION['cod_usuario'])=='' )
{  header("Location: index.php");}
  include "gestorini.php";
?>  
<html>
<head>
<style>
	

.titulo {
	border: 1px solid #B0CBEF;
	border-width: 1px 0px 0px 1px;
	border-width:2px;
	border-collapse:collapse;
	font-family:sans-serif;
	font-size:10px;

	background-color: #D0D7E5;
}

.xls {
	border: 1px solid #B0CBEF;
	border-width: 1px 0px 0px 1px;
	font-size: 10pt;
	font-family: Calibri;
	font-weight: 100;
	border-spacing: 0px;
	border-collapse: collapse;
	
}
.xls TH {
	background-image: url(excel-2007-header-bg.gif);
	background-repeat: repeat-x; 
	font-weight: bold;
	font-size: 13px;
	border: 1px solid #9EB6CE;
	border-width: 0px 1px 1px 0px;
	height: 17px;
}

.xls TD {
	border: 0px;
	background-color: white;
	padding: 0px 4px 0px 2px;
	border: 1px solid #D0D7E5;
	border-width: 0px 1px 1px 0px;
}

.xls TD.chico {
	border: 0px;
	background-color: white;
	padding: 0px 4px 0px 2px;
	border: 1px solid #D0D7E5;
	border-width: 0px 1px 1px 0px;
	font-size: 10px;
}

.xls TD.gris{
	border: 0px;
	background-color: #CCCCCC;
	padding: 0px 4px 0px 2px;
	border: 1px solid #D0D7E5;
	border-width: 0px 1px 1px 0px;
}

.xls TD B {
	border: 0px;
	background-color: white;
	font-weight: bold;
}

.xls TD.heading {
	background-color: #E4ECF7;
	text-align: center;
	font-size: 12px;
	border: 1px solid #9EB6CE;
	border-width: 0px 1px 1px 0px;
}

.xls TH.heading {
	background-image: url(excel-2007-header-left.gif);
	background-repeat: none;
}

.button {
    border: 1px solid #006;
    background: #ccf;
}
.button:hover {
    border: 1px solid #00f;
    background: #eef;
}

a { margin: 1em 0; float: center; clear: left; }

a.boton {
text-decoration: none;
background: #EEE;
color: #222;
border: 1px outset #CCC;
border-radius: 0.3em;
padding: .1em .5em;
}

a.boton:hover {
background: #CCB;
}

a.boton:active {
border: 1px inset #000;
}



</style>
</head>
<?php
//echo "<pre>";print_r($_GET);echo "</pre>";
  global $sd1,$sd2,$sd3,$sf4,$sb1,$sb9,$sb2,$ctt,$ctd,$ab6,$sa1,$sa2;
 
  $_SESSION['ses_ambiente']='40-msiga-ptt';
   include "gestorini.php";
   
global $con;

// ====================================================================
	function xlsBOF($fd) {
		fwrite($fd, pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0)); 
	return;
	}
	
	function xlsEOF($fd) {
		fwrite($fd, pack("ss", 0x0A, 0x00));
	return;
	}
	
	function xlsWriteNumber($fd,$Row, $Col, $Value) {
		fwrite ($fd,pack("sssss", 0x203, 14, $Row, $Col, 0x0));
		fwrite ($fd,pack("d", $Value));
		return;
	}
	
	function xlsWriteLabel($fd,$Row, $Col, $Value ) {
		$L = strlen($Value);
		fwrite ($fd, pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L));
		fwrite ($fd,$Value);
	return;
	}
// ===================================================================
	
	
$Gtabla_conexion_destino='(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.100.232)(PORT=1521)))(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=monarch)))';
$Gtabla_usuario='totvs';
$Gtabla_clave='totvs';

$con = db_logon($Gtabla_usuario,$Gtabla_clave,$Gtabla_conexion_destino);
$sb9='sb9010';
$sd1='sd1010';
$sd2='sd2010';
$sd3='sd3010';
$sf4='sf4010';
$sb1='sb1010';
$ctt='ctt010';
$sb2='sb2010';
$ctd="ctd010";
$ab6="ab6010";
$sa1="sa1010";
$sa2="sa2010";

function format_numero($xnum,$dec=0)
{
 return (number_format($xnum,$dec,',','.'));
}

function format_fecha($xfecha)
{
 if (trim($xfecha)==''){return(' ');}
 if (strlen($xfecha)<8){return(' ');}
 return(substr($xfecha,6,2).'-'.substr($xfecha,4,2).'-'.substr($xfecha,0,4));
}

function mostrar_sucursales($orden=' asc')
{
 global $con;
 global $ffecha,$fxls,$fcero,$ifecha;
 global $sd1,$sd2,$sd3,$sf4,$sb1,$sb9,$sb2,$ctt,$ctd; 
 global $filial_ini,$filial_fin,$bodega_ini,$bodega_fin;

 
 
 
 $sql="select ctd_item,ctd_desc01 from $ctd where d_e_l_e_t_<>'*' order by ctd_item $orden";
 $result=db_exec($con,$sql);
 
 while ($v=db_fetch_array($result))
 {
   
   $ctd_item=trim($v['CTD_ITEM']);
   $ctd_desc=substr(trim($v['CTD_DESC01']),0,10);
   $vsuc[$ctd_item]=$ctd_desc;   
 }
 
 return($vsuc); 	
}

function datos_os($num_os)
{
  global $con;
   global $sd1,$sd2,$sd3,$sf4,$sb1,$sb9,$sb2,$ctt,$ctd,$ab6,$sa1,$sa2;
 
  $nombre='';
  if (trim($num_os)==''){return(' ');}
  
  $sql="select ab6_numos,ab6_codcli,ab6_emissa,ab6_clasif,ab6_status,a1_nome,AB6_DTNF
          from $ab6,$sa1 where
          ab6_filial='01' and ab6_numos='$num_os' and 
		  ab6_codcli=A1_COD and ab6_loja=a1_loja and $ab6.D_E_L_E_T_<>'*' and $sa1.d_e_l_e_t_<>'*'";
  $result=db_exec($con,$sql);
  //echo "<br>os;$sql";
  
  if ($v=db_fetch_array($result))
  {
	  $nombre=substr($v['A1_NOME'],0,20);
	  return($nombre);
  }	  
}

function datos_factura($rut,$loja)
{ global $sd1,$sd2,$sd3,$sf4,$sb1,$sb9,$sb2,$ctt,$ctd,$ab6,$sa1,$sa2;
	 global $con;
     if (trim($rut)==''){return(' ');}
     $nombre='';
	 $sql="select a2_nome from $sa2 where a2_cod='$rut'  and a2_loja='$loja' and d_e_l_e_t_<>'*'";
	 $result=db_exec($con,$sql);

	 if ($v=db_fetch_array($result))
     {
		 $nombre=substr($v['A2_NOME'],0,20);
	 }
     return($nombre);	
}


function datos_fventa($rut,$loja)
{ global $sd1,$sd2,$sd3,$sf4,$sb1,$sb9,$sb2,$ctt,$ctd,$ab6,$sa1,$sa2;
	 global $con;
     if (trim($rut)==''){return(' ');}
     $nombre='';
	 $sql="select a1_nome from $sa1 where a1_cod='$rut'  and a1_loja='$loja' and d_e_l_e_t_<>'*'";
	 $result=db_exec($con,$sql);
	 if ($v=db_fetch_array($result))
     {
		 $nombre=substr($v['A1_NOME'],0,20);
	 }
     return($nombre);	
}
	
function procesar_datos()
{
   global $con;
    global $sd1,$sd2,$sd3,$sf4,$sb1,$sb9,$sb2,$ctt,$ctd,$ab6,$sa1,$sa2;
	//echo "<pre>";print_r($_GET);echo "</pre>";
	
   $producto=$_GET['p'];
   $fecha_inicial=$_GET['fi'];
   $fecha_final=$_GET['ff'];
   $fecha_sb9=$_GET['fb'];
   
   $filial=$_GET['fs'];
   $bodega=$_GET['fbo'];
   
   $filial_ini=$filial;$filial_fin=$filial;
   if ($filial=='00'){$filial_ini='01';$filial_fin='99';}
   
   $bodega_ini=$bodega;$bodega_fin=$bodega;
   if ($bodega=='00'){$bodega_ini='01';$bodega_fin='99';}
   
   $vsuc=mostrar_sucursales();
   
	$sql="select 
     *
 from (SELECT 
   d1_numseq as numseq,
'SD1' AS ORIGEN,
SD1.D1_FILIAL FILIAL,
'01_ENTRADA' MOV, 
SD1.D1_COD PRODUCTO,
SD1.D1_LOCAL BODEGA,
SD1.D1_QUANT CANTIDAD,
SD1.D1_CUSTO5 COSTO,
SD1.D1_DTDIGIT FECHA,
SD1.D1_TES AS TES,
'  ' NUM_OS,

sd1.d1_doc as doc,
sd1.d1_ESPECIE||'-'||sd1.d1_serie as especie,
sd1.d1_SERIE as serie,
sd1.d1_nfori as nfori,
sd1.d1_fornece as rut,
sd1.d1_loja    as loja,
' ' as codcosto

FROM
	$sd1 SD1 
	LEFT OUTER JOIN $sf4 SF4 
	ON SF4.F4_CODIGO = SD1.D1_TES AND
	SUBSTR(SF4.F4_FILIAL,1,2) = '01'


WHERE
(SD1.D1_FILIAL >= '$filial_ini' AND SD1.D1_FILIAL <= '$filial_fin')
AND (SD1.D1_DTDIGIT BETWEEN '$fecha_inicial' AND '$fecha_final')
and d1_cod='$producto'
AND SF4.F4_ESTOQUE = 'S'
AND SD1.D_E_L_E_T_ <> '*'
AND SF4.D_E_L_E_T_ <> '*'
AND SD1.D1_REMITO = ' '

UNION ALL

SELECT 
d2_numseq as numseq,
'SD2' AS ORIGEN,
SD2.D2_FILIAL FILIAL,
'02_SALIDA' MOV, 
SD2.D2_COD PRODUCTO,
SD2.D2_LOCAL BODEGA,
-SD2.D2_QUANT CANTIDAD,
-SD2.D2_CUSTO5  COSTO,
SD2.D2_EMISSAO FECHA,
SD2.D2_TES AS TES,
' ' AS NUM_OS,
sd2.d2_doc as doc,
sd2.d2_ESPECIE||'-'||d2_serie as especie,
sd2.d2_SERIE as serie,
sd2.d2_pedido as nfori,
sd2.d2_cliente as rut,
sd2.d2_loja    as loja,
' ' as codcosto

FROM
	$sd2 SD2 
					LEFT OUTER JOIN $sf4 SF4 
					ON SF4.F4_CODIGO = SD2.D2_TES AND
					SUBSTR(SF4.F4_FILIAL,1,2) = '01'
WHERE
(SD2.D2_FILIAL >= '$filial_ini' AND
					SD2.D2_FILIAL <= '$filial_fin') AND
					(SD2.D2_EMISSAO BETWEEN '$fecha_inicial' AND
					'$fecha_final') AND
					d2_cod='$producto'    AND
					SF4.F4_ESTOQUE = 'S'  AND
					SD2.D_E_L_E_T_ <> '*' AND
					SF4.D_E_L_E_T_ <> '*' AND
					SD2.D2_REMITO = ' '
					

UNION ALL

SELECT 
d3_numseq as numseq,
'SD3' AS ORIGEN,
SD3.D3_FILIAL FILIAL,
CASE WHEN SD3.D3_TM <= '499' THEN '03_ENTRADA' ELSE '04_SALIDA' END MOV,
SD3.D3_COD PRODUCTO,
SD3.D3_LOCAL BODEGA,

CASE WHEN SD3.D3_TM <= '499' THEN SD3.D3_QUANT ELSE -SD3.D3_QUANT END CANTIDAD,
CASE WHEN SD3.D3_TM <= '499' THEN SD3.D3_CUSTO5 ELSE -SD3.D3_CUSTO5 END COSTO,

SD3.D3_EMISSAO FECHA,
SD3.D3_TM AS TES,
' '  AS NUM_OS,
sd3.d3_doc as doc,
sd3.d3_cf as especie,
' ' as serie,
''  as nfori,
' ' as rut,
' ' as loja,
' ' as codcosto

FROM $sd3 SD3
WHERE
(SD3.D3_FILIAL >= '$filial_ini' AND SD3.D3_FILIAL <= '$filial_fin')
AND (SD3.D3_EMISSAO BETWEEN '$fecha_inicial' AND '$fecha_final')
and d3_cod='$producto'
AND SD3.D_E_L_E_T_ <> '*'
AND SD3.D3_ESTORNO <> 'S'

UNION ALL

SELECT
'000000' as numseq,
'SB9' AS ORIGEN,
B9_FILIAL FILIAL,
'00_SALDO' MOV, 
B9_COD PRODUCTO,
B9_LOCAL BODEGA,
B9_QINI CANTIDAD,
B9_VINI5 COSTO,
B9_DATA FECHA,
' ' AS TES,
' ' AS NUM_OS,
' ' as doc,
' ' as especie,
' ' as serie,
' ' as nfori,
' ' as rut,
' ' as loja,
' ' as codcosto

FROM $sb9
WHERE B9_FILIAL >= '$filial_ini'
     and b9_filial<='$filial_fin'
AND B9_DATA = '$fecha_sb9'
and b9_cod  ='$producto'
AND D_E_L_E_T_ = ' '
) temporal
  order by producto,fecha,mov,numseq";
  
 echo "<br>$sql";
//======= grabar excel ======
 $hoy=date("Ymd");
 $path="./excel/";
 $xproducto=str_replace('/','',$producto);
 $file_excel=$path."kardex_".trim($xproducto)."_".$hoy.".xls";
 $fd_xls=fopen($file_excel,"w");xlsBOF($fd_xls);
 $xls_linea=0;$xls_columna=3;
 
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'KARDEX INVENTARIO' );$xls_linea++;$xls_columna=3;

 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Codigo ' );$xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$producto );$xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Desde' );$xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna, format_fecha($fecha_inicial));$xls_columna++;
 
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Hasta' );$xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna, format_fecha($fecha_final));$xls_columna++;
 
   $xls_linea++;

//======================
$result=db_exec($con,$sql);

echo "<table class='xls'>";
echo "<tr>";
echo "<th>Codigo</th><td>$producto</td>";
echo "<th>Fecha Inicial</th><td>".format_fecha($fecha_inicial)."</td>";
echo "<th>Fecha Final</th><td>".format_fecha($fecha_final)."</td>";

$href="<a href='$file_excel'><image src='excel.jpg'></a>";
echo "<th>$href</th>";
echo "</tr>";

echo "<table class='xls'>";
echo "<tr>";
echo "<th colspan='6'></th>";
echo "<th colspan=2>ENTRADAS </th>";
echo "<th colspan=2>SALIDAS </th>";

echo "<th colspan=2>SALDO </th>";
echo "<th colspan=2>REFERENCIA</th>";



echo "</TR>";

// ============================================== EXCEL ===============================================
 $xls_linea++;
 $xls_columna=6;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'ENTRADAS');         $xls_columna++;$xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'SALIDAS');          $xls_columna++;$xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'SALDOS');          $xls_columna++;$xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'REFERENCIA');          $xls_columna++;$xls_columna++;
  
 $xls_columna=0;$xls_linea++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'FECHA');         $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'SUCURSAL');      $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'BODEGA');        $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'TES')   ;        $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'CF')    ;        $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'DOCUMENTO');     $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Cantidad');      $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Valor');         $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Cantidad');      $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Valor');         $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Cantidad');      $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Valor');         $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Documento');      $xls_columna++;
 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,'Nombre');         $xls_columna++;


 // ====================================================================================================
echo "<tr>";

echo "<th>FECHA</th>";
echo "<th>SUC</th>";
echo "<th>BOD</th>";

echo "<th>TES</th>";
echo "<th>CF</th>";
echo "<th>DOCUMENTO</th>";
echo "<th colspan=1>Cant. </th>";
echo "<th colspan=1>Valor </th>";

echo "<th colspan=1>Cant. </th>";
echo "<th colspan=1>Valor </th>";

echo "<th colspan=1>Cant. </th>";
echo "<th colspan=1>Valor </th>";

echo "<th colspan=1>Doc</th>";
echo "<th colspan=1>Nombre</th>";

echo "<th colspan=1>Numseq</th>";
echo "<th colspan=1>C.Costo</th>";

echo "</TR>";

$t_cantidad=0;$t_costo=0;
$t_entradas_q=0;$t_entradas_v=0;
$t_salidas_q=0; $t_salidas_v=0;

$tt_entradas_q=0;$tt_entradas_v=0;
$tt_salidas_q=0; $tt_salidas_v=0;

while ($v=db_fetch_array($result))
{
	$xls_linea++;
	
	$xls_columna=0;
	
	$fecha=$v['FECHA'];
	$sucursal=$v['FILIAL'];
	$xsuc=$sucursal."_".$vsuc[$sucursal];
	$bodega=$v['BODEGA'];
	
	$tes=$v['TES'];
	$especie=$v['ESPECIE'];
	$serie=$v['SERIE'];
	$doc=$v['DOC'];
	$mov=trim($v['MOV']);
	$cantidad=$v['CANTIDAD'];
	$costo=$v['COSTO'];
	$num_os=$v['NUM_OS'];
	$nfori=$v['NFORI'];
	
	$rut=$v['RUT'];
	$local=$v['LOJA'];
	$numseq=$v['NUMSEQ'];
	$codcosto=$v['CODCOSTO'];
	$t_cantidad=$t_cantidad+$cantidad;
	$t_costo=$t_costo+$costo;
	if ($mov=='00_SALDO'){$doc="** SALDO **";$t_entradas_q=$t_entradas_q=$t_entradas_q+$cantidad;$t_entradas_v=$t_entradas_v=$t_entradas_v+$costo;}
	
// =========================EXCEL =============================================	
	xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,format_fecha($fecha)); $xls_columna++;
    xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$xsuc);      $xls_columna++;
 
    xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$bodega);       $xls_columna++;
    xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$tes)   ;       $xls_columna++;
 
    xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$especie);    $xls_columna++;
    xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$doc);        $xls_columna++;
	
  
	
  
   
// =========================EXCEL =============================================	

	
	echo "<tr>";
	echo "<td>".format_fecha($fecha)."</td>";
	echo "<td>".$xsuc."</td>";
	echo "<td>".$bodega."</td>";
	
	echo "<td>".$tes."</td>";
	echo "<td>".$especie."</td>";
	echo "<td>".$doc."</td>";
	if ($cantidad>=0)
	{
	   echo "<td align='right'>".$cantidad."</td>";
   	   echo "<td align='right'>".format_numero($costo,0)."</td>";
	   echo "<td align='right'>&nbsp;</td>";
	   echo "<td align='right'>&nbsp;</td>";
	   $tt_entradas_q=$tt_entradas_q+$cantidad;
	   $tt_entradas_v=$tt_entradas_v+$costo;
	   
	    xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,abs($cantidad)); $xls_columna++;
       xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,round(abs($costo),0))   ; $xls_columna++;
	   $xls_columna=$xls_columna+2;
	   
	}
	
	if ($cantidad<0)
	{
	echo "<td align='right'>&nbsp;</td>";
	echo "<td align='right'>&nbsp;</td>";
	
	echo "<td align='right'>".abs($cantidad)."</td>";
	echo "<td align='right'>".format_numero(abs($costo),0)."</td>";
	$xls_columna=$xls_columna+2;
	xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,abs($cantidad));       $xls_columna++;
    xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,round(abs($costo),0))   ;$xls_columna++;
  
	$tt_salidas_q=$tt_salidas_q+abs($cantidad);
	$tt_salidas_v=$tt_salidas_v+abs($costo);
	   
	}
	
	
	echo "<td align='right'>".format_numero($t_cantidad,0)."</td>";
	echo "<td align='right'>".format_numero($t_costo,0)."</td>";
	
    xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,$t_cantidad);             $xls_columna++;
    xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,round($t_costo,0) );        $xls_columna++;

	
	$ref='';$os_datos='';$factura_venta='';$factura_datos='';
	if (trim($num_os)<>''){$ref='OS-'.$num_os;}
	if (trim($nfori)<>''){$ref='FAC-'.$nfori;}
	
	echo "<td align='right'>$ref</td>";
	$os_datos=datos_os($num_os);
	if ($cantidad+$costo>0){$factura_datos=datos_factura($rut,$local);}
	
	if ($cantidad+$costo<0){$factura_venta=datos_fventa($rut,$local);}
	
	$datos_ref=trim($os_datos).trim($factura_datos).trim($factura_venta);
	echo "<td><i>$datos_ref</i></td>";
	echo "<td>$numseq</td>";
	echo "<td>$codcosto</td>";
	
// ===========================Excel ===========================================	
	 xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$ref);             $xls_columna++;
    xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,$datos_ref);        $xls_columna++;
// ===========================================================================	
	echo "</tr>";
	
}
       $xls_linea++;
	   $xls_columna=3;
       xlsWriteLabel($fd_xls,$xls_linea,$xls_columna,"TOTALES"); $xls_columna++;
	   $xls_columna=6;
       xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,$tt_entradas_q)   ; $xls_columna++;
	   xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,round($tt_entradas_v,0))   ; $xls_columna++;
	   
	   xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,$tt_salidas_q)   ; $xls_columna++;
	   xlsWriteNumber($fd_xls,$xls_linea,$xls_columna,round($tt_salidas_v,0))   ; $xls_columna++;
	   
	   

    echo  "<tr>";
	echo "<td colspan=4>TOTALES</td>";
	echo "<td align='right'><b>".format_numero($tt_entradas_q,0)."</td>";
	echo "<td align='right'><b>".format_numero($tt_entradas_v,0)."</td>";

	echo "<td  align='right'><b>".format_numero($tt_salidas_q,0)."</td>";
	echo "<td   align='right'><b>".format_numero($tt_salidas_v,0)."</td>";
	
	
    echo "</tr>";

    xlsEOF($fd_xls);   
    fclose($fd_xls);
}
procesar_datos();

?>