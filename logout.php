<?php


// Iniciar la sesi�n
session_start();

// Destruir la sesi�n
session_destroy();

// Redirigir al usuario a la p�gina de inicio de sesi�n
header("Location: index.html");
exit();

?>