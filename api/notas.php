<?php
// Calificaciones de una tutoría (profesor dueño o admin)
require_once(__DIR__ . "/Connection.php");
require_once(__DIR__ . "/core/Request.php");
require_once(__DIR__ . "/core/Auth.php");
require_once(__DIR__ . "/core/Notas.php");

$pdo = new Connection();
$u = Auth::requiere($pdo);
$metodo = $_SERVER["REQUEST_METHOD"];

// Carga la tutoría, revisa permisos y si ya terminó
function tutoria_calificable(PDO $pdo, int $id, array $u): array
{
    $st = $pdo->prepare("SELECT id, profesor_id, estado, fecha, hora_fin FROM tutorias WHERE id = ?");
    $st->execute([$id]);
    $t = $st->fetch();
    $esDueno = $t && $u["rol"] === "profesor" && (int)$t["profesor_id"] === $u["id"];
    if (!$t || (!$esDueno && $u["rol"] !== "admin")) {
        Response::error("Tutoría no encontrada", -1, 404);
    }
    $t["finalizada"] = $t["estado"] !== "cancelada" && $t["fecha"] . " " . $t["hora_fin"] <= date("Y-m-d H:i:s");
    return $t;
}

// GET: inscritos con su nota y resultado
if ($metodo === "GET") {
    $t = tutoria_calificable($pdo, (int)($_GET["tutoria_id"] ?? 0), $u);
    $st = $pdo->prepare("SELECT i.id AS inscripcion_id, e.nombre AS estudiante, e.correo, n.asistio, n.nota, n.comentario
        FROM inscripciones i
        JOIN estudiantes e ON e.id = i.estudiante_id
        LEFT JOIN notas n ON n.inscripcion_id = i.id
        WHERE i.tutoria_id = ?
        ORDER BY e.nombre");
    $st->execute([$t["id"]]);
    $inscritos = array_map(function ($fila) {
        $fila["resultado"] = Notas::resultado($fila["asistio"], $fila["nota"]);
        return $fila;
    }, $st->fetchAll());

    Response::success(["finalizada" => $t["finalizada"], "nota_minima" => Notas::MINIMA, "inscritos" => $inscritos]);
}

// PUT: guarda las notas de todo el grupo
if ($metodo === "PUT") {
    $d = Request::body();
    $t = tutoria_calificable($pdo, (int)($d["tutoria_id"] ?? 0), $u);
    if (!$t["finalizada"]) {
        Response::error("Solo se puede calificar cuando la tutoría ya terminó");
    }
    $notas = $d["notas"] ?? [];
    if (!is_array($notas) || !$notas) {
        Response::error("No hay notas para guardar");
    }

    // Consultas reutilizadas en el ciclo
    $propia = $pdo->prepare("SELECT 1 FROM inscripciones WHERE id = ? AND tutoria_id = ?");
    $guardar = $pdo->prepare("INSERT INTO notas (inscripcion_id, asistio, nota, comentario) VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE asistio = VALUES(asistio), nota = VALUES(nota), comentario = VALUES(comentario)");

    // Todo o nada: si una nota falla, no se guarda ninguna
    $pdo->beginTransaction();
    foreach ($notas as $n) {
        $inscripcion = (int)($n["inscripcion_id"] ?? 0);
        $asistio = empty($n["asistio"]) ? 0 : 1;
        $valor = $n["nota"] ?? null;
        $comentario = Request::texto($n["comentario"] ?? "");

        $propia->execute([$inscripcion, $t["id"]]);
        if (!$propia->fetch()) {
            $pdo->rollBack();
            Response::error("Hay un estudiante que no está inscrito en esta tutoría", -1, 403);
        }
        if ($valor === "" || $valor === null) {
            $valor = null;
        } elseif (!is_numeric($valor) || $valor < 0 || $valor > 100) {
            $pdo->rollBack();
            Response::error("Las notas deben estar entre 0 y 100");
        }
        if (mb_strlen($comentario) > 255) {
            $pdo->rollBack();
            Response::error("El comentario es demasiado largo (máx. 255 caracteres)");
        }
        $guardar->execute([$inscripcion, $asistio, $valor, $comentario === "" ? null : $comentario]);
    }
    $pdo->commit();
    Response::success(null, "Notas guardadas");
}

Response::error("Método no permitido", -1, 405);
