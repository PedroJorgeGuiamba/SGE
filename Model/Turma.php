<?php
class Turma
{
    private int $codigo;
    private string $nome;
    private string $ano;
    private string $tipo;
    private string $turno;
    private int $codigoCurso;
    private int $codigoQualificacao;

    // Getters e setters
    public function setCodigo(int $codigo)
    {
        $this->codigo = $codigo;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }
    public function setAno(string $ano)
    {
        $this->ano = $ano;
    }
    public function setTipo(string $tipo)
    {
        $this->tipo = $tipo;
    }
    public function setTurno(string $turno)
    {
        $this->turno = $turno;
    }

    public function setCodigoCurso(int $codigoCurso)
    {
        $this->codigoCurso = $codigoCurso;
    }

    public function setCodigoQualificacao(int $codigoQualificacao)
    {
        $this->codigoQualificacao = $codigoQualificacao;
    }

    public function salvar(mysqli $conn): bool
    {
        $stmt = $conn->prepare("INSERT INTO turma (codigo, nome, ano_lectivo, tipo, turno, codigo_curso, codigo_qualificacao) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "issssii",
            $this->codigo,
            $this->nome,
            $this->ano,
            $this->tipo,
            $this->turno,
            $this->codigoCurso,
            $this->codigoQualificacao
        );

        return $stmt->execute();
    }

    public function actualizar(int $codigo, string $nome, string $codigo_curso, int $codigo_qualificacao,  mysqli $conn): bool
    {
        $stmt = $conn->prepare("UPDATE turma SET
            nome = ?,
            codigo_curso = ?,
            codigo_qualificacao = ?
            WHERE codigo = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("siii",
                $nome,
                $codigo_curso,
                $codigo_qualificacao,
                $codigo
            );

        return $stmt->execute();
    }
}
