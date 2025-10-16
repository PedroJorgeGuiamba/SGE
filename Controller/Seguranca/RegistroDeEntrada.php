<?php
require_once __DIR__ . '/../Conexao/conector.php';

class RegistroDeEntrada
{
    private $conn;

    private $id_viatura;
    private $tipo;
    private $data_hora;
    private $realizado_por;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }

    // Getters e setters
    public function setId_viatura(int $id_viatura)
    {
        $this->id_viatura = $id_viatura;
    }

    public function setTipo(int $tipo)
    {
        $this->tipo = $tipo;
    }

    public function setData_hora(int $data_hora)
    {
        $this->data_hora = $data_hora;
    }

    public function setRealizado_por(string $realizado_por)
    {
        $this->realizado_por = $realizado_por;
    }


    public function salvar(): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO registros_acesso (id_viatura, tipo, data_hora, realizado_por) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "issi",
            $this->id_viatura,
            $this->tipo,
            $this->data_hora,
            $this->realizado_por,
        );

        return $stmt->execute();
    }
}
