<?php
// POST: inicia sesión con correo y contraseña
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Request.php");
require_once(__DIR__ . "/core/Auth.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Response::error("Método no permitido", -1, 405);
}

// Datos enviados
$pdo = new Connection();
$datos = Request::body();
$correo = strtolower(Request::texto($datos["correo"] ?? ""));
$clave = (string)($datos["password"] ?? "");

// Validación básica
if ($correo === "" || $clave === "") {
    Response::error("Ingresa correo y contraseña");
}

// Busca el correo en cada tabla de rol (solo cuentas activas)
foreach (["admin", "profesor", "estudiante"] as $rol) {
    $tabla = Auth::tabla($rol);
    $st = $pdo->prepare("SELECT id, nombre, password FROM $tabla WHERE correo = ? AND estado = 1");
    $st->execute([$correo]);
    $u = $st->fetch();

    if ($u && password_verify($clave, $u["password"])) {
        Auth::iniciarSesion((int)$u["id"], $rol, $u["nombre"], !empty($datos["recordar"]));
        Response::success(["id" => (int)$u["id"], "rol" => $rol, "nombre" => $u["nombre"]], "Bienvenido");
    }
}

// Ningún rol coincidió
Response::error("Correo o contraseña incorrectos", -1, 401);
