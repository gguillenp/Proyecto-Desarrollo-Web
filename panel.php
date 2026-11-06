<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Panel principal (cualquier rol)
Pagina::privada();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel - MarianOpen</title>
  <meta name="description" content="Resumen de tus tutorías, cupos y solicitudes en MarianOpen.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Panel - MarianOpen">
  <meta property="og:description" content="Resumen de tus tutorías, cupos y solicitudes.">
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
    <h1 id="saludo">Panel</h1>
    <!-- Números del resumen -->
    <div id="indicadores" class="indicadores" aria-live="polite"></div>
    <!-- Tabla y accesos según el rol -->
    <section id="detalle" aria-label="Detalle del resumen"></section>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/panel.js"></script>
</body>
</html>
