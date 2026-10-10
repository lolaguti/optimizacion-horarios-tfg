<?php

require_once 'config.php';

// Conexión a la base de datos
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
$usuario = DB_USER;
$contrasena = DB_PASS;

$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $usuario, $contrasena, $options);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
//Funcion par
function es_par($num) {
  return ($num % 2) === 0;
}

// asig_cuatri
$asig_cuatri = [];
$query = 'SELECT id_asig, cuatri_asig FROM asignaturas';
foreach ($pdo->query($query) as $fila) {
  $asig_cuatri[$fila['id_asig']] = $fila['cuatri_asig'];
}

$ruta_csv = CSV_PATH;

//CSV est_asig
$fp_est_par = fopen($ruta_csv . 'est_asig_par.csv', 'w');
$fp_est_imp = fopen($ruta_csv . 'est_asig_impar.csv', 'w');
fputcsv($fp_est_par, ['id_est', 'id_asig']);
fputcsv($fp_est_imp, ['id_est', 'id_asig']);

$stmt_est = $pdo->query('
  SELECT e.id_est, rea.id_asig
  FROM estudiantes e
  JOIN rel_est_asig rea ON e.id_est = rea.id_est');

while ($fila = $stmt_est->fetch()) {
    $id_est = $fila['id_est'];
    $id_asig = $fila['id_asig'];
    $cuatri = $asig_cuatri[$id_asig] ?? null;
    if ($cuatri === null) continue;

    if (es_par($cuatri)) {
        fputcsv($fp_est_par, [$id_est, $id_asig]);
    } else {
        fputcsv($fp_est_imp, [$id_est, $id_asig]);
    }
}

fclose($fp_est_par);
fclose($fp_est_imp);

//CSV prof_asig
$fp_prof_par = fopen($ruta_csv . 'prof_asig_par.csv', 'w');
$fp_prof_imp = fopen($ruta_csv . 'prof_asig_impar.csv', 'w');
fputcsv($fp_prof_par, ['id_prof', 'id_asig']);
fputcsv($fp_prof_imp, ['id_prof', 'id_asig']);

$stmt_prof = $pdo->query('
  SELECT p.id_prof, rpa.id_asig
  FROM profesores p
  JOIN rel_prof_asig rpa ON p.id_prof = rpa.id_prof');

while ($fila = $stmt_prof->fetch()) {
    $id_prof = $fila['id_prof'];
    $id_asig = $fila['id_asig'];
    $cuatri = $asig_cuatri[$id_asig] ?? null;
    if ($cuatri === null) continue;

    if (es_par($cuatri)) {
        fputcsv($fp_prof_par, [$id_prof, $id_asig]);
    } else {
        fputcsv($fp_prof_imp, [$id_prof, $id_asig]);
    }
}

fclose($fp_prof_par);
fclose($fp_prof_imp);

//CSV grado_asig
$fp_grado_par = fopen($ruta_csv . 'grado_asig_par.csv', 'w');
$fp_grado_imp = fopen($ruta_csv . 'grado_asig_impar.csv', 'w');
fputcsv($fp_grado_par, ['id_grado', 'id_asig']);
fputcsv($fp_grado_imp, ['id_grado', 'id_asig']);

$stmt_grado = $pdo->query('
  SELECT g.id_grado, rga.id_asig
  FROM grados g
  JOIN rel_grado_asig rga ON g.id_grado = rga.id_grado');

while ($fila = $stmt_grado->fetch()) {
  $id_grado = $fila['id_grado'];
  $id_asig = $fila['id_asig'];
  $cuatri = $asig_cuatri[$id_asig] ?? null;

  if ($cuatri === null) continue;

  if (es_par($cuatri)) {
    fputcsv($fp_grado_par, [$id_grado, $id_asig]);
  } else {
    fputcsv($fp_grado_imp, [$id_grado, $id_asig]);
  }
}

fclose($fp_grado_par);
fclose($fp_grado_imp);

//CSV asig_cuatri
$fp_asig_par = fopen($ruta_csv . 'asig_cuatri_par.csv', 'w');
$fp_asig_imp = fopen($ruta_csv . 'asig_cuatri_impar.csv', 'w');
fputcsv($fp_asig_par, ['id_asig', 'cuatri']);
fputcsv($fp_asig_imp, ['id_asig', 'cuatri']);

foreach ($asig_cuatri as $id_asig => $cuatri) {
  if (es_par($cuatri)) {
    fputcsv($fp_asig_par, [$id_asig, $cuatri]);
  } else {
    fputcsv($fp_asig_imp, [$id_asig, $cuatri]);
  }
}

fclose($fp_asig_par);
fclose($fp_asig_imp);

// CSV Asignaturas que deben coincidir
$fp_coinciden = fopen($ruta_csv . 'asig_coinciden.csv', 'w');
fputcsv($fp_coinciden, ['id_asig1', 'id_asig2']);

$pares_escritos = [];
$stmt_coinciden = $pdo->query('SELECT id_asig1, id_asig2 FROM asig_dobles');

while ($fila = $stmt_coinciden->fetch()) {
  $id1 = $fila['id_asig1'];
  $id2 = $fila['id_asig2'];
  $clave = ($id1 < $id2) ? "$id1-$id2" : "$id2-$id1";

  if (!isset($pares_escritos[$clave])) {
    fputcsv($fp_coinciden, [$id1, $id2]);
    $pares_escritos[$clave] = true;
  }
}

fclose($fp_coinciden);

// tabla horario_nuevo
$pdo->exec('DROP TABLE IF EXISTS horario_nuevo');

$pdo->exec('
  CREATE TABLE horario_nuevo (
    id_asig INT NOT NULL,
    dia INT NOT NULL,
    hora INT NOT NULL,
    PRIMARY KEY (id_asig, dia, hora),
    CONSTRAINT fk_horario_asignatura
        FOREIGN KEY (id_asig)
        REFERENCES asignaturas(id_asig)
        ON DELETE CASCADE
        ON UPDATE CASCADE
  ) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci');

//Insertar datos
$archivo_horario = $ruta_csv . 'solucion_horario.csv';

if (($fp_horario = fopen($archivo_horario, 'r')) !== false) {
  $insert_stmt = $pdo->prepare('
    INSERT INTO horario_nuevo (id_asig, dia, hora)
    VALUES (?, ?, ?)');

  fgetcsv($fp_horario);

  while (($datos = fgetcsv($fp_horario)) !== false) {

    //control filas "malas"
    if (count($datos) < 3) continue;

      $id_asig = $datos[0];
      $dia = $datos[1];
      $hora = $datos[2];

      $insert_stmt->execute([$id_asig, $dia, $hora]);
    }

  fclose($fp_horario);

} else {
  echo "No se pudo abrir el archivo solucion_horario.csv";
}

// Array estudiante
$datos_estudiantes = [];

$sql = " SELECT id_est, nombre, apellido1, apellido2
          FROM estudiantes";

$stmt = $pdo->query($sql);

while ($fila = $stmt->fetch()) {
  $id_est = $fila['id_est'];

  if (!isset($datos_estudiantes[$id_est])) {
    $datos_estudiantes[$id_est] = [
      'id_est' => $id_est,
      'nombre' => $fila['nombre'],
      'apellido1' => $fila['apellido1'],
      'apellido2' => $fila['apellido2']];
  }
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Portal de Servicios</title>
  <link rel="stylesheet" href="index_style.css">
  <script>
  const estudiantes = <?= json_encode($datos_estudiantes ?: new stdClass()) ?>;
  </script>
  <script src="index_script.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>

  <header>
    <aside class="lado_izq" id="header_izq">
      <img src="imagenes/logo_uex.png" alt="Logo_UEX">
    </aside>

    <main class="lado_centro" id="header_centro" >
      <h1>PORTAL DE SERVICIOS</h1>
    </main>

    <aside class="lado_der" id="header_der">
    <div class="user-controls">
      <select id="select_estudiantes" name="estudiantes">
        <option value="">Selecciona un alumno</option>
      </select>

      <img src="https://img.icons8.com/ios-glyphs/30/user--v1.png" alt="icono usuario" />
    </div>
    </aside>

  </header>

  <div class="menu"> 

    <!-- MENU IZQUIERDA-->
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
        <img src="imagenes/iconoF.png"><a href="horario.php" id="link_horario">Horario de clase</a>
      </div>
    </aside>

    <!-- MENU CENTRO-->
    <main id="menu_centro" class="lado_centro">
      <div class="imagen_central">
        <img src="imagenes/biblioteca.jpg">
        <div id="texto_central" >Bienvenido al Portal de Servicios</div>
      </div>

      <div class="avisos">
        <strong>Avisos / Notificaciones</strong>
        <p>No tienes notificaciones.</p>
      </div>
    </main>

    <!-- MENU DERECHA-->
    <aside id="menu_der" class="lado_der">
        <div id="info_user" class="caja_der">
            <p class="username" id="saludo_estudiante"></p>
            <img src="imagenes/icono_hombre.png" alt="icono">
            <p id="texto_user">Consulta y verifica tus datos personales en Académico</p>
        </div>
        <div id="caja_buscar" class="caja_der">
            <p>¿Buscas un servicio?</p>
            <div id="buscar">
              <input type="text">
              <button>
                <img src="imagenes/icono_lupa.png" alt="lupa" />
              </button>
            </div>
        </div>

        <div class="boton_der" id="icono1">
          <div class="caja_icono">
            <img src="imagenes/icono1.png"/>
          </div>
          <p>Servicio de biblioteca</p>
        </div>

        <div class="boton_der" id="icono2">
          <div class="caja_icono">
            <img src="imagenes/icono2.png"/>
          </div>
          <p>Campus virtual</p>
        </div>

        <div class="boton_der" id="icono3">
          <div class="caja_icono">
            <img src="imagenes/icono3.png"/>
          </div>
          <p>Grupo G9</p>
        </div>
    </aside>
    
  </div>

  <footer>
    <p id="p_izq">Universidad de Extremadura</p><p id="p_der">Portal de Servicios (V. 4.4.0) | Todos los derechos reservados</p>
  </footer>

</body>
</html>
