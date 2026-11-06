<?php
// GET: devuelve el usuario con sesión activa (o 401)
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Auth.php");

$pdo = new Connection();
$usuario = Auth::usuario($pdo);

if (!$usuario) {
    Response::error("Sin sesión", -1, 401);
}
Response::success($usuario);
