<?php

class Conector {
    private mysqli $conexao;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $envPath = __DIR__ . '/../config/.env';
        
        if (!file_exists($envPath)) {
            throw new RuntimeException("Ficheiro .env não encontrado em: $envPath");
        }
        
        $env = parse_ini_file(__DIR__ . '/../config/.env');

        foreach (['DB_HOST', 'DB_USER', 'DB_NAME_ITC', 'DB_PASS', 'DB_PORT'] as $key) {
            if (empty($env[$key])) {
                throw new RuntimeException("Variável '$key' em falta ou vazia no .env");
            }
        }

        foreach ($env as $key => $value) {
            putenv("$key=$value");
        }

        $this->conexao = mysqli_connect(
            getenv("DB_HOST"),
            getenv("DB_USER"),
            getenv("DB_PASS"),
            getenv("DB_NAME_ITC"),
            (int) getenv("DB_PORT")
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