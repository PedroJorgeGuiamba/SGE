<?php
require_once __DIR__ . '/../Conexao/conector.php';
class AvaliarEstagio{
    private $id_resposta;
    private $numero_pedido;
    private $qualificacao;
    private $resultado;
    private $docPath;
    private $conn;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }

    public function getResultado(){
        return $this->resultado;
    }

        public function setId_resposta(int $id_resposta)
    {
        $this->id_resposta = $id_resposta;
    }

    public function setNumPedido(int $numero_pedido)
    {
        $this->numero_pedido = $numero_pedido;
    }

    public function setQualificacao(int $qualificacao)
    {
        $this->qualificacao = $qualificacao;
    }

    public function setResultado(string $resultado){
        $this->resultado = $resultado;
    }

    public function setDocPath(string $docPath){
        $this->docPath = $docPath;
    }

    public function salvar(): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO avaliacao_estagio (id_resposta, numero_pedido, qualificacao, resultado, docPath) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "iiiss",
            $this->id_resposta,
            $this->numero_pedido,
            $this->qualificacao,
            $this->resultado,
            $this->docPath
        );

        return $stmt->execute();
    }
}