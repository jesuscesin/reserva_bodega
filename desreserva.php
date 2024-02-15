<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Embarques</title>




    <!-- Importar la biblioteca xlsx desde un CDN -->
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="estilos.css?date=<?php echo date('h:i:s');?>">
    <script src="https://kit.fontawesome.com/f0f86f4b44.js" crossorigin="anonymous"></script>


</head>

<body>

<div id='loading-modal' class='loading-modal'>
  <div class='loading-content'>
    <i class='fas fa-spinner fa-spin'></i>
    <h1>Cargando...</h1>
  </div>
</div>


<?php flush(); ?>
 <?php


//define('TABLA_PAC', 'T_FACTURASPAC2');

include("conex.php");
include("actualizar_estado.php");
          
$Gtabla_conexion = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.100.110.79)(PORT=1521)))(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=TOTVS)))';
$conn           = ocilogon('TOTVS12C', 'TOTVS12C', $Gtabla_conexion);


$nombre = $_GET['usuario'];
//echo "sesion:";
//echo $nombre;


// Consulta para obtener las opciones de embarque
    $query = "SELECT DISTINCT N_EMBARQUE, FECHA_EMB, N_CARPETA, FECHA_CARP, ESTADO FROM ".TABLA_PAC." order by TO_NUMBER(N_EMBARQUE) desc ";
    echo $query;
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    echo "<div class='tabla-container'>";
    echo "<table  id='codigoz'>";
    echo "<thead>"; 
    echo "<tr>";
        echo "<th>N</th>";
        echo "<th>N FACTS</th>";
        echo "<th>N PARTES</th>";
        echo "<th>RESUMEN</th>";
        echo "<th>FECHA CARGA</th>";
        echo "<th>N CARPETA</th>";
        echo "<th>FECHA CARP</th>";
        echo "<th>ESTADO</th>";
        echo "<th>DETALLE</th>";
        echo "<th>N ESTADO</th>";
    echo "</tr>";
    echo "</thead>"; 

    while ($row = oci_fetch_assoc($stmt)) {

        $fecha_emb = substr($row["FECHA_EMB"], 0, 4) . "-" . substr($row["FECHA_EMB"], 4, 2) . "-" . substr($row["FECHA_EMB"], 6, 2);
        $fecha_carp = substr($row["FECHA_CARP"], 0, 4) . "-" . substr($row["FECHA_CARP"], 4, 2) . "-" . substr($row["FECHA_CARP"], 6, 2);

            echo "<tr>
                <td class='text-right'> <a href='facturaspac_result.php?num=".intval($row["N_EMBARQUE"])."&usuario=".$nombre."' target='main-zone'>".$row["N_EMBARQUE"]."</a></td>";

                $query5 = "SELECT COUNT(DISTINCT FACTURA) AS FACTURAS, COUNT( N_PARTE) AS NPARTE FROM T_FACTURASPAC2 WHERE  N_EMBARQUE = ".$row["N_EMBARQUE"]."
                AND N_PARTE IS NOT NULL";
                $stmt_5 = oci_parse($conn, $query5);
                oci_execute($stmt_5);
                //echo $query5;

                $row2 = oci_fetch_assoc($stmt_5);
                $cant_fact = $row2["FACTURAS"];
                $cant_np = $row2["NPARTE"];


                // Realizar una consulta SQL para obtener las facturas correspondientes
                $queryFacturas = "SELECT distinct FACTURA FROM T_FACTURASPAC2 WHERE N_EMBARQUE = ".intval($row["N_EMBARQUE"])." order by factura asc";
                $stmt_6 = oci_parse($conn, $queryFacturas);
                oci_execute($stmt_6);

                // Variable para almacenar los resultados separados por gui�n
                $resultados = array();

                while ($row6 = oci_fetch_assoc($stmt_6)) {
                    $resultados[] = $row6["FACTURA"];
                }

                // Imprimir los resultados separados por gui�n
                //echo implode(' - ', $resultados);



            echo"<td>".implode(' - ', $resultados)."</td>
                <td class='text-right'>".$cant_np."</td>
                <td class='text-center'><a href='resumen_carga.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-file-lines'></i></td>
                <td class='text-center'>".$fecha_emb."</td>   
                <td class='text-center'> <a href='facturaspac_result_carpeta.php?carpeta=". $row["N_CARPETA"]. "' class='popup-link' >".$row["N_CARPETA"]."</a></td>
                <td>".$fecha_carp."</td>   
                <td>".$row["ESTADO"]."</td>

                <td>";     
            

           
                $query_comentarios = "SELECT COUNT(*) AS NUM_COMENTARIOS FROM T_FACTURASPAC2 WHERE COMENTARIO <> ' ' AND N_embarque = ".$row["N_EMBARQUE"];
                $stmt_comentarios = oci_parse($conn, $query_comentarios);
                oci_execute($stmt_comentarios);
                $num_comentarios = oci_fetch_assoc($stmt_comentarios)["NUM_COMENTARIOS"];


                $query_estados = "SELECT MAX(ESTADO) AS ESTADO FROM T_FACTURASPAC2 WHERE  N_embarque = ".$row["N_EMBARQUE"];
                $stmt_estados = oci_parse($conn, $query_estados);
                oci_execute($stmt_estados);
                $estado = trim(oci_fetch_assoc($stmt_estados)["ESTADO"]);
                //echo $estado;

                $query_carpeta = "SELECT N_CARPETA AS CARPETA FROM T_FACTURASPAC2 WHERE  N_embarque = ".$row["N_EMBARQUE"];
                $stmt_carpeta = oci_parse($conn, $query_carpeta);
                oci_execute($stmt_carpeta);
                $carpeta = trim(oci_fetch_assoc($stmt_carpeta)["CARPETA"]);

                //echo $num_comentarios;

                // Mostrar el �cono y enlazar seg�n EL ESTADO
                if ($estado == 'CON ERRORES - SIN REVISAR'){
                    echo "<a href='diferencias_embarques.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-file-circle-question'></i></a>";
                }elseif($estado == 'CON ERRORES - EN REVISION'){
                    echo "<a href='diferencias_embarques.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-file-pen'></i></a>";
                }elseif($estado == 'SIN ERRORES' and $carpeta == null || trim($carpeta) == ''){
                    echo "<a href='crea_carpeta.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-file-circle-check'></i></a>"; 
                }elseif($estado == 'SIN ERRORES' and $carpeta != null || trim($carpeta) == ''){
                    echo "<a href='diferencias_embarques.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-file-circle-check'></i>"; 
                }elseif ($estado == 'SIN ERRORES - CORREGIDO' and $carpeta != null || trim($carpeta) == '') {
                    echo "<a href='crea_carpeta.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-file-circle-exclamation'></i>";
                }elseif ($estado == 'SIN ERRORES - CORREGIDO' and $carpeta == null || trim($carpeta) == '') {
                    echo "<a href='crea_carpeta.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-file-circle-exclamation'></i></a>";
                }elseif($estado == 'CARGA SIN ERRORES' and $carpeta != null || trim($carpeta) == ''){
                    echo "<i class='fa-solid fa-folder-open' '></i>";
                }elseif($estado == 'CARGA - CORREGIDA' and $carpeta != null || trim($carpeta) == ''){
                    echo "<a href='diferencias_embarques.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-folder-open' '></i>"; 
                }elseif($estado == 'CARPETA CERRADA' and $carpeta != null || trim($carpeta) == ''){
                    echo "<a href='diferencias_embarques.php?num=".$row["N_EMBARQUE"]."' class='popup-link'><i class='fa-solid fa-folder-closed' ></i>"; 
                } else {
                }
                echo "</td>";
                
                $query6 = "SELECT DBA_OK AS N_ESTADO FROM DBA020 WHERE DBA_HAWB = '".$row["N_CARPETA"]."'";
                $stmt_6 = oci_parse($conn, $query6);
                oci_execute($stmt_6);

                //echo $query6;

                $row3 = oci_fetch_assoc($stmt_6);
                if (isset($row3["N_ESTADO"])){
                    $estado_carpeta = $row3["N_ESTADO"];
                }else{
                    $estado_carpeta = ' ';
                }
                

            echo"<td>".$estado_carpeta."</td>
            </tr>";
        }
    echo "</table>";
    echo "</div>";


    oci_free_statement($stmt);
    oci_close($conn);
?>

        </select>
        </div>

        </div>


    <iframe name="main-zone" scrolling="auto" class="main-iframe"  title="main-zone">
    </iframe>
<script>
    // Funci�n para abrir una ventana emergente
    function openPopup(url) {
        window.open(url, 'popup', 'width=800,height=600,scrollbars=yes,resizable=yes');
    }

    // Obtener todos los enlaces con la clase "popup-link"
    var popupLinks = document.querySelectorAll('.popup-link');

    // Agregar un evento de clic a cada enlace
    popupLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
            var url = this.getAttribute('href'); // Obtener la URL del enlace
            openPopup(url); // Abrir la ventana emergente con la URL
        });
    });




</script>

<script>

    
// Funci�n para ocultar el modal de carga una vez que la p�gina se haya cargado completamente
window.addEventListener("load", function() {
    const loadingModal = document.getElementById("loading-modal");
    loadingModal.style.display = "none";
});

// Llamar a showLoadingModal() antes de realizar la carga
showLoadingModal();
</script>


</body>
</html>











