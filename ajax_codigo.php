<?php
include_once "consulta.class.php";
$codigo = new b_codigo();
echo json_encode($codigo->buscar_articulo($_GET['term']));
?>