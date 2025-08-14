<?php
require_once __DIR__ . '/../Conexao/conector.php';
require_once __DIR__ . '/../Model/Pessoa.php';
use Models\Pessoa;
class Formando extends Pessoa{
    private $conn;
    private int $codigo;
    public function __construct(
        string $nome,
        string $apelido,
        int $codigo,
        DateTime $dataDeNascimento,
        string $naturalidade,
        string $tipoDeDocumento,
        string $numeroDeDocumento,
        string $localEmitido,
        DateTime $dataDeEmissao,
        int $NUIT,
        int $Telefone,
        string $email
    ) {
        parent::__construct($nome, $apelido,  $dataDeNascimento,$naturalidade, $tipoDeDocumento, $numeroDeDocumento, $localEmitido, $dataDeEmissao, $NUIT, $Telefone, $email);
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->codigo = $codigo;
    }

    public function setCodigo(int $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function getCodigo(): int
    {
        return $this->codigo;
    }

    public function salvar(): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO formando (codigo, nome, apelido, dataDeNascimento, naturalidade, tipoDeDocumento, numeroDeDocumento, localEmitido, dataDeEmissao, NUIT, telefone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            error_log("Erro na preparação da query: " . $this->conn->error);
            return false;
        }

        // Converter DateTime para string no formato 'Y-m-d' para o banco de dados
        $dataNascimento = $this->getDataDeNascimento()->format('Y-m-d');
        $dataEmissao = $this->getDataDeEmissao()->format('Y-m-d');

        // $stmt->bind_param(
        //     "issssssssiis",
        //     $this->codigo,
        //     $this->getNome(),
        //     $this->getApelido(),
        //     $dataNascimento,
        //     $this->getNaturalidade(),
        //     $this->getTipoDeDocumento(),
        //     $this->getNumeroDeDocumento(),
        //     $this->getLocalEmitido(),
        //     $dataEmissao,
        //     $this->getNUIT(),
        //     $this->getTelefone(),
        //     $this->getEmail()
        // );

        $nome = $this->getNome();
        $apelido = $this->getApelido();
        $dataNascimento = $this->getDataDeNascimento()->format('Y-m-d');
        $naturalidade = $this->getNaturalidade();
        $tipoDeDocumento = $this->getTipoDeDocumento();
        $numeroDeDocumento = $this->getNumeroDeDocumento();
        $localEmitido = $this->getLocalEmitido();
        $dataEmissao = $this->getDataDeEmissao()->format('Y-m-d');
        $nuit = $this->getNUIT();
        $telefone = $this->getTelefone();
        $email = $this->getEmail();

        $stmt->bind_param(
            "issssssssiis",
            $this->codigo,
            $nome,
            $apelido,
            $dataNascimento,
            $naturalidade,
            $tipoDeDocumento,
            $numeroDeDocumento,
            $localEmitido,
            $dataEmissao,
            $nuit,
            $telefone,
            $email
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Erro na execução da query: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }
}

?>