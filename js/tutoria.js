// Detalle de una tutoría: datos, acciones, solicitudes y calificaciones
// ID de la tutoría (viene en la URL)
const tutoriaId = Number(new URLSearchParams(location.search).get("id"));
let usuario = null;
let tutoria = null;

// Arranque de la página
const iniciar = async () => {
  usuario = await iniciarPagina();
  if (!usuario) return;
  if (!tutoriaId) {
    $("#detalle").textContent = "Tutoría no indicada.";
    return;
  }
  await recargar();
};

// Vuelve a pedir la tutoría y pinta todo
const recargar = async () => {
  try {
    tutoria = await api("api/tutorias.php?id=" + tutoriaId);
  } catch (e) {
    $("#detalle").textContent = e.message;
    return;
  }
  pintarDetalle();
  if (usuario.rol !== "estudiante") {
    cargarSolicitudes();
    if (tutoria.estado === "finalizada") {
      cargarNotas();
    }
  }
};

// Calificaciones: solo profesor/admin y con la tutoría finalizada
const cargarNotas = async () => {
  try {
    const datos = await api("api/notas.php?tutoria_id=" + tutoriaId);
    $("#reglaNotas").textContent = `Nota de 0 a 100. Se aprueba con ${datos.nota_minima}; si no asistió, reprueba.`;
    $("#filasNotas").innerHTML = datos.inscritos.length
      ? datos.inscritos.map(filaNota).join("")
      : '<tr><td colspan="5" class="vacio">No hubo estudiantes inscritos.</td></tr>';
    $("#zonaNotas").classList.remove("oculto");
    $("#zonaNotas button[type=submit]").classList.toggle("oculto", !datos.inscritos.length);
  } catch (e) {
    avisar(e.message, "error");
  }
};

// Fila de un inscrito con asistencia, nota y comentario
const filaNota = (n) => {
  const nombre = esc(n.estudiante);
  return `<tr data-inscripcion="${n.inscripcion_id}">
      <td>${nombre}<br><span class="suave">${esc(n.correo)}</span></td>
      <td><input type="checkbox" class="asistio" aria-label="Asistió ${nombre}" ${n.asistio === null || Number(n.asistio) === 1 ? "checked" : ""}></td>
      <td><input type="number" class="nota campo-nota" min="0" max="100" step="0.01" aria-label="Nota de ${nombre}" value="${n.nota ?? ""}"></td>
      <td><input type="text" class="comentario" maxlength="255" aria-label="Comentario para ${nombre}" value="${esc(n.comentario ?? "")}"></td>
      <td>${insignia(n.resultado)}</td>
    </tr>`;
};

// Guardar notas de todo el grupo
$("#zonaNotas").addEventListener("submit", async (evento) => {
  evento.preventDefault();
  const error = $("#errorNotas");
  error.textContent = "";
  const notas = [...document.querySelectorAll("#filasNotas tr[data-inscripcion]")].map((fila) => ({
    inscripcion_id: Number(fila.dataset.inscripcion),
    asistio: fila.querySelector(".asistio").checked,
    nota: fila.querySelector(".nota").value,
    comentario: fila.querySelector(".comentario").value.trim(),
  }));

  if (notas.some((n) => n.nota !== "" && (Number(n.nota) < 0 || Number(n.nota) > 100))) {
    error.textContent = "Las notas deben estar entre 0 y 100";
    return;
  }

  try {
    await accion("api/notas.php", "PUT", { tutoria_id: tutoriaId, notas });
    cargarNotas();
  } catch (err) {
    error.textContent = err.message;
  }
});

