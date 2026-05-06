<?php
require_once __DIR__ . '/../Conexao/conector.php';
class AvaliarEstagio
{
    private int $id_pedido;
    private int $codigo_formando;
    private string $empresa;
    private int $qualificacao;
    private int $turma;
    private int $ano_turma;
    private string $resultado;
    private string $docPath;
    private string $comentario;

    public function setCodigoFormando(int $codigoFormando)
    {
        $this->codigo_formando = $codigoFormando;
    }

    public function setIdPedido(int $id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function setQualificacao(int $qualificacao)
    {
        $this->qualificacao = $qualificacao;
    }

    public function setTurma(int $turma)
    {
        $this->turma = $turma;
    }

    public function setAnoTurma(int $ano_turma)
    {
        $this->ano_turma = $ano_turma;
    }

    public function setResultado(string $resultado)
    {
        $this->resultado = $resultado;
    }

    public function setEmpresa(string $empresa)
    {
        $this->empresa = $empresa;
    }

    public function setDocPath(string $docPath)
    {
        $this->docPath = $docPath;
    }

    public function setComentario(string $comentario)
    {
        $this->comentario = $comentario;
    }

    public function salvar(mysqli $conn): bool
    {
        $stmt = $conn->prepare("INSERT INTO avaliacao_estagio (id_pedido_estagio, codigo_formando, empresa, qualificacao, turma, ano_turma, doc_path, resultado, comentario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "iisiiisss",
            $this->id_pedido,
            $this->codigo_formando,
            $this->empresa,
            $this->qualificacao,
            $this->turma,
            $this->ano_turma,
            $this->docPath,
            $this->resultado,
            $this->comentario
        );

        return $stmt->execute();
    }

    public function actualizar(int $codigo_formando, int $qualificacao, int $codigo_turma, string $empresa, int $ano_turma, string $resultado, string $comentario, int $id_pedido_carta, int $id_avaliacao, mysqli $conn): bool
    {
        $stmt = $conn->prepare("UPDATE avaliacao_estagio SET
            codigo_formando = ?,
            empresa = ?,
            qualificacao = ?,
            turma = ?,
            ano_turma = ?,
            resultado = ?,
            comentario = ?,
            id_pedido_estagio = ?
            WHERE id_avaliacao = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("isiiissii",
                $codigo_formando,
                $empresa,
                $qualificacao,
                $codigo_turma,
                $ano_turma,
                $resultado,
                $comentario,
                $id_pedido_carta,
                $id_avaliacao
            );

        return $stmt->execute();
    }

}
