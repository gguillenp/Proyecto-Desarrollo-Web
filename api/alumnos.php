<?php
// Gestión de estudiantes (solo admin)
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Auth.php");
require_once(__DIR__ . "/core/Usuarios.php");

$pdo = new Connection();
Auth::requiere($pdo, "admin");
$metodo = $_SERVER["REQUEST_METHOD"];

// GET: lista de estudiantes con solicitudes e inscripciones
if ($metodo === "GET") {
    Response::success($pdo->query("SELECT e.id, e.nombre, e.correo, e.estado, e.fecha_creacion,
            (SELECT COUNT(*) FROM solicitudes s WHERE s.estudiante_id = e.id) AS solicitudes,
            (SELECT COUNT(*) FROM inscripciones i WHERE i.estudiante_id = e.id) AS inscripciones
        FROM estudiantes e ORDER BY e.nombre")->fetchAll());
}

// PUT: activa o desactiva un estudiante
if ($metodo === "PUT") {
    Usuarios::cambiarEstado($pdo, "estudiantes", Request::body());
    Response::success(null, "Estado actualizado");
}

Response::error("Método no permitido", -1, 405);
