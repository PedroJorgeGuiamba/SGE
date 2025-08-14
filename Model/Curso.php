<?php
require_once __DIR__ . '/../Conexao/conector.php';

class Curso
{
    private $conn;

    private $codigo;
    private $nome;
    private $descricao;
    private $sigla;
    private $id_qualificacao;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }

    // Getters e setters
    public function setCodigo(int $codigo)
    {
        $this->codigo = $codigo;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    public function setDescricao(string $descricao)
    {
        $this->descricao = $descricao;
    }

    public function setSigla(string $sigla)
    {
        $this->sigla = $sigla;
    }

    public function setIdQualificacao(int $id_qualificacao)
    {
        $this->id_qualificacao = $id_qualificacao;
    }

    // Salva o curso no banco de dados
    public function salvar(): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO curso (codigo, nome, descricao, sigla, codigo_qualificacao) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "isssi",
            $this->codigo,
            $this->nome,
            $this->descricao,
            $this->sigla,
            $this->id_qualificacao
        );

        return $stmt->execute();
    }
}
