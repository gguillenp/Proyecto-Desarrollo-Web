<?php
// Protección de páginas del lado del servidor
require_once(__DIR__ . "/../Connection.php");
require_once(__DIR__ . "/Auth.php");

class Pagina
{
    // Página con sesión (y rol opcional); si no, redirige
    public static function privada(?string $rol = null): array
    {
        $usuario = Auth::usuario(new Connection());
        if (!$usuario) {
            self::ir("index.php");
        }
        if ($rol !== null && $usuario["rol"] !== $rol) {
            self::ir("panel.php");
        }
        return $usuario;
    }

    // Página sin sesión (login, registro); si ya entró, va al panel
    public static function publica(): void
    {
        if (Auth::usuario(new Connection())) {
            self::ir("panel.php");
        }
    }

    // Redirige y termina
    private static function ir(string $pagina): void
    {
        header("Location: " . $pagina);
        exit;
    }
}
