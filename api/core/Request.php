<?php
// Ayudas para leer lo que manda el navegador
class Request
{
    // Cuerpo JSON de la petición como arreglo
    public static function body(): array
    {
        $datos = json_decode(file_get_contents("php://input"), true);
        return is_array($datos) ? $datos : [];
    }

    // Convierte a texto sin espacios sobrantes
    public static function texto(mixed $valor): string
    {
        return trim((string)($valor ?? ""));
    }
}
