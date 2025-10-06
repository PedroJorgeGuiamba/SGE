<?php
require_once __DIR__ . '/../Conexao/conector.php';

class Resposta
{
    private $conn;

    private $numero;
    private $status;
    private $dataResposta;
    private $contactoResponsavel;
    private $dataInicio;
    private $dataFim;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }

    // Getters e setters
    public function setNumero(int $numero)
    {
        $this->numero = $numero;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function setDataResposta(string $dataResposta)
    {
        $this->dataResposta = $dataResposta;
    }

    public function setContactoResponsavel(string $contactoResponsavel)
    {
        $this->contactoResponsavel = $contactoResponsavel;
    }

    public function setDataInicio(string $dataInicio)
    {
        $this->dataInicio = $dataInicio;
    }

    public function setDataFim(string $dataFim)
    {
        $this->dataFim = $dataFim;
    }

    public function salvar(): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO resposta_carta (numero_carta, status_resposta, data_resposta, contato_responsavel, data_inicio_estagio, data_fim_estagio) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "isssss",
            $this->numero,
            $this->status,
            $this->dataResposta,
            $this->contactoResponsavel,
            $this->dataInicio,
            $this->dataFim,

        );

        return $stmt->execute();
    }
}
