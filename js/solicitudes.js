// Mis solicitudes (estudiante): historial, estado y nota
let solicitudes = [];

// Arranque
const iniciar = async () => {
  if (!await iniciarPagina("estudiante")) return;
  await cargar();
};

// Pide las solicitudes y marca las finalizadas
const cargar = async () => {
  try {
    solicitudes = (await api("api/solicitudes.php")).map((s) => {
      s.empezo = `${s.fecha} ${s.hora_inicio}` <= ahora();
      if (s.estado === "aceptada" && s.estado_tutoria !== "cancelada" && `${s.fecha} ${s.hora_fin}` <= ahora()) {
        s.estado = "finalizada";
      }
      return s;
    });
    pintar();
  } catch (e) {
    avisar(e.message, "error");
  }
};

// Pinta la tabla según el filtro
const pintar = () => {
  const filtro = $("#f_estado").value;
  const lista = solicitudes.filter((s) => !filtro || s.estado === filtro);
  $("#filas").innerHTML = lista.length
    ? lista.map(fila).join("")
    : '<tr><td colspan="9" class="vacio">No hay solicitudes.</td></tr>';
};

// Nota, resultado y comentario del profesor
const notaTexto = (s) => {
  const nota = s.nota === null ? "" : `${Number(s.nota)} `;
  const comentario = s.comentario ? `<br><span class="suave">${esc(s.comentario)}</span>` : "";
  return `${nota}${insignia(s.resultado)}${comentario}`;
};

// Fila de una solicitud; se cancela solo antes de que empiece
const fila = (s) => {
  const cancelable = ["pendiente", "aceptada"].includes(s.estado) && !s.empezo;
  return `<tr>
      <td><a href="tutoria.php?id=${s.tutoria_id}">${esc(s.titulo)}</a> ${s.estado_tutoria === "cancelada" ? insignia("cancelada") : ""}</td>
      <td>${esc(s.materia)} / ${esc(s.tema)}</td>
      <td>${esc(s.profesor)}</td>
      <td>${fmtFecha(s.fecha)} ${fmtHora(s.hora_inicio)}</td>
      <td>${fmtFechaHora(s.fecha_solicitud)}</td>
      <td>${fmtFechaHora(s.fecha_resolucion) || "-"}</td>
      <td>${insignia(s.estado)}</td>
      <td>${s.estado === "finalizada" ? notaTexto(s) : "-"}</td>
      <td>${cancelable ? `<button type="button" class="boton peligro chico" data-id="${s.id}" aria-label="Cancelar solicitud de ${esc(s.titulo)}">Cancelar</button>` : ""}</td>
    </tr>`;
};

// Filtro por estado
$("#f_estado").addEventListener("change", pintar);

// Cancelar una solicitud
$("#filas").addEventListener("click", async (evento) => {
  const boton = evento.target.closest("button[data-id]");
  if (!boton || !confirm("¿Cancelar esta solicitud?")) return;

  try {
    await accion("api/solicitudes.php", "DELETE", { id: Number(boton.dataset.id) });
  } catch (error) {
    avisar(error.message, "error");
  }
  cargar();
});

iniciar();
