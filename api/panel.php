<?php
// GET: números del panel según el rol
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Auth.php");
require_once(__DIR__ . "/core/Notas.php");

$pdo = new Connection();
$u = Auth::requiere($pdo);
$ahora = date("Y-m-d H:i:s");

// Ejecuta un COUNT(*) y devuelve el número
function contar(PDO $pdo, string $sql, array $args): int
{
    $st = $pdo->prepare($sql);
    $st->execute($args);
    return (int)$st->fetchColumn();
}

// Panel del admin
if ($u["rol"] === "admin") {
    Response::success([
        "rol" => "admin",
        "profesores" => contar($pdo, "SELECT COUNT(*) FROM profesores", []),
        "estudiantes" => contar($pdo, "SELECT COUNT(*) FROM estudiantes", []),
        "tutorias" => contar($pdo, "SELECT COUNT(*) FROM tutorias", []),
        "materias" => contar($pdo, "SELECT COUNT(*) FROM materias", []),
    ]);
}

// Panel del profesor: solo sus tutorías
if ($u["rol"] === "profesor") {
    $id = $u["id"];
    $resumen = [
        "rol" => "profesor",
        "tutorias_total"        => contar($pdo, "SELECT COUNT(*) FROM tutorias WHERE profesor_id = ?", [$id]),
        "tutorias_abiertas"     => contar($pdo, "SELECT COUNT(*) FROM tutorias WHERE profesor_id = ? AND estado = 'abierta' AND TIMESTAMP(fecha, hora_fin) > ?", [$id, $ahora]),
        "tutorias_confirmadas"  => contar($pdo, "SELECT COUNT(*) FROM tutorias WHERE profesor_id = ? AND estado = 'confirmada' AND TIMESTAMP(fecha, hora_fin) > ?", [$id, $ahora]),
        "tutorias_finalizadas"  => contar($pdo, "SELECT COUNT(*) FROM tutorias WHERE profesor_id = ? AND estado <> 'cancelada' AND TIMESTAMP(fecha, hora_fin) <= ?", [$id, $ahora]),
        "estudiantes_inscritos" => contar($pdo, "SELECT COUNT(*) FROM inscripciones i JOIN tutorias t ON t.id = i.tutoria_id WHERE t.profesor_id = ?", [$id]),
        "solicitudes_pendientes" => contar($pdo, "SELECT COUNT(*) FROM solicitudes s JOIN tutorias t ON t.id = s.tutoria_id WHERE t.profesor_id = ? AND s.estado = 'pendiente'", [$id]),
        "por_calificar" => contar($pdo, "SELECT COUNT(*) FROM inscripciones i JOIN tutorias t ON t.id = i.tutoria_id
            LEFT JOIN notas n ON n.inscripcion_id = i.id
            WHERE t.profesor_id = ? AND t.estado <> 'cancelada' AND TIMESTAMP(t.fecha, t.hora_fin) <= ?
            AND (n.id IS NULL OR (n.asistio = 1 AND n.nota IS NULL))", [$id, $ahora]),
    ];

    // Tabla de sus tutorías con cupos
    $st = $pdo->prepare("SELECT t.id, t.titulo, t.tema, t.fecha, t.cupo_min, t.cupo_max,
            IF(TIMESTAMP(t.fecha, t.hora_fin) <= ?, 'finalizada', t.estado) AS estado,
            (SELECT COUNT(*) FROM inscripciones i WHERE i.tutoria_id = t.id) AS ocupados
        FROM tutorias t WHERE t.profesor_id = ? AND t.estado <> 'cancelada' ORDER BY t.fecha");
    $st->execute([$ahora, $id]);
    $resumen["tutorias"] = $st->fetchAll();
    Response::success($resumen);
}

// Panel del estudiante
$id = $u["id"];
$base = "SELECT COUNT(*) FROM solicitudes s JOIN tutorias t ON t.id = s.tutoria_id WHERE s.estudiante_id = ?";

// Próximas tutorías donde está inscrito
$st = $pdo->prepare("SELECT t.id, t.titulo, t.tema, t.fecha, t.hora_inicio, p.nombre AS profesor, t.estado
    FROM inscripciones i JOIN tutorias t ON t.id = i.tutoria_id JOIN profesores p ON p.id = t.profesor_id
    WHERE i.estudiante_id = ? AND TIMESTAMP(t.fecha, t.hora_fin) > ? AND t.estado <> 'cancelada'
    ORDER BY t.fecha, t.hora_inicio LIMIT 5");
$st->execute([$id, $ahora]);

Response::success([
    "rol" => "estudiante",
    "disponibles" => contar($pdo, "SELECT COUNT(*) FROM tutorias WHERE estado <> 'cancelada' AND TIMESTAMP(fecha, hora_inicio) > ?", [$ahora]),
    "pendientes"  => contar($pdo, $base . " AND s.estado = 'pendiente'", [$id]),
    "inscritas"   => contar($pdo, $base . " AND s.estado = 'aceptada' AND t.estado <> 'cancelada' AND TIMESTAMP(t.fecha, t.hora_fin) > ?", [$id, $ahora]),
    "finalizadas" => contar($pdo, $base . " AND s.estado = 'aceptada' AND t.estado <> 'cancelada' AND TIMESTAMP(t.fecha, t.hora_fin) <= ?", [$id, $ahora]),
    "aprobadas"   => contar($pdo, "SELECT COUNT(*) FROM notas n JOIN inscripciones i ON i.id = n.inscripcion_id
        WHERE i.estudiante_id = ? AND n.asistio = 1 AND n.nota >= ?", [$id, Notas::MINIMA]),
    "rechazadas"  => contar($pdo, $base . " AND s.estado = 'rechazada'", [$id]),
    "canceladas"  => contar($pdo, $base . " AND (s.estado = 'cancelada' OR (s.estado = 'aceptada' AND t.estado = 'cancelada'))", [$id]),
    "proximas"    => $st->fetchAll(),
]);
