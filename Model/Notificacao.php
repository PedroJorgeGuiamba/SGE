<?php

class Notificacao{
    private $id_utilizador;
    private $mensagem;

    public function setId_Utilizador($r){$this->id_utilizador = $r;}
    public function setMensagem($f){$this->mensagem = $f;}

    public function salvar($conn){
        $sql = "INSERT INTO notificacao (
                mensagem,
                id_utilizador)
                VALUES (?, ?);";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $this->mensagem, $this->id_utilizador);
        $resultado = $stmt->execute();

        if (!$resultado) {
            error_log("Erro ao inserir notificacao: " . $stmt->error);
        }

        $stmt->close();
        return $resultado;
    }
}

?>