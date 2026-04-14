<?php
require_once __DIR__ . '/../Conexao/conector.php';

class PedidoDeVisita
{
    private $conn;
    private $id_visita;
    private $id_pedido_carta;
    private $codigoFormando;
    private $contactoFormando;
    private $empresa;
    private $endereco;
    private $nomeSupervisor;
    private $contactoSupervisor;
    private $dataHoraVisita;
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
    public function setIdVisita($id)
    {
        $this->id_visita = $id;
    }

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

    public function setEmpresa(string $empresa)
    {
        $this->empresa = $empresa;
    }

    public function setEndereco(string $endereco)
    {
        $this->endereco = $endereco;
    }

    public function setNomeSupervisor(string $nomeSupervisor)
    {
        $this->nomeSupervisor = $nomeSupervisor;
    }

    public function setContactoSupervisor(string $contactoSupervisor)
    {
        $this->contactoSupervisor = $contactoSupervisor;
    }

    public function setDataHoraVisita(string $dataHoraVisita)
    {
        $this->dataHoraVisita = $dataHoraVisita;
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
        $stmt = $this->conn->prepare("INSERT INTO visita_estagio (id_pedido_carta, codigo_formando, nome, apelido, contactoFormando, empresa, endereco, nomeSupervisor, contactoSupervisor, dataHoraDaVisita, data_do_pedido) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iisssssssss",
            $this->id_pedido_carta,
            $this->codigoFormando,
            $nome,
            $apelido,
            $this->contactoFormando,
            $this->empresa,
            $this->endereco,
            $this->nomeSupervisor,
            $this->contactoSupervisor,
            $this->dataHoraVisita,
            $this->dataPedido,
        );

        return $stmt->execute();
    }
    public function actualizar(string $nome, string $apelido, int $codigo_formando, string $contactoFormando, string $empresa, string $endereco, string $nomeSupervisor, string $contactoSupervisor, string $dataHoraDaVisita, int $id_visita): bool
    {
        $stmt= $this->conn->prepare("UPDATE visita_estagio SET
                    nome = ?,
                    apelido = ?,
                    codigo_formando = ?,
                    contactoFormando = ?,
                    empresa = ?,
                    endereco = ?,
                    nomeSupervisor = ?,
                    contactoSupervisor = ?,
                    dataHoraDaVisita = ?
                    WHERE id_visita = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssissssssi",
                $nome,
                $apelido,
                $codigo_formando,
                $contactoFormando,
                $empresa,
                $endereco,
                $nomeSupervisor,
                $contactoSupervisor,
                $dataHoraDaVisita,
                $id_visita
            );

        return $stmt->execute();
    }

    public function LastInsertId($conn){
        return $conn->insert_id;
    }
}
