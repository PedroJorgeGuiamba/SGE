<?php

class Qualificacao
{
    private $id_qualificacao;
    private $qualificacao;
    private $descricao;
    private $nivel;

    // Getters e setters
    public function setQualificacao(int $qualificacao)
    {
        $this->qualificacao = $qualificacao;
    }

    public function setDescricao(string $descricao)
    {
        $this->descricao = $descricao;
    }

    public function setNivel(string $nivel)
    {
        $this->nivel = $nivel;
    }

    public function setIdQualificacao(string $id_qualificacao)
    {
        $this->id_qualificacao = $id_qualificacao;
    }

    public function salvar($conn): bool
    {
        $stmt = $conn->prepare("INSERT INTO qualificacao (qualificacao, descricao, nivel) VALUES (?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "iss",
            $this->qualificacao,
            $this->descricao,
            $this->nivel,
        );

        return $stmt->execute();
    }

    public function actualizar(int $qualificacao, string $descricao, string $nivel, int $id_qualificacao, mysqli $conn): bool
    {
        $stmt = $conn->prepare("UPDATE qualificacao SET
            qualificacao = ?,
            descricao = ?,
            nivel = ?
            WHERE id_qualificacao = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("issi",
                $qualificacao,
                $descricao,
                $nivel,
                $id_qualificacao
            );

        return $stmt->execute();
    }
}
