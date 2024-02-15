<?php
error_reporting(E_ALL);
/**
  *  Tablas MYSQL
  */
define('USUARIOS','PORTAL_USUARIOS');
define('MENU','PORTAL_MENU');
define('PERMISOS','PORTAL_PERMISOS');




/**
  *  Tablas totvs 
  */
define('TBL_COMPRA_DICOTEX','TOTVS.Z2B_XCOMPRA');
define('TBL_SC5','TOTVS.SC5010');
define('TBL_SC6','TOTVS.SC6010');
define('TBL_SC9','TOTVS.SC9010');
define('TBL_SA1','TOTVS.SA1010');
define('TBL_SB1','TOTVS.SB1010');
define('TBL_ACY','TOTVS.ACY010');
define('TBL_ZEQ','TOTVS.ZEQ010');
define('TBL_ZC6010','TOTVS.ZC6010');
// define('TBL_ZC6010','TOTVS.ZZ6010');
/**
  *  arreglos con valores de campos estaticos 
  */
$ec     = array('S'=>'SOLTERO(A)','C'=>'CASADO(A)','E'=>'SEPARADO(A)','V'=>'VIUDO(A)','0'=>'NO INFORMADO');
$conta  = array('N'=>'Concepto General','S'=>'Concepto Adicional');
$tc     = array('P'=>'PERMANENTE','F'=>'PLAZO FIJO','O'=>'OBRA O FAENA');
$tc1    = array('G'=>'PERMANENTE','PH'=>'PART TIME HORAS','PD'=>'PART TIME DIAS');

/**
  *  Ruta archivos excel ventas tiendas clientes 
  */
define('PATH_XLS','xlsVentas/');
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');


function envia_correo($to, $asunto, $msj, $adjunto,$adjunto2) {
  global $conexion;

  $mail = new PHPMailer();        // crea un nuevo object
  $mail->CharSet = 'UTF-8';
  $mail->IsSMTP();                // habilita SMTP
  $mail->SMTPDebug = 0;           // depuración: 1 = errores y messages, 2 = mensajes solamente
  $mail->Debugoutput = 'html';
  $mail->SMTPAuth = true;         // autenticación habilitada
  $mail->SMTPSecure = 'ssl';      // transferencia segura habilitada requerida por Gmail
  $mail->Host = 'smtp.gmail.com';
  $mail->Port = 465;
  $mail->Username = C_USER_MCH; 
  $mail->Password = C_PASS_MCH;
  
  $mail->SetFrom(FROM_MCH, FROM_NAME_MCH);  //remitente
  $mail->Subject = $asunto;          //asunto
  if(is_array($to)){
      foreach($to as $destinatario){
      $mail->AddAddress($destinatario);             //destinatario
      //echo "ENVIANDO MAIL A: ".$destinatario."<br>\n";
      }
  }else{
      $mail->AddAddress($to);
  }
  
  if($adjunto <> ''){
      $mail->addAttachment($adjunto);     //archivo adjunto
  }    
if($adjunto2 <> ''){
      $mail->addAttachment($adjunto2);     //archivo adjunto
  }
  
  $mail->Body = $msj;                //cuerpo mensaje
  $mail->AltBody = $msj;             //cuerpo mensaje no html

  if($mail->Send()){
      return true;
  }else{
      echo "Mailer Error: " . $mail->ErrorInfo ."<br>";
  }
}




/**
  *  funciones genéricas
  * 
  */
function formatDate($cadena){
    //global $conexion;
    if($cadena<>''){
        return substr($cadena,6,2).'/'.substr($cadena,4,2).'/'.substr($cadena,0,4);
    }
}
function formatDateSave($cadena){
    if($cadena<>''){
        return substr($cadena,4,4).substr($cadena,2,2).substr($cadena,0,2);
    }
}
function formatRut( $rut ) {
  return number_format( substr ( $rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen($rut) -1 , 1 );
}
//mail
define('C_USER',    'informatica@promer.cl');
define('C_PASS',    'promer2016');
define('FROM',      'informatica@promer.cl');
define('FROM_NAME', 'Informática Promer');
define('FIRMA',     '<br><br>Este mensaje se ha generado automaticamente, por favor NO RESPONDER.<br><br>Atte.<br>Informática Grupomonarch.');
define('MSJNUEVOUSER','
        Ud. ha sido registrado como usuario del sistema de Recepción, Trazabilidad y Control de Pedidos de Grupo Empresas Monarch.<br><br>
        Sus credenciales son las siguientes:<br>');


///////////////////////////////////////////////////////////////////////////////////////////////////
//CORREO MONARCH
define('C_USER_MCH',    'informatica@grupomonarch.cl');
define('C_PASS_MCH',    'informatica1234');
define('FROM_MCH',      'informatica@grupomonarch.cl');
define('FROM_NAME_MCH', 'Informatica Monarch');
define('FIRMA_MCH',     '<br><br>Este mensaje se ha generado automaticamente, por favor NO RESPONDER.<br><br>Atte.<br>InformÃ¡tica Grupomonarch.');
define('MSJNUEVOUSER_MCH','
        Ud. ha sido registrado como usuario del sistema de RecepciÃ³n, Trazabilidad y Control de Pedidos de Grupo Empresas Monarch.<br><br>
        Sus credenciales son las siguientes:<br>');        
?>