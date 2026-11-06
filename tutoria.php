<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Detalle de una tutoría (cualquier rol)
Pagina::privada();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detalle de tutoría - MarianOpen</title>
  <meta name="description" content="Detalle de la tutoría: tema, horario, cupos disponibles y solicitudes de los estudiantes.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Detalle de tutoría - MarianOpen">
  <meta property="og:description" content="Tema, horario y cupos de la tutoría.">
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
    <p><a href="tutorias.php">&larr; Volver a tutorías</a></p>

    <!-- Datos de la tutoría y acciones -->
    <section id="detalle" class="caja" aria-live="polite">Cargando...</section>

    <!-- Solicitudes (profesor dueño y admin) -->
    <section id="zonaSolicitudes" class="caja oculto" aria-labelledby="tituloSolicitudes">
      <h2 id="tituloSolicitudes">Solicitudes de estudiantes</h2>
      <p class="suave">Al aceptar una solicitud el estudiante ocupa un cupo. No se puede superar el cupo máximo.</p>
      <div class="tabla-scroll">
        <table>
          <caption class="solo-lectores">Solicitudes de esta tutoría</caption>
          <thead>
            <tr><th scope="col">Estudiante</th><th scope="col">Correo</th><th scope="col">Solicitada</th><th scope="col">Resuelta</th><th scope="col">Estado</th><th scope="col"><span class="solo-lectores">Acciones</span></th></tr>
          </thead>
          <tbody id="filas"></tbody>
        </table>
      </div>
    </section>

    <!-- Calificaciones (cuando la tutoría ya terminó) -->
    <form id="zonaNotas" class="caja oculto" aria-labelledby="tituloNotas" novalidate>
      <h2 id="tituloNotas">Calificaciones</h2>
      <p class="suave" id="reglaNotas"></p>
      <div class="tabla-scroll">
        <table>
          <caption class="solo-lectores">Notas de los inscritos</caption>
          <thead>
            <tr><th scope="col">Estudiante</th><th scope="col">Asistió</th><th scope="col">Nota</th><th scope="col">Comentario</th><th scope="col">Resultado</th></tr>
          </thead>
          <tbody id="filasNotas"></tbody>
        </table>
      </div>
      <p class="error-campo" id="errorNotas" role="alert"></p>
      <button type="submit" class="boton">Guardar notas</button>
    </form>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/tutoria.js"></script>
</body>
</html>
