<?php

ini_set('max_execution_time', 300);
ini_set("default_socket_timeout", 60);


function consultaseq()
{
    /*
    try {

        $client=new SoapClient('http://192.168.100.153:8095/ws0101/NUMSEQ_MAS_UNO.apw?WSDL',array("trace" => 1, "exception" => 0));
        $result = $client->CONSULTA_NUMSEQ();
    //  echo "ID ESTADO: " . $result->CONSULTA_NUMSEQRESULT->STRUCSEQ->CESTADO . "<br>";
    //  echo "DESCRIPCION: " . $result->CONSULTA_NUMSEQRESULT->STRUCSEQ->CDESCRI . "<br>";
    //  echo "SECUENCIA: " . $result->CONSULTA_NUMSEQRESULT->STRUCSEQ->CSEQUEN . "<br>";

    } catch ( SoapFault $e ) {

        echo $e->getMessage();
        echo PHP_EOL;
        
    }
    */
    return(['OK','OK','TEST01']);

}

 
?>