<?php
require_once __DIR__ . '/../Conexao/conector.php';

class PedidoDeCredencial
{
    private $conn;

    private $id_pedido_carta;
    private $codigoFormando;
    private $contactoFormando;
    private $email;
    private $empresa;
    private $dataPedido;

    public function __construct($conn = null)
    {
        if ($conn instanceof mysqli) {
            $this->conn = $conn;
            return;
        }

        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }

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

    public function buscarNomeEApelido(int $codigo): ?array
    {
        $stmt = $this->conn->prepare("SELECT nome, apelido FROM formando WHERE codigo = ?");
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

    public function salvar(string $nome, string $apelido): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO credencial_estagio (id_pedido_carta, codigo_formando, nome, apelido, contactoFormando, email, empresa, data_do_pedido) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iissssss",
            $this->id_pedido_carta,
            $this->codigoFormando,
            $nome,
            $apelido,
            $this->contactoFormando,
            $this->email,
            $this->empresa,
            $this->dataPedido,
        );

        return $stmt->execute();
    }

    public function actualizar(string $nome, string $apelido,int $codigo_formando, string $contactoFormando, string $empresa, string $email, int $id_credencial): bool
    {
        $stmt= $this->conn->prepare("UPDATE credencial_estagio SET
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
