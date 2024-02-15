<?php
global $dev_tipo;
$Global_titulo='Generador Pie de Firma';
$Global_logo_empresa='';
$Global_logo_producto='gestornet.png';
$Global_maxlin=4000;
//$Global_logo_producto='logo-opendte.jpg';
$dev_tipo=trim($_SESSION['ses_devtipo']);
// echo "fev_tipo :".$dev_tipo;

$Global_css='blue.css';
$Global_tema='menu_blue.css';
$Global_twokey=true;
date_default_timezone_set('America/Santiago');
if($dev_tipo=='movil')
{
  $Global_css='mv_blue.css';
  $Global_tema="mv_menu_blue.css";
}
?>
