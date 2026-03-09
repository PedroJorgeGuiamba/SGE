<?php

class Conector {
    private $conexao;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        $env = parse_ini_file(__DIR__ . '/../Config/.env');

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
    }

    public function getConexao() {
        return $this->conexao;
    }
}