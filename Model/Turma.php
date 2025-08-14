<?php
require_once __DIR__ . '/../Conexao/conector.php';

class Turma
{
    private $conn;

    private $codigo;
    private $nome;
    private $codigoCurso;
    private $codigoQualificacao;

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

    public function setCodigoCurso(string $codigoCurso)
    {
        $this->codigoCurso = $codigoCurso;
    }

    public function setCodigoQualificacao(string $codigoQualificacao)
    {
        $this->codigoQualificacao = $codigoQualificacao;
    }

    public function salvar(): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO turma (codigo, nome, codigo_curso, codigo_qualificacao) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "isii",
            $this->codigo,
            $this->nome,
            $this->codigoCurso,
            $this->codigoQualificacao
        );

        return $stmt->execute();
    }
}
