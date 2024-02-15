<?php  
require_once "conexion.php";
require_once "config.php";

class b_codigo
{
	public function buscar_articulo($zarticulo)
	{
        $zarticulo = strtoupper($_GET['term']);
        global $tipobd_totvs_dev,$conexion_totvs_dev;
            $xcodigo = array();
            $sql="select b1_cod,b1_desc from totvs.sb1010 where 
                    b1_cod like '%$zarticulo%' and 
                    b1_desc not in ('*******   NO USAR   *******','NO USAR') and 
                    D_E_L_E_T_ <> '*'
                    ORDER BY SUBSTR(B1_COD,10,5) asc";
            $result=querys($sql,$tipobd_totvs_dev,$conexion_totvs_dev);
            while($v=ver_result($result, $tipobd_totvs_dev))
            {
                $xcodigo[] = Array("value" => trim($v['B1_COD']),
                        "descrip" => utf8_encode(trim(ucwords(strtolower($v['B1_DESC'])))));
            }
        return $xcodigo;
	}

}

?>