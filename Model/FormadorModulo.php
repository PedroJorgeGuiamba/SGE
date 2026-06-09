<?php
// Model/FormadorModulo.php

class FormadorModulo
{
    private int $idModulo;
    private int $idFormador;
    private int $idModuloTurma;
    private string $data_inicio;
    private string $data_fim;
    private int $carga_horaria;

    public function setIdModulo(int $idModulo): void
    {
        $this->idModulo = $idModulo;
    }
    public function setIdModuloTurma(int $idModuloTurma): void
    {
        $this->idModuloTurma = $idModuloTurma;
    }
    public function setIdFormador(int $idFormador): void
    {
        $this->idFormador = $idFormador;
    }
    public function setCargaHoraria(int $carga_horaria): void
    {
        $this->carga_horaria = $carga_horaria;
    }
    public function setDataI(string $data_inicio): void
    {
        $this->data_inicio = $data_inicio;
    }
    public function setDataF(string $data_fim): void
    {
        $this->data_fim = $data_fim;
    }
    
    public function salvar(mysqli $conn): bool
    {
        $stmt = $conn->prepare(
            "INSERT INTO formador_modulo (id_modulo, id_formador) VALUES (?, ?)"
        );

        if (!$stmt) return false;

        $stmt->bind_param("ii", $this->idModulo, $this->idFormador);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
    public function salvarLecionarModulo(mysqli $conn): bool
    {
        $stmt = $conn->prepare(
            "INSERT INTO leciona_modulo (id_formador, id_modulo_turma, data_inicio, data_fim, carga_horaria_semanal)
            VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) return false;

        $stmt->bind_param("iissi", $this->idFormador, $this->idModuloTurma, $this->data_inicio, $this->data_fim, $this->carga_horaria);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function buscarFormadorModulo(mysqli $conn){
        $sql = "SELECT id_formador, id_modulo FROM formador_modulo WHERE id_formador = ? AND id_modulo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $this->idFormador, $this->idModulo);
        $stmt->execute();
        $result = $stmt ->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }
}