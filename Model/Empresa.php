<?php
require_once __DIR__ . '/../Conexao/conector.php';

class Empresa{
    public $nome;
    public $abreviatura;

    public function setNome($nome){$this->nome = $nome;}
    public function setAbr($abreviatura){$this->abreviatura = $abreviatura;}

    public function salvar(){
        if (empty($this->nome)) {
            error_log("Tentativa de salvar empresa sem nome");
            return false;
        }

        $conexao = new Conector();
        $conn = $conexao->getConexao();

        $sql = "INSERT INTO empresa (nome, abreviatura, email) VALUES (?, ?, NULL)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $this->nome, $this->abreviatura);
        $resultado = $stmt->execute();

        if (!$resultado) {
            error_log("Erro ao inserir empresa: " . $stmt->error);
        }

        $stmt->close();
        return $resultado;
    }
}