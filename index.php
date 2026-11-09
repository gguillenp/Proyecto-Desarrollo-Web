<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Inicio de sesión (solo sin sesión)
Pagina::publica();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión - MarianOpen</title>
  <meta name="description" content="Inicia sesión en MarianOpen para solicitar tutorías académicas o gestionar las que ofreces como profesor.">
  <meta property="og:title" content="MarianOpen">
  <meta property="og:description" content="Consulta tutorías por materia y tema, solicita un cupo y sigue el estado de tus solicitudes.">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="MarianOpen">
  <meta property="og:locale" content="es_GT">
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="icon" href="favicon.png" type="image/png">
</head>
<body class="acceso">
  <!-- Formulario de inicio de sesión -->
  <main class="centrado">
    <section class="caja" aria-labelledby="titulo">
      <h1 id="titulo"><img class="logo" src="img/logo-eslogan.webp" alt="MarianOpen. Tus tutorías, abiertas y de código abierto" width="800" height="204"></h1>
      <p class="suave">Inicia sesión con tu correo y contraseña.</p>

      <form id="formLogin" novalidate>
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" autocomplete="username" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>

        <label class="opcion" for="recordar">
          <input type="checkbox" id="recordar" name="recordar"> Recordarme en este equipo (30 días)
        </label>

        <p class="error-campo" id="error" role="alert"></p>
        <button type="submit" class="boton ancho">Entrar</button>
      </form>

      <!-- Enlace al registro -->
      <p class="suave sin-margen-abajo">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    </section>
  </main>

  <!-- Pie de página: autores con enlace a su portafolio -->
  <footer class="pie">
    <p>Autores</p>
    <a class="boton chico" href="https://gguillenp.github.io/portafolio-gabrielg/" target="_blank" rel="noopener">Gabriel Guillén</a>
    <a class="boton chico" href="https://marioo-t.github.io/portafolio-mario/" target="_blank" rel="noopener">Mario Taracena</a>
    <a class="boton chico" href="https://dylanherdz.github.io/" target="_blank" rel="noopener">Dylan Hernández</a>
  </footer>

  <script src="js/api.js"></script>
  <!-- Lógica del login -->
  <script>
    // Si ya hay sesión, va directo al panel
    api("api/sesion.php").then(() => { location.href = "panel.php"; }).catch(() => {});

    // Valida y envía el login
    $("#formLogin").addEventListener("submit", async (evento) => {
      evento.preventDefault();
      const error = $("#error");
      const correo = $("#correo").value.trim();
      const password = $("#password").value;
      error.textContent = "";

      if (correo === "" || password === "") {
        error.textContent = "Ingresa tu correo y contraseña";
        return;
      }
      if (!/^\S+@\S+\.\S+$/.test(correo)) {
        error.textContent = "El correo no es válido";
        return;
      }

      try {
        await api("api/login.php", "POST", { correo, password, recordar: $("#recordar").checked });
        location.href = "panel.php";
      } catch (err) {
        error.textContent = err.message;
      }
    });
  </script>
</body>
</html>
