<?php
// Operaciones de cuentas: crear, activar/desactivar y cambiar contraseña
require_once(__DIR__ . "/Request.php");
require_once(__DIR__ . "/Response.php");

class Usuarios
{
    // Crea un usuario validando datos y correo único
    public static function crear(PDO $pdo, string $tabla, array $datos): int
    {
        $nombre = Request::texto($datos["nombre"] ?? "");
        $correo = strtolower(Request::texto($datos["correo"] ?? ""));
        $clave = (string)($datos["password"] ?? "");

        if (mb_strlen($nombre) < 3) {
            Response::error("El nombre debe tener al menos 3 caracteres");
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Response::error("Correo inválido");
        }
        if (strlen($clave) < 6) {
            Response::error("La contraseña debe tener al menos 6 caracteres");
        }

        // El correo no puede repetirse en ninguna de las tres tablas
        $st = $pdo->prepare("SELECT 1 FROM administradores WHERE correo = ?
            UNION SELECT 1 FROM profesores WHERE correo = ?
            UNION SELECT 1 FROM estudiantes WHERE correo = ?");
        $st->execute([$correo, $correo, $correo]);
        if ($st->fetch()) {
            Response::error("Ese correo ya está registrado", -1, 409);
        }

        $st = $pdo->prepare("INSERT INTO $tabla (nombre, correo, password) VALUES (?, ?, ?)");
        $st->execute([$nombre, $correo, password_hash($clave, PASSWORD_DEFAULT)]);
        return (int)$pdo->lastInsertId();
    }

    // Activa (1) o desactiva (0) una cuenta
    public static function cambiarEstado(PDO $pdo, string $tabla, array $datos): void
    {
        $id = (int)($datos["id"] ?? 0);
        $estado = (int)($datos["estado"] ?? -1);
        if (!in_array($estado, [0, 1], true)) {
            Response::error("Estado inválido");
        }
        $st = $pdo->prepare("UPDATE $tabla SET estado = ? WHERE id = ?");
        $st->execute([$estado, $id]);
        if ($st->rowCount() === 0) {
            $existe = $pdo->prepare("SELECT 1 FROM $tabla WHERE id = ?");
            $existe->execute([$id]);
            if (!$existe->fetch()) {
                Response::error("Usuario no encontrado", -1, 404);
            }
        }
    }

    // Cambia la contraseña pidiendo la actual
    public static function cambiarClave(PDO $pdo, string $tabla, int $id, array $datos): void
    {
        $actual = (string)($datos["actual"] ?? "");
        $nueva = (string)($datos["nueva"] ?? "");
        if (strlen($nueva) < 6) {
            Response::error("La nueva contraseña debe tener al menos 6 caracteres");
        }
        $st = $pdo->prepare("SELECT password FROM $tabla WHERE id = ?");
        $st->execute([$id]);
        $fila = $st->fetch();
        if (!$fila || !password_verify($actual, $fila["password"])) {
            Response::error("La contraseña actual no es correcta", -1, 403);
        }
        $pdo->prepare("UPDATE $tabla SET password = ? WHERE id = ?")->execute([password_hash($nueva, PASSWORD_DEFAULT), $id]);
    }
}
