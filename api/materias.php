<?php
// Materias: todos las leen, solo el admin crea y elimina
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Request.php");
require_once(__DIR__ . "/core/Auth.php");

$pdo = new Connection();
Auth::requiere($pdo);
$metodo = $_SERVER["REQUEST_METHOD"];

// GET: lista con el número de tutorías de cada materia
if ($metodo === "GET") {
    Response::success($pdo->query("SELECT m.id, m.nombre,
            (SELECT COUNT(*) FROM tutorias t WHERE t.materia_id = m.id) AS tutorias
        FROM materias m ORDER BY m.nombre")->fetchAll());
}

// Desde aquí, solo admin
Auth::requiere($pdo, "admin");

// POST: crea una materia (nombre único)
if ($metodo === "POST") {
    $nombre = Request::texto(Request::body()["nombre"] ?? "");
    if ($nombre === "" || mb_strlen($nombre) > 80) {
        Response::error("El nombre es obligatorio (máx. 80 caracteres)");
    }
    $st = $pdo->prepare("SELECT 1 FROM materias WHERE nombre = ?");
    $st->execute([$nombre]);
    if ($st->fetch()) {
        Response::error("Esa materia ya existe", -1, 409);
    }
    $pdo->prepare("INSERT INTO materias (nombre) VALUES (?)")->execute([$nombre]);
    Response::success(["id" => (int)$pdo->lastInsertId()], "Materia creada", 201);
}

// DELETE: elimina una materia sin tutorías
if ($metodo === "DELETE") {
    $id = (int)(Request::body()["id"] ?? $_GET["id"] ?? 0);
    $st = $pdo->prepare("SELECT COUNT(*) FROM tutorias WHERE materia_id = ?");
    $st->execute([$id]);
    if ((int)$st->fetchColumn() > 0) {
        Response::error("La materia tiene tutorías: no se puede eliminar", -1, 409);
    }
    $st = $pdo->prepare("DELETE FROM materias WHERE id = ?");
    $st->execute([$id]);
    if ($st->rowCount() === 0) {
        Response::error("Materia no encontrada", -1, 404);
    }
    Response::success(null, "Materia eliminada");
}

Response::error("Método no permitido", -1, 405);
