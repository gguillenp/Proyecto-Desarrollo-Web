// Sesión en el navegador y menú de navegación
// Verifica la sesión (y el rol) y pinta el menú; devuelve el usuario
const iniciarPagina = async (rolRequerido = null) => {
  let usuario;
  try {
    usuario = await api("api/sesion.php");
  } catch (e) {
    location.href = "index.php";
    return null;
  }
  if (rolRequerido && usuario.rol !== rolRequerido) {
    location.href = "panel.php";
    return null;
  }
  pintarMenu(usuario);
  return usuario;
};

// Menú según el rol
const pintarMenu = (usuario) => {
  const actual = location.pathname.split("/").pop() || "index.php";
  const enlaces = [["panel.php", "Panel"], ["tutorias.php", "Tutorías"]];
  if (usuario.rol === "estudiante") {
    enlaces.push(["mis-solicitudes.php", "Mis solicitudes"]);
  } else if (usuario.rol === "profesor") {
    enlaces.push(["estudiantes.php", "Mis estudiantes"]);
  } else {
    enlaces.push(["profesores.php", "Profesores"], ["alumnos.php", "Estudiantes"], ["materias.php", "Materias"]);
  }
  enlaces.push(["cuenta.php", "Mi cuenta"]);

  // Botones del menú, nombre del usuario y Salir
  $("#menu").innerHTML = enlaces.map(([url, texto]) =>
    `<a href="${url}" class="${actual === url ? "activo" : ""}" ${actual === url ? 'aria-current="page"' : ""}>${texto}</a>`).join("") +
    `<span class="usuario">${esc(usuario.nombre)} (${esc(usuario.rol)})</span>
     <button type="button" id="btnSalir" aria-label="Cerrar sesión de ${esc(usuario.nombre)}">Salir</button>`;

  // Cerrar sesión
  $("#btnSalir").addEventListener("click", async () => {
    try {
      await api("api/logout.php", "POST", {});
    } catch (e) {
    }
    location.href = "index.php";
  });
};

