<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "config.php";
require_once "conexion.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}

function ver_listado($empresa, $inicio, $fin){	
	global $tipobd_totvs,$conexion_totvs;

    //CASO EN EL QUE NO SE FILTRA POR FECHAS (TODAS LAS ORDENES DE COMPRA)
	if(((empty($inicio) && empty($fin)))){
		$querysel = "SELECT OCE_PROVDESC AS PROVEEDOR,
					        OCE_PROVCOD AS RUT,
                            OCE_CONTACTO AS CONTACTO,
                            OCE_SOLICI AS SOLICITANTE,
                            OCE_FCHEMIS AS FECHA,
                            OCE_NUMORD AS NUMERO,
                            OCE_ESTADO AS ESTADO
				    FROM OC_ENCABE
                    WHERE OCE_ACTIVO = 'no'
                    ORDER BY OCE_NUMORD DESC";

	}else if((empty($inicio) && $fin == true)){//CASO EN EL QUE EXISTE UNA FECHA LIMITE (DESDE LA MAS ANTIGUA HASTA LE FECHA LIMITE)
		$querysel = "SELECT OCE_PROVDESC AS PROVEEDOR,
                            OCE_PROVCOD AS RUT,
                            OCE_CONTACTO AS CONTACTO,
                            OCE_SOLICI AS SOLICITANTE,
                            OCE_FCHEMIS AS FECHA,
                            OCE_NUMORD AS NUMERO,
                            OCE_ESTADO AS ESTADO
                     FROM OC_ENCABE
                     WHERE OCE_ACTIVO = 'no'
                     and OCE_FCHEMIS <= '$fin'
                     ORDER BY OCE_NUMORD DESC";

	}else if(($inicio == true && empty($fin))){//CASO EN EL QUE EXISTE UNA FECHA DE INICIO (DESDE LA FECHA DE INICIO A LA MAS RECIENTE)
        $querysel = "SELECT OCE_PROVDESC AS PROVEEDOR,
                            OCE_PROVCOD AS RUT,
                            OCE_CONTACTO AS CONTACTO,
                            OCE_SOLICI AS SOLICITANTE,
                            OCE_FCHEMIS AS FECHA,
                            OCE_NUMORD AS NUMERO,
                            OCE_ESTADO AS ESTADO
                     FROM OC_ENCABE
                     WHERE OCE_ACTIVO = 'no'
                     AND OCE_FCHEMIS >= '$inicio'
                     ORDER BY OCE_NUMORD DESC";

    } else{//CASO EN EL QUE EXISTE UN INUCIO Y UN LIMITE EN EL FILTRO DE CUSQUEDA POR FECHAS (DESDE LA FECHA DE INICIO HASTA FECHA LIMITE)
        $querysel = "SELECT OCE_PROVDESC AS PROVEEDOR,
                            OCE_PROVCOD AS RUT,
                            OCE_CONTACTO AS CONTACTO,
                            OCE_SOLICI AS SOLICITANTE,
                            OCE_FCHEMIS AS FECHA,
                            OCE_NUMORD AS NUMERO,
                            OCE_ESTADO AS ESTADO
                    FROM OC_ENCABE
                    WHERE OCE_ACTIVO = 'no'
                    AND OCE_FCHEMIS >= '$inicio'
                    and OCE_FCHEMIS <= '$fin'
                    ORDER BY OCE_NUMORD DESC";
    }
	$rss = querys($querysel,$tipobd_totvs,$conexion_totvs);
	while($v = ver_result($rss,$tipobd_totvs)){
        $numero = $v["NUMERO"];
		$cargar[]=array(
					"PROVEEDOR"  	=>trim($v["PROVEEDOR"]),
					"RUT" 		    =>formatRut($v["RUT"]),
					"CONTACTO" 		=>$v["CONTACTO"],
					"SOLICITANTE"   =>$v["SOLICITANTE"],
					"FECHA"	        =>formatDate($v["FECHA"]),
					"NUMERO"	    =>$v["NUMERO"],
                    "BTNPDF"        =>"<a target='_blank'  class='btn btn-block btn-outline-success btn-sm' style='color: black; border-color: black;' href='genera_pdf.php/?num=$numero'> <span class='far fa-file-pdf'></span> N° $numero</a>",
                    "ESTADO"        =>$v["ESTADO"]                                                                                                                
		);
	}
	echo json_encode($cargar);
}

if(isset($_GET["ver"])){   
    $empresa  =  $_GET["empresa"];
    $inicio = $_GET["inicio"];
    $fin = $_GET["fin"];
    ver_listado($empresa, $inicio, $fin);
}

?>