<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservas de Stock</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <script src="https://kit.fontawesome.com/7b82ce3d1c.js" crossorigin="anonymous"></script>
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js"></script>
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- InputMask -->
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>
  <!--  Javascript -->
    <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
    <script src="js/editar_reserva.js" type="text/javascript"></script>
</head>
<body>
    

<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
include('send_mail.php');
include('WS_totvs_mch.php');
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";

// Recibir variables por GET
$recno          = $_GET['recno'];
$reserva        = $_GET['reserva'];
$os             = $_GET['os'];
$requerimiento  = $_GET['requerimiento'];
$np		        = $_GET['articulos'];
$cantidad       = $_GET['cantidad'];
$compra         = $_GET['compra'];



echo "<form action='guardar_cambios.php' method='post' onsubmit='return validateForm()'>

  <div class='tabla-container2'>    
  <table  id='editar' >


    <tr>
        <th colspan='3'>INFO RESERVA</th>
    </tr>
    <tr>
        <th>Reserva</th>
        <td><input class='input_formulario' type='text' name='reserva' id='reserva' value='".$reserva."' readonly></td>
    </tr>
    <tr>
        <th>OS</th>
        <td><input class='input_formulario' type='text' name='os' id='os' value='".$os."' readonly></td>
    </tr>
    <tr>
        <th>Requerimiento</th>
        <td><input class='input_formulario' type='text' name='requerimiento' id='requerimiento' value='".$requerimiento."' readonly></td>
	</tr>
    <tr>
        <th>N/P</th>
        <td><input class='input_formulario' type='text' name='np' id='np' value='". $np."' readonly></td>
	</tr>
	<tr>
	<th>Cantidad</th>
	<td><input class='input_formulario' type='text' name='cantidad' id='cantidad' value='". $cantidad."' readonly></td>
  	</tr>
	<tr>
	<th>Compra</th>
	<td><input class='input_formulario' type='text' name='compra' id='compre' value='". $compra."' readonly></td>
  	</tr>
	<tr>
	<th>NUEVA CANTIDAD DE LA RESERVA</th>
	<td colspan='4'><input type='number' name='cant_new' required></td>
	</tr>
<tr>
<td colspan='5'><input type='submit' class='boton_guardar' value='Guardar cambios' id='submitButton'></td>
</tr>

</table>
</div>

<input type='hidden' name='reserva' value='".$reserva."'>
<input type='hidden' name='os' value='".$os."'>
<input type='hidden' name='requerimiento' value='".$requerimiento."'>
<input type='hidden' name='np' value='".$np."'>
<input type='hidden' name='cantidad' value='".$cantidad."'>
<input type='hidden' name='compra' value='".$compra."'>
<input type='hidden' name='recno' value='".$recno."'>



</form>" ;
?>

</body>
</html>