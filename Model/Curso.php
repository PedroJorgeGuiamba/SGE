<?php

class Curso
{
    private $codigo;
    private $nome;
    private $descricao;
    private $sigla;
    private $id_qualificacao;

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
    public function salvar($conn): bool
    {
        $stmt = $conn->prepare("INSERT INTO curso (codigo, nome, descricao, sigla, codigo_qualificacao) VALUES (?, ?, ?, ?, ?)");

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

    public function actualizar(int $codigo, string $nome, string $descricao, string $sigla, int $codigo_qualificacao, int $id_curso, mysqli $conn): bool
    {
        $stmt = $conn->prepare("UPDATE curso SET
            codigo = ?,
            nome = ?,
            descricao = ?,
            sigla = ?,
            codigo_qualificacao = ?
            WHERE id_curso = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("isssii",
                $codigo,
                $nome,
                $descricao,
                $sigla,
                $codigo_qualificacao,
                $id_curso
            );

        return $stmt->execute();
    }
}
