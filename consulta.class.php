<?php
require_once "conexion.php";
require_once "config.php";


class busqueda
{
	public function procesar_form1($rut_entrada)
 {
	$rut_entrada = $_GET['term'];
	
	$rut = str_replace(".","",$rut_entrada);
	$rut_entr = str_replace("-","",$rut);
	global $tipobd_totvs,$conexion_totvs;
	$datos = array();
	$sql="select 
			a1_cod,
			a1_loja,
			a1_endent,
			a1_giro,
			A1_NREDUZ,
			a1_end,
			A1_NOME,
			a1_tabela,
			X5_DESCRI,
			e4_descri
			from 
			totvs.sa1010, totvs.sx5010, totvs.se4010
			where 
			a1_cod like '%$rut_entr%' and 
			A1_MUNE = X5_CHAVE and 
			a1_conexion2d=e4_codigo and
			X5_Tabela='ZS' and
			a1_msblql<>'1' and
			se4010.d_e_l_e_t_<>'*'
			and sa1010.d_e_l_e_t_<>'*'";
      
	$result=querys($sql, $tipobd_totvs, $conexion_totvs);
	while($v=ver_result($result, $tipobd_totvs))
	{
		$rutloc=trim($v['A1_COD']).'--'.trim($v['A1_LOJA']);
		//echo $rutloc;
		$datos[] = Array("value" => $rutloc,
			        "rsocial" => trim($v['A1_NOME']),
				"dir_despacho" => trim($v['A1_ENDENT']),
				"precio_lista" => trim($v['A1_TABELA']),
				"ciudad" => trim($v['X5_DESCRI']),
				"conexion2d_pago" => trim($v['E4_DESCRI']));
	}
	return $datos;
//echo $sql;
 }
}


class b_codigo
{
	public function buscar_articulo($zarticulo)
	{
	$zarticulo = strtoupper($_GET['term']);
		global $tipobd_totvs,$conexion_totvs;
		$xcodigo = array();
		$sql="select b1_cod,b1_desc from totvs.sb1010 where 
				b1_cod like '%$zarticulo%' and 
				b1_desc not in ('*******   NO USAR   *******','NO USAR') and 
				 D_E_L_E_T_ <> '*'";
		$result=querys($sql, $tipobd_totvs, $conexion_totvs);
		while($v=ver_result($result, $tipobd_totvs))
		{
			$xcodigo[] = Array("value" => trim($v['B1_COD']),
					"descrip" => utf8_encode(trim(ucwords(strtolower($v['B1_DESC'])))));
		}
	 return $xcodigo;
	}

}



class b_stock
{
	public function busca_stock($art)
	{
		global $tipobd_totvs,$conexion_totvs;
		$stock = array();
		$sql="select b1_cod as codigo,max(b1_desc) as descripcion,
			sum(case when b2_local='01' then round(b2_qatu /12,0)end) as Calcetines_VM_Doc,
			sum(case when b2_local='02' then round(b2_qatu /12,0)end) as Trama_VM_Doc,
			sum(case when b2_local='03' then round(b2_qatu /12,0)end) as Saldos_VM_Doc,
			sum(case when b2_local='04' then round(b2_qatu /12,0)end) as Devoluciones_VM_Doc,
			sum(case when b2_local='11' then round(b2_qatu /12,0)end) as Calcetines_JA_Doc,
			sum(case when b2_local='12' then round(b2_qatu /12,0)end) as Trama_JA_Doc,
			sum(case when b2_local='14' then round(b2_qatu /12,0)end) as Devoluciones_JA_Doc,
			sum(case when b2_local='15' then round(b2_qatu /12,0)end) as RInterior_JA_Doc,
			sum(case when b2_local='21' then round(b2_qatu /12,0)end) as ProductosTerminados_AG_Doc,
			sum(case when b2_local='22' then round(b2_qatu /12,0)end) as Semielaborado_AG_Doc,
			sum(case when b2_local='23' then round(b2_qatu /12,0)end) as Reproceso_AG_Doc,
			sum(case when b2_local='31' then round(b2_qatu /12,0)end) as Calcetines_AM_Doc,
			sum(case when b2_local='34' then round(b2_qatu /12,0)end) as Devoluciones_AM_Doc,
			sum(case when b2_local='42' then round(b2_qatu /12,0)end) as Glamour_SE_Doc,
			sum(case when b2_local='43' then round(b2_qatu /12,0)end) as Taller_Externo_Dev_Doc,
			max(b1_grupo) as clase
		from totvs.sb1010,totvs.sb2010
		where b1_cod= b2_cod (+) and b1_cod like '%$art%'
		and substr(b1_cod,1,2)<>'XX' and sb1010.d_e_l_e_t_<>'*' and sb2010.d_e_l_e_t_<>'*' and rownum<='150'
		group by b1_cod
		order by b1_cod";
		$result=querys($sql, $tipobd_totvs, $conexion_totvs);
			while($v=ver_result($result, $tipobd_totvs))
		{
		//	$stock [] = array(
				if(trim($v['Calcetines_VM_Doc'])!=""){			$stock[] = array("calcetines_vm_doc"			=> 	trim($v['Calcetines_VM_Doc']));}
				if(trim($v['Trama_VM_Doc'])!=""){				$stock[] = array("trama_vm_doc"					=> 	trim($v['Trama_VM_Doc']));}
				if(trim($v['Saldos_VM_Doc'])!=""){				$stock[] = array("saldos_vm_doc"	   			=> 	trim($v['Saldos_VM_Doc']));}
				if(trim($v['Devoluciones_VM_Doc'])!=""){		$stock[] = array("devoluciones_vm_doc" 			=> 	trim($v['Devoluciones_VM_Doc']));}
				if(trim($v['RInterior_JA_Doc'])!=""){			$stock[] = array("rinterior_ja_doc"				=> 	trim($v['RInterior_JA_Doc']));}
				if(trim($v['ProductosTerminados_AG_Doc'])!=""){	$stock[] = array("productosterminados_ag_doc"	=> 	trim($v['ProductosTerminados_AG_Doc']));}
				if(trim($v['Semielaborado_AG_Doc'])!=""){		$stock[] = array("semielaborado_ag_doc"			=> 	trim($v['Semielaborado_AG_Doc']));}
				if(trim($v['Reproceso_AG_Doc'])!=""){			$stock[] = array("reproceso_ag_doc"				=> 	trim($v['Reproceso_AG_Doc']));}
				if(trim($v['Calcetines_AM_Doc'])!=""){			$stock[] = array("calcetines_am_doc"			=> 	trim($v['Calcetines_AM_Doc']));}
				if(trim($v['Devoluciones_AM_Doc'])!=""){		$stock[] = array("devoluciones_am_doc"			=>	trim($v['Devoluciones_AM_Doc']));}
				if(trim($v['Glamour_SE_Doc'])!=""){				$stock[] = array("glamour_se_doc"				=> 	trim($v['Glamour_SE_Doc']));}
				if(trim($v['Taller_Externo_Dev_Doc'])!=""){		$stock[] = array("taller_externo_dev_doc"		=>	trim($v['Taller_Externo_Dev_Doc']));}
	//		);
		}
	return $stock;
	}
}
?>

