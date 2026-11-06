<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Mi cuenta (cualquier rol)
Pagina::privada();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mi cuenta - MarianOpen</title>
  <meta name="description" content="Cambia la contraseña de tu cuenta.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Mi cuenta - MarianOpen">
  <meta property="og:description" content="Cambia la contraseña de tu cuenta.">
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
    <h1>Mi cuenta</h1>

    <!-- Cambiar contraseña -->
    <form id="formClave" class="caja" novalidate>
      <h2>Cambiar contraseña</h2>
      <label for="actual">Contraseña actual</label>
      <input type="password" id="actual" name="actual" autocomplete="current-password" required>
      <label for="nueva">Nueva contraseña (mínimo 6 caracteres)</label>
      <input type="password" id="nueva" name="nueva" autocomplete="new-password" required>
      <label for="nueva2">Repite la nueva contraseña</label>
      <input type="password" id="nueva2" name="nueva2" autocomplete="new-password" required>
      <p class="error-campo" id="error" role="alert"></p>
      <button type="submit" class="boton">Guardar</button>
    </form>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/cuenta.js"></script>
</body>
</html>
