<?php
require_once "../conexion.php";
require_once "../config.php";

function actualiza_estados(){
    global $tipobd_totvs, $conexion_totvs;
    
    $querysel = "SELECT OCD_PEDIDO AS PEDIDO FROM ZTMP_OCDIC 
                WHERE OCD_STATUS='30'
                GROUP BY OCD_PEDIDO
                ORDER BY OCD_PEDIDO";
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    while($v = ver_result($rss, $tipobd_totvs)){
        
        $pedido = $v['PEDIDO'];
        $querysel_2 ="SELECT distinct D2_ITEMPV,d2_doc,D2_DTDIGIT, D2_SERIE  FROM SD2010
                    WHERE D2_FILIAL='01'
                    AND D2_PEDIDO='$pedido'
                    AND D_E_L_E_T_<>'*'
                ORDER BY NLSSORT(D2_ITEMPV,'NLS_SORT=BINARY_AI')";
        $v2=querys($querysel_2, $tipobd_totvs, $conexion_totvs);
        $d2_doc='';
        
        
        while ($vx=ver_result($v2, $tipobd_totvs)){
            
            //$d2_doc=$d2_doc.substr(trim($vx['D2_DOC']),2,12).",";
            $item       = $vx["D2_ITEMPV"];
            $d2_doc     = $vx["D2_DOC"];
            $serie      = $vx["D2_SERIE"];
            $d2_digit   = $vx["D2_DTDIGIT"];
 
        //$d2_doc=trim($d2_doc,',');
            if ($d2_doc<>''){
             //UPDATE ZC6   
            $queryup="update ZC6010 set ZC6_NOTA='$d2_doc', ZC6_SERIE='$serie', ZC6_DATFAT='$d2_digit'  where ZC6_NUM='$pedido' AND Z6_ITEM='$item'";
            $r2=querys($queryup, $tipobd_totvs, $conexion_totvs);
            echo "QUERYUP : ".$queryup."<br>";
            $queryup_2="update ZC6010 set Z6_OKDIGI='F' where ZC6_NUM='$pedido'";
            $r2=querys($queryup_2, $tipobd_totvs, $conexion_totvs);
            echo "QUERYUP 2 : ".$queryup_2."<br>";
            //UPDATE ZTMP_OCDIC
            $queryup_3 = "update ZTMP_OCDIC set OCD_STATUS='40' where OCD_PEDIDO='$pedido'";
            $r2=querys($queryup_3, $tipobd_totvs, $conexion_totvs);
            echo "QUERYUP 2 : ".$queryup_3."<br>";
			
			$queryup_4 = "update Z2B_XCOMPRA set B2D_STATUS='09' where B2D_PEDIDO='$pedido'";
            $r2=querys($queryup_4, $tipobd_totvs, $conexion_totvs);
            echo "QUERYUP 2 : ".$queryup_4."<br>";
            }
        }
    }

    
}
 actualiza_estados();

?>