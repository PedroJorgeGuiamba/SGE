<?php

require_once __DIR__ . '/../Conexao/conector.php';

class Supervisor{
    private $nome;
    private $id_qualificacao;
    private $user;
    private $area;
    private $conn;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }


    public function setNome(string $nome){$this->nome = $nome;}
    public function setId_Qualificacao(int $id_qualificacao){$this->id_qualificacao = $id_qualificacao;}
    public function setUser(int $user){$this->user = $user;}
    public function setArea(string $area){$this->area = $area;}

    public function salvar(){
        $stmt = $this->conn->prepare("INSERT INTO supervisor (nome_supervisor, id_qualificacao, usuario_id, area) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "sii",
            $this->nome,
            $this->id_qualificacao,
            $this->user,
            $this->area
        );

        return $stmt->execute();
    }
}