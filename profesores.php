<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Profesores (solo admin)
Pagina::privada("admin");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profesores - MarianOpen</title>
  <meta name="description" content="Administración de las cuentas de profesores.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Profesores - MarianOpen">
  <meta property="og:description" content="Administración de las cuentas de profesores.">
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
    <h1>Profesores</h1>

    <!-- Crear profesor -->
    <form id="formProfesor" class="caja" novalidate>
      <h2>Nuevo profesor</h2>
      <div class="fila">
        <div><label for="nombre">Nombre completo</label><input type="text" id="nombre" name="nombre" maxlength="255" required></div>
        <div><label for="correo">Correo</label><input type="email" id="correo" name="correo" maxlength="150" required></div>
        <div><label for="password">Contraseña (mínimo 6 caracteres)</label><input type="password" id="password" name="password" autocomplete="new-password" required></div>
      </div>
      <p class="error-campo" id="error" role="alert"></p>
      <button type="submit" class="boton">Crear profesor</button>
    </form>

    <!-- Tabla de profesores -->
    <div class="caja tabla-scroll">
      <table>
        <caption class="solo-lectores">Profesores registrados</caption>
        <thead>
          <tr><th scope="col">Nombre</th><th scope="col">Correo</th><th scope="col">Tutorías</th><th scope="col">Estado</th><th scope="col">Creado</th><th scope="col">Acción</th></tr>
        </thead>
        <tbody id="filas"></tbody>
      </table>
    </div>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/profesores.js"></script>
</body>
</html>
