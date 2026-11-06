// Panel: resumen con números según el rol
// Tarjeta con un número y su nombre
const indicador = (numero, nombre) =>
  `<div class="caja indicador"><div class="numero">${numero}</div><div class="nombre">${nombre}</div></div>`;

// Carga los datos y pinta el panel del rol
const iniciar = async () => {
  const usuario = await iniciarPagina();
  if (!usuario) return;
  $("#saludo").textContent = "Hola, " + usuario.nombre;

  let datos;
  try {
    datos = await api("api/panel.php");
  } catch (e) {
    avisar(e.message, "error");
    return;
  }
  if (datos.rol === "admin") {
    panelAdmin(datos);
  } else if (datos.rol === "profesor") {
    panelProfesor(datos);
  } else {
    panelEstudiante(datos);
  }
};

// Panel del admin: totales y accesos de gestión
const panelAdmin = (d) => {
  $("#indicadores").innerHTML =
    indicador(d.profesores, "Profesores") +
    indicador(d.estudiantes, "Estudiantes") +
    indicador(d.tutorias, "Tutorías") +
    indicador(d.materias, "Materias");

  $("#detalle").innerHTML = `<div class="acciones">
    <a class="boton" href="tutorias.php">Gestionar tutorías</a>
    <a class="boton" href="profesores.php">Gestionar profesores</a>
    <a class="boton" href="alumnos.php">Gestionar estudiantes</a>
    <a class="boton" href="materias.php">Gestionar materias</a></div>`;
};

// Panel del profesor: números y tabla de sus tutorías
const panelProfesor = (d) => {
  $("#indicadores").innerHTML =
    indicador(d.tutorias_total, "Tutorías creadas") +
    indicador(d.tutorias_abiertas, "Abiertas") +
    indicador(d.tutorias_confirmadas, "Confirmadas") +
    indicador(d.tutorias_finalizadas, "Finalizadas") +
    indicador(d.estudiantes_inscritos, "Estudiantes inscritos") +
    indicador(d.solicitudes_pendientes, "Solicitudes pendientes") +
    indicador(d.por_calificar, "Por calificar");

  const filas = d.tutorias.map((t) => {
    const faltan = Math.max(0, t.cupo_min - t.ocupados);
    return `<tr>
        <td><a href="tutoria.php?id=${t.id}">${esc(t.titulo)}</a><br><span class="suave">${esc(t.tema)}</span></td>
        <td>${fmtFecha(t.fecha)}</td>
        <td>${insignia(t.estado)}</td>
        <td class="col-cupos">${barraCupos(t)}</td>
        <td>${faltan > 0 ? `Faltan ${faltan} para el mínimo` : "Mínimo alcanzado"}</td>
      </tr>`;
  }).join("");

  $("#detalle").innerHTML = `<div class="caja tabla-scroll"><h3>Estado de mis tutorías</h3>
    <table>
      <thead><tr><th>Tutoría</th><th>Fecha</th><th>Estado</th><th>Cupos</th><th>Mínimo</th></tr></thead>
      <tbody>${filas || '<tr><td colspan="5" class="vacio">Aún no has creado tutorías.</td></tr>'}</tbody>
    </table>
    <div class="acciones"><a class="boton" href="tutorias.php">Gestionar tutorías</a></div></div>`;
};

// Panel del estudiante: números y próximas tutorías
const panelEstudiante = (d) => {
  $("#indicadores").innerHTML =
    indicador(d.disponibles, "Disponibles") +
    indicador(d.pendientes, "Solicitudes pendientes") +
    indicador(d.inscritas, "Inscritas") +
    indicador(d.finalizadas, `Finalizadas · ${d.aprobadas} aprobada${d.aprobadas === 1 ? "" : "s"}`) +
    indicador(d.rechazadas, "Rechazadas") +
    indicador(d.canceladas, "Canceladas");

  const filas = d.proximas.map((t) => `<tr>
      <td><a href="tutoria.php?id=${t.id}">${esc(t.titulo)}</a><br><span class="suave">${esc(t.tema)}</span></td>
      <td>${fmtFecha(t.fecha)} ${fmtHora(t.hora_inicio)}</td>
      <td>${esc(t.profesor)}</td>
      <td>${insignia(t.estado)}</td>
    </tr>`).join("");

  $("#detalle").innerHTML = `<div class="caja tabla-scroll"><h3>Mis próximas tutorías</h3>
    <table>
      <thead><tr><th>Tutoría</th><th>Fecha</th><th>Profesor</th><th>Estado</th></tr></thead>
      <tbody>${filas || '<tr><td colspan="4" class="vacio">No tienes tutorías próximas aceptadas.</td></tr>'}</tbody>
    </table>
    <div class="acciones"><a class="boton" href="tutorias.php">Buscar tutorías</a>
    <a class="boton secundario" href="mis-solicitudes.php">Ver mis solicitudes</a></div></div>`;
};

// Arranque
iniciar();
