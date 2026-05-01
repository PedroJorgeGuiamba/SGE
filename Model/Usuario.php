<?php

class Usuario{
    private string $email;
    private string $email_hash;
    private string $senha;
    private string $role;

    public function getEmail() {
        return $this->email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setEmail(string $email) {
        $this->email = $email;
    }
    public function setEmailHash(string $email) {
        $this->email_hash = $email;
    }
    
    public function setSenha(string $senha) {
        $this->senha = $senha;
    }
    public function setRole(string $role) {
        $this->role = $role;
    }

    public function salvar(mysqli $conn){
        $sql = "INSERT INTO usuarios (Email, password, role, email_hash) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $this->email, $this->senha, $this->role, $this->email_hash);

        return $stmt->execute();
    }
    public function getUsers(mysqli $conn){
        $sql = "SELECT id, Email, password, role FROM usuarios";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getUsersByEmail(mysqli $conn, string $email){
        $sql = "SELECT id FROM usuarios WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getUsersByEmailHashed(mysqli $conn, string $email){
        $sql = "SELECT id, password, Email, role FROM usuarios WHERE email_hash = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateRoleById(mysqli $conn, string $role, int $usuario_id){
        $sql = "UPDATE usuarios SET role = ? WHERE usuario_id - ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $role, $usuario_id);
        return $stmt->execute();
    }
}