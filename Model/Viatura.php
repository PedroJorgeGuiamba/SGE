<?php
require_once __DIR__ . '/../Conexao/conector.php';

class Viatura
{
    private $conn;

    private $marca;
    private $modelo;
    private $matricula;
    private $cor;
    private $aprovado;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }

    // Getters e setters
    public function setMarca(int $marca)
    {
        $this->marca = $marca;
    }

    public function setmodelo(int $modelo)
    {
        $this->modelo = $modelo;
    }

    public function setmatricula(int $matricula)
    {
        $this->matricula = $matricula;
    }

    public function setaprovado(string $aprovado)
    {
        $this->aprovado = $aprovado;
    }


    public function salvar(): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO viaturas (marca, modelo, matricula, cor, aprovado) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "ssssi",
            $this->marca,
            $this->modelo,
            $this->matricula,
            $this->cor,
            $this->aprovado,
        );

        return $stmt->execute();
    }
}
