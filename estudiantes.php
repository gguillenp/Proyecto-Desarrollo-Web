<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Mis estudiantes (solo profesor)
Pagina::privada("profesor");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis estudiantes - MarianOpen</title>
  <meta name="description" content="Estudiantes inscritos en tus tutorías.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Mis estudiantes - MarianOpen">
  <meta property="og:description" content="Estudiantes inscritos en tus tutorías.">
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
    <h1>Mis estudiantes</h1>
    <p class="suave">Estudiantes aceptados en tus tutorías.</p>

    <!-- Filtro por tutoría -->
    <div class="caja">
      <div class="campo-corto">
        <label for="f_tutoria">Filtrar por tutoría</label>
        <select id="f_tutoria">
          <option value="">Todas</option>
        </select>
      </div>
    </div>

    <!-- Tabla de estudiantes inscritos -->
    <div class="caja tabla-scroll">
      <table>
        <caption class="solo-lectores">Estudiantes inscritos</caption>
        <thead>
          <tr><th scope="col">Estudiante</th><th scope="col">Correo</th><th scope="col">Tutoría</th><th scope="col">Materia / tema</th><th scope="col">Fecha</th><th scope="col">Inscrito</th></tr>
        </thead>
        <tbody id="filas"></tbody>
      </table>
    </div>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/estudiantes.js"></script>
</body>
</html>
