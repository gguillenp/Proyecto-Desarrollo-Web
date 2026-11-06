// Estudiantes (admin): lista y activar/desactivar
// Arranque
const iniciar = async () => {
  if (!await iniciarPagina("admin")) return;
  await cargar();
};

// Pide la lista de estudiantes
const cargar = async () => {
  try {
    const alumnos = await api("api/alumnos.php");
    $("#filas").innerHTML = alumnos.length
      ? alumnos.map(fila).join("")
      : '<tr><td colspan="7" class="vacio">No hay estudiantes.</td></tr>';
  } catch (e) {
    avisar(e.message, "error");
  }
};

// Fila de un estudiante
const fila = (a) => `<tr>
    <td>${esc(a.nombre)}</td>
    <td>${esc(a.correo)}</td>
    <td>${a.solicitudes}</td>
    <td>${a.inscripciones}</td>
    <td>${Number(a.estado) === 1 ? "Activo" : "Desactivado"}</td>
    <td>${fmtFechaHora(a.fecha_creacion)}</td>
    <td>${botonEstado(a)}</td>
  </tr>`;

// Activar o desactivar
$("#filas").addEventListener("click", async (evento) => {
  const boton = evento.target.closest("button[data-estado]");
  if (!boton) return;
  try {
    await accion("api/alumnos.php", "PUT", { id: Number(boton.dataset.id), estado: Number(boton.dataset.estado) });
  } catch (error) {
    avisar(error.message, "error");
  }
  cargar();
});

iniciar();
