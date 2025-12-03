<?php
require_once __DIR__ . '/../Conexao/conector.php';

class Estagio{
    private $id_resposta;
    private $codigo_formando;
    private $id_empresa;
    private $id_supervisor;
    private $data_inicio;
    private $data_fim;
    private $status;
    private $observacoes;

    public function setId_resposta($r){$this->id_resposta = $r;}
    public function setCodigo($f){$this->codigo_formando = $f;}
    public function setId_empresa($e){$this->id_empresa = $e;}
    public function setId_supervisor($s){$this->id_supervisor = $s;}
    public function setDataI($i){$this->data_inicio = $i;}
    public function setDataF($f){$this->data_fim = $f;}
    public function setStatus($st){$this->status = $st;}
    public function setObs($o){$this->observacoes = $o;}

    public function salvar(){
        $conexao = new Conector();
        $conn = $conexao->getConexao();

        $sql = "INSERT INTO estagio (
                codigo_formando,
                id_empresa,
                id_supervisor,
                data_inicio,
                data_fim,
                status,
                observacoes,
                id_resposta)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiissssi", $this->codigo_formando, $this->id_empresa, $this->id_supervisor, $this->data_inicio, $this->data_fim, $this->status, $this->observacoes, $this->id_resposta);
        $resultado = $stmt->execute();

        if (!$resultado) {
            error_log("Erro ao inserir estagio: " . $stmt->error);
        }

        $stmt->close();
        return $resultado;
    }

    public function salvarNoEdit(){
        $conexao = new Conector();
        $conn = $conexao->getConexao();

        $sql = "INSERT INTO estagio (
                codigo_formando,
                id_empresa,
                id_supervisor,
                data_inicio,
                data_fim,
                status,
                id_resposta)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiissssi", $this->codigo_formando, $this->id_empresa, $this->id_supervisor, $this->data_inicio, $this->data_fim, $this->status, $this->observacoes, $this->id_resposta);
        $resultado = $stmt->execute();

        if (!$resultado) {
            error_log("Erro ao inserir estagio: " . $stmt->error);
        }

        $stmt->close();
        return $resultado;
    }

    
}

?>