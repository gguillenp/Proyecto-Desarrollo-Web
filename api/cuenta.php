<?php
// PUT: el usuario cambia su propia contraseña
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Auth.php");
require_once(__DIR__ . "/core/Usuarios.php");

$pdo = new Connection();
$u = Auth::requiere($pdo);

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    Usuarios::cambiarClave($pdo, Auth::tabla($u["rol"]), $u["id"], Request::body());
    Response::success(null, "Contraseña actualizada");
}

Response::error("Método no permitido", -1, 405);
