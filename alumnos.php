<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Estudiantes (solo admin)
Pagina::privada("admin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Estudiantes - MarianOpen</title>
  <meta name="description" content="Administración de las cuentas de estudiantes.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Estudiantes - MarianOpen">
  <meta property="og:description" content="Administración de las cuentas de estudiantes.">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="MarianOpen">
  <meta property="og:locale" content="es_GT">
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="icon" href="favicon.png" type="image/png">
</head>
<body>
  <!-- Encabezado: logo y menú (el menú lo pinta js/sesion.js) -->
  <header class="encabezado">
    <div class="contenido">
      <a class="marca" href="panel.php"><img src="img/logo.webp" alt="MarianOpen" width="160" height="31"></a>
      <nav class="menu" id="menu" aria-label="Menú principal"></nav>
    </div>
  </header>

  <!-- Contenido principal de la página -->
  <main>
    <h1>Estudiantes</h1>

    <!-- Tabla de estudiantes -->
    <div class="caja tabla-scroll">
      <table>
        <caption class="solo-lectores">Estudiantes registrados</caption>
        <thead>
          <tr><th scope="col">Nombre</th><th scope="col">Correo</th><th scope="col">Solicitudes</th><th scope="col">Inscripciones</th><th scope="col">Estado</th><th scope="col">Creado</th><th scope="col">Acción</th></tr>
        </thead>
        <tbody id="filas"></tbody>
      </table>
    </div>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/alumnos.js"></script>
</body>
</html>
