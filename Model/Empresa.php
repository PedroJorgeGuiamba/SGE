<?php

class Empresa{
    public $nome;
    public $abreviatura;

    public function setNome($nome){$this->nome = $nome;}
    public function setAbr($abreviatura){$this->abreviatura = $abreviatura;}

    public function salvar($conn){
        if (empty($this->nome)) {
            error_log("Tentativa de salvar empresa sem nome");
            return false;
        }
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

    function getSiglaEmpresa($nomeEmpresa) {
        $palavrasIgnorar = ['DE', 'DA', 'DO', 'DAS', 'DOS', 'E', 'COM', 'EM', 'PARA', 'UM', 'UMA', 'LTDA', 'LDA', 'SA'];
        
        $nomeEmpresa = strtoupper(trim($nomeEmpresa));
        $palavras = preg_split('/\s+/', $nomeEmpresa);
        $sigla = '';
        
        foreach ($palavras as $palavra) {
            // Pega primeira letra se não for palavra ignorada
            if (!in_array($palavra, $palavrasIgnorar) && !empty($palavra)) {
                $sigla .= $palavra[0];
            }
            
            // Limita a 5 caracteres
            if (strlen($sigla) >= 5) {
                $sigla = substr($sigla, 0, 5);
                break;
            }
        }
        
        return $sigla ?: substr($nomeEmpresa, 0, 3); // Fallback
    }
}