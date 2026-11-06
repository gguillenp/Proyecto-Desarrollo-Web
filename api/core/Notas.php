<?php
// Reglas de calificación
class Notas
{
    // Nota mínima para aprobar
    public const MINIMA = 61;

    // aprobado, reprobado o pendiente según asistencia y nota
    public static function resultado($asistio, $nota): string
    {
        if ($asistio !== null && (int)$asistio === 0) {
            return "reprobado";
        }
        if ($nota === null) {
            return "pendiente";
        }
        return (float)$nota >= self::MINIMA ? "aprobado" : "reprobado";
    }
}
