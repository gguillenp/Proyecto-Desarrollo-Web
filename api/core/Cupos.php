<?php
// Conteo de cupos ocupados
class Cupos
{
    // Estudiantes inscritos en una tutoría
    public static function ocupados(PDO $pdo, int $tutoriaId): int
    {
        $st = $pdo->prepare("SELECT COUNT(*) FROM inscripciones WHERE tutoria_id = ?");
        $st->execute([$tutoriaId]);
        return (int)$st->fetchColumn();
    }
}
