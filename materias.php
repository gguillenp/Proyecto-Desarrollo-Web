<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Materias (solo admin)
Pagina::privada("admin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Materias - MarianOpen</title>
  <meta name="description" content="Administración de las materias.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Materias - MarianOpen">
  <meta property="og:description" content="Administración de las materias.">
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
    <h1>Materias</h1>

    <!-- Crear materia -->
    <form id="formMateria" class="caja" novalidate>
      <h2>Nueva materia</h2>
      <label for="nombre">Nombre</label>
      <input type="text" id="nombre" name="nombre" maxlength="80" required>
      <p class="error-campo" id="error" role="alert"></p>
      <button type="submit" class="boton">Crear materia</button>
    </form>

    <!-- Tabla de materias -->
    <div class="caja tabla-scroll">
      <table>
        <caption class="solo-lectores">Materias registradas</caption>
        <thead>
          <tr><th scope="col">Nombre</th><th scope="col">Tutorías</th><th scope="col">Acción</th></tr>
        </thead>
        <tbody id="filas"></tbody>
      </table>
    </div>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/materias.js"></script>
</body>
</html>
