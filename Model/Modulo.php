<?php
class Modulo{
    private string $codigoModulo;
    private string $codigoResultado;
    private int $idModulo;
    private int $idQualificacao;
    private string $descricaoModulo;
    private string $descricaoResultado;
    private int $cargaHoraria;
    private string $tipoResultado;
    private string $observacoesResultado;

    public function setCodigoModulo(string $codigoModulo){
        $this->codigoModulo = $codigoModulo;
    }
    public function setCodigoResultado(string $codigoResultado){
        $this->codigoResultado = $codigoResultado;
    }
    public function setIdModulo(int $idModulo){
        $this->idModulo = $idModulo;
    }
    public function setIdQualificacao(int $idQualificacao){
        $this->idQualificacao = $idQualificacao;
    }
    public function setDescricaoModulo(string $descricaoModulo){
        $this->descricaoModulo = $descricaoModulo;
    }
    public function setDescricaoResultado(string $descricaoResultado){
        $this->descricaoResultado = $descricaoResultado;
    }
    public function setCargaHoraria(int $cargaHoraria){
        $this->cargaHoraria = $cargaHoraria;
    }
    public function setTipoResultado(string $tipoResultado){
        $this->tipoResultado = $tipoResultado;
    }
    public function setObservacoesResultado(string $observacoesResultado){
        $this->observacoesResultado = $observacoesResultado;
    }
    public function salvarModulo(mysqli $conn){
        $sql = "INSERT INTO modulo (codigo, descricao, carga_horaria) VALUES (?, ?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", 
        $this->codigoModulo, 
        $this->descricaoModulo, 
        $this->cargaHoraria);
        return $stmt->execute();
    }
    
    public function salvarQualModulo(mysqli $conn){
        $sql = "INSERT INTO modulo_qualificacao (codigo_modulo, codigo_qualificacao) VALUES (?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", 
        $this->idModulo, 
        $this->idQualificacao);
        return $stmt->execute();
    }
    public function salvarResultadoAprend(mysqli $conn){
        $sql = "INSERT INTO resultado_aprendizagem (codigo, descricao, tipo, observacoes) VALUES (?, ?, ?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", 
        $this->codigoResultado, 
        $this->descricaoResultado, 
        $this->tipoResultado,
        $this->observacoesResultado);
        return $stmt->execute();
    }
}