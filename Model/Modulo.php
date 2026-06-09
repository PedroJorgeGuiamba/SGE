<?php
class Modulo{
    private string $codigoModulo;
    private string $codigoResultado;
    private int $idModulo;
    private int $idCompetencia;
    private string $descricaoDesempenho;
    private string $descricaoEvidencia;
    private int $idQualificacao;
    private string $descricaoModulo;
    private string $descricaoResultado;
    private int $cargaHoraria;
    private string $tipoResultado;
    private string $observacoesResultado;
    private int $numeroCompt;
    private string $descricaoCompt;
    private string $ctxAplicacao;
    private string $tipoAvaliacao;
    private int $percentualAvaliacao;
    private string $observacoesCriterioAvaliacao;
    
    public function setCodigoModulo(string $codigoModulo){
        $this->codigoModulo = $codigoModulo;
    }
    public function setCodigoResultado(string $codigoResultado){
        $this->codigoResultado = $codigoResultado;
    }
    public function setIdModulo(int $idModulo){
        $this->idModulo = $idModulo;
    }
    public function setIdCompetencia(int $idCompetencia){
        $this->idCompetencia = $idCompetencia;
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
    public function setNumeroCompt(int $numeroCompt){
        $this->numeroCompt = $numeroCompt;
    }
    public function setDescricaoCompt(string $descricaoCompt){
        $this->descricaoCompt = $descricaoCompt;
    }
    public function setDescricaoEvid(string $descricaoEvidencia){
        $this->descricaoEvidencia = $descricaoEvidencia;
    }
    public function setDescricaoDesemp(string $descricaoDesempenho){
        $this->descricaoDesempenho = $descricaoDesempenho;
    }
    public function setCtxAplic(string $ctxAplicacao){
        $this->ctxAplicacao = $ctxAplicacao;
    }
    //Criterio Avaliacao
    public function setTipoAvaliacao(string $tipoAvaliacao){
        $this->tipoAvaliacao = $tipoAvaliacao;
    }
    public function setPercentualAvaliacao(int $percentualAvaliacao){
        $this->percentualAvaliacao = $percentualAvaliacao;
    }
    public function setObservacaoAvaliacao(string $observacoesCriterioAvaliacao){
        $this->observacoesCriterioAvaliacao = $observacoesCriterioAvaliacao;
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
    public function salvarElementoComp(mysqli $conn){
        $sql = "INSERT INTO competencia (id_modulo, numero_elemento, descricao_elemento, ctx_aplicacao) VALUES (?, ?, ?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", 
        $this->idModulo, 
        $this->numeroCompt,
        $this->descricaoCompt, 
        $this->ctxAplicacao);
        return $stmt->execute();
    }
    public function salvarCriterioAvaliacao(mysqli $conn){
        $sql = "INSERT INTO criterio_avaliacao (id_modulo, id_tipo_avaliacao, percentual_minimo, observacoes) VALUES (?, ?, ?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", 
        $this->idModulo, 
        $this->tipoAvaliacao,
        $this->percentualAvaliacao,
        $this->observacoesCriterioAvaliacao);
        return $stmt->execute();
    }
    public function salvarCriterioDesempenho(mysqli $conn){
        $sql = "INSERT INTO criterio_desempenho (id_competencia, descricao_desempenho) VALUES (?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", 
        $this->idCompetencia, 
        $this->descricaoDesempenho);
        return $stmt->execute();
    }
    public function salvarEvidenciaRequerida(mysqli $conn){
        $sql = "INSERT INTO evidencia_competencia (id_competencia, descricao_evidencia) VALUES (?, ?);";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", 
        $this->idCompetencia, 
        $this->descricaoEvidencia);
        return $stmt->execute();
    }
}
