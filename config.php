<?php

// Base de datos
// Estas variables deben editarse con los datos del servidor de destino

define('DB_HOST', ''); // SERVIDOR MySQL
define('DB_NAME', ''); // NOMBRE DE LA BASE DE DATOS
define('DB_USER', ''); // USUARIO
define('DB_PASS', ''); // CONTRASEÑA

// Directorios
// Modificar solo si se va a alterar la estructura de directorios descrita en el manual de despliegue

// Raíz del proyecto
define('RAIZ_PATH', __DIR__);

// Directorio carpeta R
define('CSV_PATH', RAIZ_PATH . DIRECTORY_SEPARATOR . 'R' . DIRECTORY_SEPARATOR);

?>
