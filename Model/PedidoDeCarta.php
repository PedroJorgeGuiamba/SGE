<?php

class PedidoDeCarta
{
    private $codigoFormando;
    private $numero;
    private $qualificacao;
    private $turma;
    private $dataPedido;
    private $horaPedido;
    private $empresa;
    private $contactoPrincipal;
    private $contactoSecundario;
    private $email;

    // Getters e setters
    public function setCodigoFormando(int $codigoFormando)
    {
        $this->codigoFormando = $codigoFormando;
    }

    public function setNumero($numero){$this->numero = $numero;}

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
    public function buscarNomeEApelido(int $codigo, mysqli $conn): ?array
    {
        $stmt = $conn->prepare("SELECT nome, apelido FROM formando WHERE codigo = ?");
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

    public function salvar(string $nome, string $apelido, mysqli $conn): bool
    {
        $stmt = $conn->prepare("INSERT INTO pedido_carta (numero, codigo_formando, qualificacao, codigo_turma, data_do_pedido, hora_do_pedido, empresa, contactoPrincipal, contactoSecundario, email, nome, apelido) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            // Erro na preparação da query
            return false;
        }

        $stmt->bind_param(
            "iiiissssssss",
            $this->numero,
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

    public function actualizar(string $nome, string $apelido, int $codigo_formando, int $qualificacao, int $codigo_turma, string $empresa, string $contactoPrincipal, string $contactoSecundario, string $email, int $id_pedido_carta, mysqli $conn): bool
    {
        $stmt = $conn->prepare("UPDATE pedido_carta SET
            nome = ?,
            apelido = ?,
            codigo_formando = ?,
            qualificacao = ?,
            codigo_turma = ?,
            empresa = ?,
            contactoPrincipal = ?,
            contactoSecundario = ?,
            email = ?
            WHERE id_pedido_carta = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("sssssssssi",
                $nome,
                $apelido,
                $codigo_formando,
                $qualificacao,
                $codigo_turma,
                $empresa,
                $contactoPrincipal,
                $contactoSecundario,
                $email,
                $id_pedido_carta
            );

        return $stmt->execute();
    }

    public function LastInsertId($conn){
        return $conn->insert_id;
    }
}
