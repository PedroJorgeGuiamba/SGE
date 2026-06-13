<?php

class Conector {
    private mysqli $conexao;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $dbHost = getenv('DB_HOST') ?: null;
        $dbUser = getenv('DB_USER') ?: null;
        $dbPass = getenv('DB_PASS') ?: null;
        $dbName = getenv('DB_NAME_ITC') ?: null;
        $dbPort = getenv('DB_PORT') ?: null;

        if (!$dbHost || !$dbUser || !$dbPass || !$dbName) {
            $envPath = __DIR__ . '/../config/.env';
            
            if (file_exists($envPath)) {
                $env = parse_ini_file($envPath);
                
                $dbHost = $dbHost ?: ($env['DB_HOST'] ?? null);
                $dbUser = $dbUser ?: ($env['DB_USER'] ?? null);
                $dbPass = $dbPass ?: ($env['DB_PASS'] ?? null);
                $dbName = $dbName ?: ($env['DB_NAME_ITC'] ?? null);
                $dbPort = $dbPort ?: ($env['DB_PORT'] ?? null);
            }
        }

        $missing = [];
        if (!$dbHost) $missing[] = 'DB_HOST';
        if (!$dbUser) $missing[] = 'DB_USER';
        if (!$dbPass) $missing[] = 'DB_PASS';
        if (!$dbName) $missing[] = 'DB_NAME_ITC';
        if (!$dbPort) $missing[] = 'DB_PORT';

        if (!empty($missing)) {
            throw new RuntimeException(
                "Variáveis em falta: " . implode(', ', $missing) . 
                ". Configure no Infisical ou no arquivo .env"
            );
        }

        $this->conexao = mysqli_connect(
            $dbHost,
            $dbUser,
            $dbPass,
            $dbName,
            (int) $dbPort
        );

        if ($this->conexao->connect_error) {
            throw new RuntimeException('Erro de ligação: ' . $this->conexao->connect_error);
        }

        $this->conexao->set_charset('utf8mb4');
    }

    public function getConexao() {
        return $this->conexao;
    }
}