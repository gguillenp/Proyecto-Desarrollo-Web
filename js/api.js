// Utilidades compartidas por todas las páginas
// Atajo para buscar un elemento
const $ = (selector) => document.querySelector(selector);

// Escapa texto antes de meterlo en HTML
const esc = (texto) => String(texto ?? "").replace(/[&<>"']/g, (c) => ({
  "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;",
}[c]));

// Error con el código HTTP
class ErrorApi extends Error {
  constructor(mensaje, estado) {
    super(mensaje);
    this.estado = estado;
  }
}

// Llama a la API con fetch y revisa la respuesta
class ClienteApi {
  async solicitar(url, metodo = "GET", datos = null) {
    const opciones = { method: metodo, headers: {} };
    if (datos) {
      opciones.headers["Content-Type"] = "application/json";
      opciones.body = JSON.stringify(datos);
    }

    let respuesta;
    try {
      respuesta = await fetch(url, opciones);
    } catch (e) {
      throw new ErrorApi("No se pudo conectar con el servidor", 0);
    }

    let cuerpo = null;
    try {
      cuerpo = await respuesta.json();
    } catch (e) {
    }
    if (!respuesta.ok || !cuerpo || cuerpo.success === false) {
      throw new ErrorApi((cuerpo && cuerpo.message) || "Ocurrió un error inesperado", respuesta.status);
    }
    return cuerpo;
  }
}

const clienteApi = new ClienteApi();

// Pide datos a la API y devuelve solo data
const api = async (url, metodo = "GET", datos = null) => {
  const cuerpo = await clienteApi.solicitar(url, metodo, datos);
  return cuerpo.data;
};

// Igual que api(), pero muestra el mensaje de éxito
const accion = async (url, metodo, datos = null) => {
  const cuerpo = await clienteApi.solicitar(url, metodo, datos);
  avisar(cuerpo.message);
  return cuerpo.data;
};

// Aviso flotante abajo a la derecha
const avisar = (texto, tipo = "ok") => {
  const div = document.createElement("div");
  div.className = "aviso " + (tipo === "error" ? "error" : "");
  div.setAttribute("role", "status");
  div.textContent = texto;
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 3500);
};

// Formatos de fecha y hora para mostrar
const fmtFecha = (f) => {
  if (!f) return "";
  const [anio, mes, dia] = String(f).slice(0, 10).split("-");
  return `${dia}/${mes}/${anio}`;
};

const fmtHora = (h) => String(h || "").slice(0, 5);

// Botón Activar/Desactivar de las tablas de usuarios
const botonEstado = (u) => Number(u.estado) === 1
  ? `<button type="button" class="boton peligro chico" data-id="${u.id}" data-estado="0" aria-label="Desactivar a ${esc(u.nombre)}">Desactivar</button>`
  : `<button type="button" class="boton ok chico" data-id="${u.id}" data-estado="1" aria-label="Activar a ${esc(u.nombre)}">Activar</button>`;

// Fecha y hora actual como "AAAA-MM-DD HH:MM:SS"
const ahora = () => {
  const d = new Date();
  const dos = (n) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${dos(d.getMonth() + 1)}-${dos(d.getDate())} ${dos(d.getHours())}:${dos(d.getMinutes())}:${dos(d.getSeconds())}`;
};

const fmtFechaHora = (f) => (f ? fmtFecha(f) + " " + String(f).slice(11, 16) : "");

// Etiqueta de estado (abierta, finalizada, aprobado...)
const insignia = (estado) => `<span class="insignia ${esc(estado)}">${esc(estado)}</span>`;

// Texto de inscritos y cupo mínimo
const barraCupos = (t) => {
  const max = Number(t.cupo_max);
  const min = Number(t.cupo_min);
  const ocupados = Number(t.ocupados);

  return `<div class="cupo">
      <span>Inscritos <strong>${ocupados}/${max}</strong> &middot; mínimo ${min}</span>
    </div>`;
};

// Abre el selector de fecha/hora al hacer clic en el campo
document.addEventListener("click", (evento) => {
  const campo = evento.target.closest('input[type="date"], input[type="time"]');
  if (campo && campo.showPicker) {
    try {
      campo.showPicker();
    } catch (e) {
    }
  }
});
