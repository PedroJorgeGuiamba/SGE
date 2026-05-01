<?php

class PedidoDeCredencial
{
    private int $id_pedido_carta;
    private int $codigoFormando;
    private string $contactoFormando;
    private string $email;
    private string $empresa;
    private string $dataPedido;
    private string $cartaPath;

    // Getters e setters
    public function setIdPedido($id)
    {
        $this->id_pedido_carta = $id;
    }

    public function setCodigoFormando(int $codigoFormando)
    {
        $this->codigoFormando = $codigoFormando;
    }
    public function setContactoFormando(string $contactoFormando)
    {
        $this->contactoFormando = $contactoFormando;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setEmpresa(string $empresa)
    {
        $this->empresa = $empresa;
    }
    public function setDataPedido(string $dataPedido)
    {
        $this->dataPedido = $dataPedido;
    }
    public function setCartaPath(string $cartaPath)
    {
        $this->cartaPath = $cartaPath;
    }

    public function buscarNomeEApelido(int $codigo, mysqli $conn): ?array
    {
        $stmt = $conn->prepare("SELECT nome, apelido FROM formando WHERE codigo = ?");
        if ($stmt === false) {
            return null;
        }
        $stmt->bind_param("i", $codigo);
        if (!$stmt->execute()) {
            return null;
        }
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function salvar(string $nome, string $apelido, mysqli $conn): bool
    {
        $stmt = $conn->prepare("INSERT INTO credencial_estagio (id_pedido_carta, codigo_formando, nome, apelido, contactoFormando, email, empresa, data_do_pedido, carta_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iisssssss",
            $this->id_pedido_carta,
            $this->codigoFormando,
            $nome,
            $apelido,
            $this->contactoFormando,
            $this->email,
            $this->empresa,
            $this->dataPedido,
            $this->cartaPath
        );

        return $stmt->execute();
    }

    public function actualizar(string $nome, string $apelido,int $codigo_formando, string $contactoFormando, string $empresa, string $email, int $id_credencial, mysqli $conn): bool
    {
        $stmt= $conn->prepare("UPDATE credencial_estagio SET
                    nome = ?,
                    apelido = ?,
                    codigo_formando = ?,
                    contactoFormando = ?,
                    empresa = ?,
                    email = ?
                    WHERE id_credencial = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssisssi",
                $nome,
                $apelido,
                $codigo_formando,
                $contactoFormando,
                $empresa,
                $email,
                $id_credencial
        );

        return $stmt->execute();
    }

    public function LastInsertId($conn){
        return $conn->insert_id;
    }
}
