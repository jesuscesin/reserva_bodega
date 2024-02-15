<?php


// Iniciar la sesión
session_start();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio de sesión
header("Location: index.html");
exit();

?>