// Datos de la tutoría y botones según el rol
const pintarDetalle = () => {
  const t = tutoria;
  const esDueno = usuario.rol === "profesor" && t.profesor_id === usuario.id;
  const puedeGestionar = esDueno || usuario.rol === "admin";
  const empezo = `${t.fecha} ${t.hora_inicio}` <= ahora();
  const libres = Number(t.cupo_max) - Number(t.ocupados);
  const faltan = Math.max(0, Number(t.cupo_min) - Number(t.ocupados));
  let acciones = "";

  // Estudiante: solicitar o cancelar
  if (usuario.rol === "estudiante") {
    const solicitud = t.mi_solicitud;
    if (solicitud && solicitud.estado !== "cancelada") {
      acciones = `<p>Tu solicitud: ${insignia(solicitud.estado)}</p>`;
      if (["pendiente", "aceptada"].includes(solicitud.estado) && !empezo) {
        acciones += `<button type="button" class="boton peligro" data-accion="cancelar" data-id="${solicitud.id}">Cancelar mi solicitud</button>`;
      }
    } else if (t.estado === "cancelada") {
      acciones = '<p class="suave">Esta tutoría fue cancelada.</p>';
    } else if (libres <= 0) {
      acciones = '<p class="suave">Sin cupos disponibles.</p>';
    } else {
      acciones = '<button type="button" class="boton" data-accion="solicitar">Solicitar inscripción</button>';
    }
  } else if (puedeGestionar && t.estado === "finalizada") {
    // Dueño o admin con la tutoría terminada: solo un aviso
    acciones = '<p class="suave">Esta tutoría ya terminó.</p>';
  } else if (puedeGestionar) {
    // Dueño o admin: editar y cambiar estado
    acciones = `<a class="boton secundario" href="tutorias.php?editar=${t.id}">Editar datos</a>`;
    if (t.estado === "abierta") {
      acciones += ` <button type="button" class="boton ok" data-accion="estado" data-estado="confirmada"
        ${faltan > 0 ? 'disabled title="Aún no se alcanza el cupo mínimo"' : ""}>Confirmar tutoría</button>`;
    }
    if (t.estado === "confirmada") {
      acciones += ' <button type="button" class="boton secundario" data-accion="estado" data-estado="abierta">Reabrir</button>';
    }
    if (t.estado !== "cancelada") {
      acciones += ' <button type="button" class="boton peligro" data-accion="estado" data-estado="cancelada">Cancelar tutoría</button>';
    } else {
      acciones += ' <button type="button" class="boton secundario" data-accion="estado" data-estado="abierta">Reactivar</button>';
    }
  }

  $("#detalle").innerHTML = `
    <div><span class="insignia materia">${esc(t.materia)}</span> ${insignia(t.estado)}</div>
    <h1>${esc(t.titulo)}</h1>
    <p><strong>Tema:</strong> ${esc(t.tema)}</p>
    <p>${esc(t.descripcion) || '<span class="suave">Sin descripción.</span>'}</p>
    <p class="datos">
      <strong>Profesor:</strong> ${esc(t.profesor)}<br>
      <strong>Fecha:</strong> ${fmtFecha(t.fecha)} &middot; ${fmtHora(t.hora_inicio)} - ${fmtHora(t.hora_fin)}
    </p>
    ${barraCupos(t)}
    <p class="suave">
      Cupos libres: ${libres} &middot;
      ${faltan > 0 ? `faltan ${faltan} inscritos para alcanzar el mínimo` : "cupo mínimo alcanzado"}
      ${puedeGestionar ? ` &middot; solicitudes pendientes: ${t.pendientes}` : ""}
    </p>
    <div class="acciones">${acciones}</div>`;
};

// Clics en los botones del detalle
$("#detalle").addEventListener("click", async (evento) => {
  const boton = evento.target.closest("button[data-accion]");
  if (!boton || boton.disabled) return;

  try {
    if (boton.dataset.accion === "solicitar") {
      await accion("api/solicitudes.php", "POST", { tutoria_id: tutoriaId });
    } else if (boton.dataset.accion === "cancelar") {
      if (!confirm("¿Cancelar tu solicitud?")) return;
      await accion("api/solicitudes.php", "DELETE", { id: Number(boton.dataset.id) });
    } else if (boton.dataset.accion === "estado") {
      if (boton.dataset.estado === "cancelada" && !confirm("¿Cancelar esta tutoría?")) return;
      await accion("api/tutorias.php", "PUT", { id: tutoriaId, accion: "estado", estado: boton.dataset.estado });
    }
  } catch (error) {
    avisar(error.message, "error");
  }
  recargar();
});

// Tabla de solicitudes de estudiantes
const cargarSolicitudes = async () => {
  $("#zonaSolicitudes").classList.remove("oculto");
  try {
    const lista = await api("api/solicitudes.php?tutoria_id=" + tutoriaId);
    $("#filas").innerHTML = lista.length
      ? lista.map(fila).join("")
      : '<tr><td colspan="6" class="vacio">Aún no hay solicitudes.</td></tr>';
  } catch (e) {
    avisar(e.message, "error");
  }
};

// Fila de una solicitud (aceptar solo antes de que empiece)
const fila = (s) => {
  const esDueno = usuario.rol === "profesor" && tutoria.profesor_id === usuario.id;
  const empezo = `${tutoria.fecha} ${tutoria.hora_inicio}` <= ahora();
  const botones = esDueno && s.estado === "pendiente"
    ? `${empezo ? "" : `<button type="button" class="boton ok chico" data-id="${s.id}" data-estado="aceptada" aria-label="Aceptar a ${esc(s.estudiante)}">Aceptar</button>`}
       <button type="button" class="boton peligro chico" data-id="${s.id}" data-estado="rechazada" aria-label="Rechazar a ${esc(s.estudiante)}">Rechazar</button>`
    : "";
  return `<tr>
      <td>${esc(s.estudiante)}</td>
      <td>${esc(s.correo)}</td>
      <td>${fmtFechaHora(s.fecha_solicitud)}</td>
      <td>${fmtFechaHora(s.fecha_resolucion) || "-"}</td>
      <td>${insignia(s.estado)}</td>
      <td>${botones}</td>
    </tr>`;
};

// Aceptar o rechazar una solicitud
$("#filas").addEventListener("click", async (evento) => {
  const boton = evento.target.closest("button[data-estado]");
  if (!boton) return;

  try {
    await accion("api/solicitudes.php", "PUT", { id: Number(boton.dataset.id), estado: boton.dataset.estado });
  } catch (error) {
    avisar(error.message, "error");
  }
  recargar();
});

// Arranque
iniciar();
