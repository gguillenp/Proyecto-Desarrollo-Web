// Mi cuenta: cambiar contraseña
iniciarPagina();

// Valida y envía el cambio
$("#formClave").addEventListener("submit", async (evento) => {
  evento.preventDefault();
  const error = $("#error");
  const datos = { actual: $("#actual").value, nueva: $("#nueva").value };
  error.textContent = "";

  if (datos.actual === "") {
    error.textContent = "Escribe tu contraseña actual";
    return;
  }
  if (datos.nueva.length < 6) {
    error.textContent = "La nueva contraseña debe tener al menos 6 caracteres";
    return;
  }
  if (datos.nueva !== $("#nueva2").value) {
    error.textContent = "Las contraseñas no coinciden";
    return;
  }

  try {
    await accion("api/cuenta.php", "PUT", datos);
    evento.target.reset();
  } catch (err) {
    error.textContent = err.message;
  }
});
