<?php

class Supervisor{
    private $nome;
    private $id_qualificacao;
    private $user;
    private $area;

    public function setNome(string $nome){$this->nome = $nome;}
    public function setId_Qualificacao(int $id_qualificacao){$this->id_qualificacao = $id_qualificacao;}
    public function setUser(int $user){$this->user = $user;}
    public function setArea(string $area){$this->area = $area;}

    public function salvar($conn){
        $stmt = $conn->prepare("INSERT INTO supervisor (nome_supervisor, id_qualificacao, usuario_id, area) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "siis",
            $this->nome,
            $this->id_qualificacao,
            $this->user,
            $this->area
        );

        return $stmt->execute();
    }

    public function getSupervisorByIdAndQual($conn, $user, $id_qualificacao){
        $sql = "SELECT id_supervisor FROM supervisor WHERE usuario_id = ? AND id_qualificacao = ?;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user, $id_qualificacao);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function actualizar(string $nome, string $area, int $id_qualificacao, int $id_supervisor, mysqli $conn): bool
    {
        $stmt = $conn->prepare("UPDATE supervisor SET
            nome_supervisor = ?,
            area = ?,
            id_qualificacao = ?
            WHERE id_supervisor = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssii",
                $nome,
                $area,
                $id_qualificacao,
                $id_supervisor
            );

        return $stmt->execute();
    }
}