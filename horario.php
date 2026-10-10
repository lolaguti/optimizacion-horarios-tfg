<?php

require_once 'config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
    $usuario = DB_USER;
    $contrasena = DB_PASS;

    $pdo = new PDO($dsn, $usuario, $contrasena);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$id_estudiante = intval($_GET['id']);

$sql = "SELECT e.id_grado, a.nombre_asig, a.cuatri_asig, hn.dia, hn.hora
        FROM estudiantes e
	        JOIN rel_est_asig rea ON e.id_est = rea.id_est
          JOIN asignaturas a ON rea.id_asig = a.id_asig
        LEFT JOIN horario_nuevo hn ON a.id_asig = hn.id_asig
        WHERE e.id_est =" . $id_estudiante;

  $stmt = $pdo->query($sql);

  $horario_est = [];

  while ($fila = $stmt->fetch()) {
    $horario_est[] = $fila;
  }

  $grado = $horario_est[0]['id_grado'];

  $sql2 = "SELECT a.nombre_asig, a.cuatri_asig, hn.dia, hn.hora
          FROM asignaturas a
          JOIN horario_nuevo hn ON a.id_asig = hn.id_asig
          JOIN rel_grado_asig rga ON rga.id_asig = a.id_asig
          WHERE rga.id_grado =" . $grado; 
  
  $stmt = $pdo->query($sql2);

  $asig_grado = [];

  while ($fila = $stmt->fetch()) {
    $asig_grado[] = $fila;
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Portal de Servicios</title>
  <link rel="stylesheet" href="horario_style.css">
  <script src="horario_script.js" defer></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>

  <script>
    const horario_est =<?php echo json_encode($horario_est);?>;
    const asig_grado =<?php echo json_encode($asig_grado);?>;
  </script>

  <header>
    <aside class="lado_izq" id="header_izq">
      <img src="imagenes/logo_uex.png" alt="Logo_UEX">
    </aside>

    <main class="lado_centro" id="header_centro">
      <h1>PORTAL DE SERVICIOS</h1>
    </main>
  </header>

  <div class="menu"> 

    <!-- MENU IZQUIERDA -->
    <aside id="menu_izq" class="lado_izq">
      <div class="lista_izq">
        <img src="imagenes/iconoA.png">
        <p>Datos personales y laborales</p> 
      </div>
      <div class="lista_izq">
        <img src="imagenes/iconoB.png">
        <p>Docencia</p>
      </div>
      <div class="lista_izq">
        <img src="imagenes/iconoC.png">
        <p>Datos Académicos</p>
      </div>
      <div class="lista_izq">
        <img src="imagenes/iconoD.png">
        <p>Matrícula</p>
      </div>
      <div class="lista_izq">
        <img src="imagenes/iconoE.png">
        <p>Oferta académica/educativa</p>
      </div>
      <div class="lista_izq">
        <img src="imagenes/iconoF.png">
        <p>Horario de clase</p>
      </div>
    </aside>

    <!-- MENU CENTRO -->
    <main id="menu_centro" class="lado_centro">
      
      <div id="select_cuatrimestre">
      </div> 
      <div id="horario">
      </div>

    </main>
    
  </div>

  <footer>
    <p id="p_izq">Universidad de Extremadura</p>
    <p id="p_der">Portal de Servicios (V. 4.4.0) | Todos los derechos reservados</p>
  </footer>

</body>
</html>
