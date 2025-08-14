<?php
require_once __DIR__ . '/../Conexao/conector.php';

class PedidoDeCarta
{
    private $conn;

    private $codigoFormando;
    private $qualificacao;
    private $turma;
    private $dataPedido;
    private $horaPedido;
    private $empresa;
    private $contactoPrincipal;
    private $contactoSecundario;
    private $email;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }

    // Getters e setters
    public function setCodigoFormando(int $codigoFormando)
    {
        $this->codigoFormando = $codigoFormando;
    }

    public function setQualificacao(int $qualificacao)
    {
        $this->qualificacao = $qualificacao;
    }

    public function setTurma(int $turma)
    {
        $this->turma = $turma;
    }

    public function setHoraPedido(string $horaPedido)
    {
        $this->horaPedido = $horaPedido;
    }

    public function setDataPedido(string $dataPedido)
    {
        $this->dataPedido = $dataPedido;
    }

    public function setEmpresa(string $empresa)
    {
        $this->empresa = $empresa;
    }

    public function setContactoPrincipal(string $contactoPrincipal)
    {
        $this->contactoPrincipal = $contactoPrincipal;
    }

    public function setContactoSecundario(string $contactoSecundario)
    {
        $this->contactoSecundario = $contactoSecundario;
    }
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    // public function buscarNomeEApelido(int $codigo): ?array
    // {
    //     $stmt = $this->conn->prepare("SELECT nome, apelido FROM formando WHERE codigo = ?");
    //     $stmt->bind_param("i", $codigo);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    // }

    public function buscarNomeEApelido(int $codigo): ?array
    {
        $stmt = $this->conn->prepare("SELECT nome, apelido FROM formando WHERE codigo = ?");
        if ($stmt === false) {
            // Handle prepare error
            return null;
        }
        $stmt->bind_param("i", $codigo);
        if (!$stmt->execute()) {
            // Handle execute error
            return null;
        }
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function salvar(string $nome, string $apelido): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO pedido_carta (codigo_formando, qualificacao, codigo_turma, data_do_pedido, hora_do_pedido, empresa, contactoPrincipal, contactoSecundario, email, nome, apelido) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "iiisssiisss",
            $this->codigoFormando,
            $this->qualificacao,
            $this->turma,
            $this->dataPedido,
            $this->horaPedido,
            $this->empresa,
            $this->contactoPrincipal,
            $this->contactoSecundario,
            $this->email,
            $nome,
            $apelido
        );

        return $stmt->execute();
    }
}
