// Materias (admin): lista, crear y eliminar
// Arranque
const iniciar = async () => {
  if (!await iniciarPagina("admin")) return;
  await cargar();
};

// Pide la lista de materias
const cargar = async () => {
  try {
    const materias = await api("api/materias.php");
    $("#filas").innerHTML = materias.length
      ? materias.map(fila).join("")
      : '<tr><td colspan="3" class="vacio">No hay materias.</td></tr>';
  } catch (e) {
    avisar(e.message, "error");
  }
};

// Fila de una materia (se elimina solo si no tiene tutorías)
const fila = (m) => `<tr>
    <td>${esc(m.nombre)}</td>
    <td>${m.tutorias}</td>
    <td>${Number(m.tutorias) === 0
      ? `<button type="button" class="boton peligro chico" data-id="${m.id}" aria-label="Eliminar ${esc(m.nombre)}">Eliminar</button>`
      : ""}</td>
  </tr>`;

// Eliminar materia
$("#filas").addEventListener("click", async (evento) => {
  const boton = evento.target.closest("button[data-id]");
  if (!boton || !confirm("¿Eliminar esta materia?")) return;
  try {
    await accion("api/materias.php", "DELETE", { id: Number(boton.dataset.id) });
  } catch (error) {
    avisar(error.message, "error");
  }
  cargar();
});

// Crear materia
$("#formMateria").addEventListener("submit", async (evento) => {
  evento.preventDefault();
  const error = $("#error");
  const nombre = $("#nombre").value.trim();
  error.textContent = "";

  if (nombre === "") {
    error.textContent = "Escribe el nombre de la materia";
    return;
  }

  try {
    await accion("api/materias.php", "POST", { nombre });
    evento.target.reset();
    cargar();
  } catch (err) {
    error.textContent = err.message;
  }
});

iniciar();
