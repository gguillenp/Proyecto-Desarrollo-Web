<?php
require_once(__DIR__ . "/api/core/Pagina.php");
// Lista de tutorías (cualquier rol)
Pagina::privada();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tutorías - MarianOpen</title>
  <meta name="description" content="Busca tutorías por materia, tema, fecha o profesor, solicita un cupo o crea y gestiona tus propias tutorías.">
  <meta name="robots" content="noindex, nofollow">
  <meta property="og:title" content="Tutorías - MarianOpen">
  <meta property="og:description" content="Busca tutorías por materia, tema, fecha o profesor.">
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
    <h1>Tutorías</h1>

    <!-- Crear/editar tutoría (profesor y admin) -->
    <section id="zonaProfesor" class="oculto" aria-label="Gestión de tutorías">
      <button type="button" class="boton" id="btnNueva">+ Nueva tutoría</button>

      <form id="formTutoria" class="caja oculto separado" novalidate>
        <h2 id="tituloForm">Nueva tutoría</h2>
        <input type="hidden" id="id" name="id">

        <div class="fila">
          <div>
            <label for="materia_id">Materia</label>
            <select id="materia_id" name="materia_id" required></select>
          </div>
          <div>
            <label for="tema">Tema</label>
            <input type="text" id="tema" name="tema" maxlength="120" placeholder="Ej. Álgebra" required>
          </div>
        </div>

        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" maxlength="150" required>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion"></textarea>

        <div class="fila">
          <div><label for="fecha">Fecha</label><input type="date" id="fecha" name="fecha" required></div>
          <div><label for="hora_inicio">Hora inicio</label><input type="time" id="hora_inicio" name="hora_inicio" required></div>
          <div><label for="hora_fin">Hora fin</label><input type="time" id="hora_fin" name="hora_fin" required></div>
          <div><label for="cupo_min">Cupo mínimo</label><input type="number" id="cupo_min" name="cupo_min" min="1" value="10" required></div>
          <div><label for="cupo_max">Cupo máximo</label><input type="number" id="cupo_max" name="cupo_max" min="1" value="20" required></div>
        </div>
        <p class="suave">La tutoría solo puede confirmarse cuando se alcance el cupo mínimo; nunca se aceptan más estudiantes que el cupo máximo.</p>

        <p class="error-campo" id="errorForm" role="alert"></p>
        <div class="acciones">
          <button type="submit" class="boton" id="btnGuardar">Guardar</button>
          <button type="button" class="boton secundario" id="btnCancelarForm">Cancelar</button>
        </div>
      </form>
    </section>

    <!-- Filtros de búsqueda -->
    <form id="filtros" class="caja filtros separado" aria-label="Filtros de tutorías">
      <div>
        <label for="f_materia">Materia</label>
        <select id="f_materia"><option value="">Todas</option></select>
      </div>
      <div>
        <label for="f_tema">Tema</label>
        <input type="search" id="f_tema" placeholder="Buscar tema">
      </div>
      <div>
        <label for="f_fecha">Fecha</label>
        <input type="date" id="f_fecha">
      </div>
      <div id="divProfesor">
        <label for="f_profesor">Profesor</label>
        <input type="search" id="f_profesor" placeholder="Nombre">
      </div>
      <div>
        <label for="f_estado">Estado</label>
        <select id="f_estado">
          <option value="">Todos</option>
          <option value="abierta">Abierta</option>
          <option value="confirmada">Confirmada</option>
          <option value="finalizada">Finalizada</option>
          <option value="cancelada">Cancelada</option>
        </select>
      </div>
      <div class="acciones sin-margen-arriba">
        <button type="submit" class="boton">Filtrar</button>
        <button type="button" class="boton secundario" id="btnLimpiar">Limpiar</button>
      </div>
    </form>

    <!-- Tarjetas de tutorías -->
    <div id="lista" class="rejilla" aria-live="polite"></div>
  </main>

  <!-- Scripts: utilidades, sesión/menú y lógica de esta página -->
  <script src="js/api.js"></script>
  <script src="js/sesion.js"></script>
  <script src="js/tutorias.js"></script>
</body>
</html>
