<?php

ini_set('max_execution_time', 300);
ini_set("default_socket_timeout", 60);

function consultaseq(){

    $url  = "http://10.20.0.94/desa/ptt/transferencia/WS_totvs_mch.php";
    $curl  = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $exec  = curl_exec($curl);
    curl_close($curl); 
    //echo $url;
    //echo $exec;
    return $exec; 
}

//consultaseq();


?>